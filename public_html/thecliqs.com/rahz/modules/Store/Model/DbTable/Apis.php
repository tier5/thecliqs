<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Apis.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Apis extends Engine_Db_Table
{
  protected $_rowClass = 'Store_Model_Api';

  protected $_serializedColumns = array('config');

  protected $_cryptedColumns = array('config');

  static private $_cryptKey;

  public function getEnabledState($page_id)
  {
    return ($this->getEnabledGatewayCount($page_id) > 0) ? true : false;
  }

  /**
   * @param int $page_id
   *
   * @return int
   */
  public function getEnabledGatewayCount($page_id)
  {
    return (int)$this->select()
      ->from($this, new Zend_Db_Expr('COUNT(*)'))
      ->where('enabled = ?', 1)
      ->where('page_id = ?', $page_id)
      ->query()
      ->fetchColumn();
  }

  /**
   * @param $page_id
   *
   * @return Engine_Db_Table_Rowset
   */
  public function getEnabledGateways($page_id)
  {
    return
      $this->fetchAll($this
        ->select()
        ->where('enabled = ?', 1)
        ->where('page_id = ?', $page_id)
      );
  }

  /**
   * @param int $page_id
   * @param int $gateway_id
   * @return Store_Model_Api
   */
  public function getGateway($page_id, $gateway_id)
  {
    return $this->fetchRow($this
      ->select()
      ->where('page_id = ?', $page_id)
      ->where('gateway_id = ?', $gateway_id)
    );
  }

  /**
   * @param int $page_id
   * @param int $gateway_id
   *
   * @return Boolean
   */
  public function isGatewayEnabled($page_id, $gateway_id)
  {
    return $this->select()
      ->from($this, new Zend_Db_Expr('TRUE'))
      ->where('gateway_id = ?', $gateway_id)
      ->where('page_id = ?', $page_id)
      ->where('enabled = ?', 1)
      ->query()
      ->fetchColumn();
  }

  // Inline encryption/decryption

  public function insert(array $data)
  {
    // Serialize
    $data = $this->_serializeColumns($data);

    // Encrypt each column
    foreach ($this->_cryptedColumns as $col) {
      if (!empty($data[$col])) {
        $data[$col] = self::_encrypt($data[$col]);
      }
    }

    return parent::insert($data);
  }


  public function update(array $data, $where)
  {
    // Serialize
    $data = $this->_serializeColumns($data);

    // Encrypt each column
    foreach ($this->_cryptedColumns as $col) {
      if (!empty($data[$col])) {
        $data[$col] = self::_encrypt($data[$col]);
      }
    }

    return parent::update($data, $where);
  }

  protected function _fetch(Zend_Db_Table_Select $select)
  {
    $rows = parent::_fetch($select);

    foreach ($rows as $index => $data) {
      // Decrypt each column
      foreach ($this->_cryptedColumns as $col) {
        if (!empty($rows[$index][$col])) {
          $rows[$index][$col] = self::_decrypt($rows[$index][$col]);
        }
      }
      // Unserialize
      $rows[$index] = $this->_unserializeColumns($rows[$index]);
    }

    return $rows;
  }


  // Crypt Utility

  static private function _encrypt($data)
  {
    if (!extension_loaded('mcrypt')) {
      return $data;
    }

    $key       = self::_getCryptKey();
    $cryptData = mcrypt_encrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);

    return $cryptData;
  }

  static private function _decrypt($data)
  {
    if (!extension_loaded('mcrypt')) {
      return $data;
    }

    $key       = self::_getCryptKey();
    $cryptData = mcrypt_decrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);
    $cryptData = rtrim($cryptData, "\0");

    return $cryptData;
  }

  static private function _getCryptKey()
  {
    if (null === self::$_cryptKey) {
      $key = Engine_Api::_()->getApi('settings', 'core')->core_secret
        . '^'
        . Engine_Api::_()->getApi('settings', 'core')->page_secret;
      self::$_cryptKey = substr(md5($key, true), 0, 8);
    }

    return self::$_cryptKey;
  }

  public function getApi($page_id, $gateway_id)
  {
    if (!$page_id) {
      $table = Engine_Api::_()->getDbTable('gateways', 'store');
      return $table->fetchRow(array('title = ?' => 'PayPal'));
    }
    $select = $this->select()
      ->where('page_id=?', $page_id)
      ->where('gateway_id=?', $gateway_id);

    return $this->fetchRow($select);
  }
}