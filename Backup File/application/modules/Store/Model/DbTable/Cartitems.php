<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Cartitems.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Cartitems extends Engine_Db_Table
{
  protected $_rowClass = "Store_Model_Cartitem";

  protected $_serializedColumns = array('params');

  protected $_user;

  public function init()
  {
    $this->_user = Engine_Api::_()->user()->getViewer();

    $prefix = $this->getTablePrefix();
    $sql    = "DELETE " . $this->info('name') . ".* FROM " . $this->info('name') . "
        LEFT JOIN " . $prefix . "store_carts ON (" . $prefix . "store_carts.cart_id = " . $this->info('name') . ".cart_id )
        LEFT JOIN " . $prefix . "store_products ON (" . $prefix . "store_products.product_id = " . $this->info('name') . ".product_id )
        WHERE " . $prefix . "store_carts.cart_id IS NULL OR " . $prefix . "store_products.product_id IS NULL";

    $db = $this->getAdapter();
    $db->query($sql);

    return parent::init();
  }

  public function setUser($user)
  {
    if ($user instanceof User_Model_User) {
      $this->_user = $user;
      return $this;
    }


    if (is_integer($user) && null != ($user = Engine_Api::_()->getItem('user', $user))) {
      $this->_user = $user;
      return $this;
    }

    return $this;
  }

  public function getUser()
  {
    return $this->_user;
  }


  public function getSelect()
  {
    $prefix = $this->getTablePrefix();
    /**
     * @var $table Store_Model_DbTable_Products
     */
    $table = Engine_Api::_()->getItemTable('store_product');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=> $prefix . 'store_carts'), array())
      ->joinInner(array('i'=> $this->info('name')), 'i.cart_id = c.cart_id')
      ->joinInner($prefix . 'store_products', $prefix . 'store_products.product_id=i.product_id', array());

    $select = $table->setStoreIntegrity($select);

    $select
      ->where('c.active = ?', 1)
      ->where('c.user_id = ?', $this->_user->getIdentity());

    return $select;
  }

  public function getPaginator($page_id = null)
  {
    if (null == ($select = $this->getSelect($page_id))) return;

    return Zend_Paginator::factory($select);
  }

  protected function _fetch(Zend_Db_Table_Select $select)
  {
    // Decrypt each column
    $rows = parent::_fetch($select);
    foreach ($rows as $index => $data) {
      $rows[$index] = $this->_unserializeColumns($data);
    }

    return $rows;
  }
}