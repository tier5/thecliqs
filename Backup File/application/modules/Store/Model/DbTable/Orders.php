<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Carts.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Orders extends Engine_Db_Table
{
  protected $_rowClass = "Store_Model_Order";

  protected $_serializedColumns = array('shipping_details');

  public function init(){
    $prefix = $this->getTablePrefix();

    $sql = "DELETE ".$this->info('name').".* FROM ".$this->info('name')."
        LEFT JOIN ".$prefix."store_carts ON (".$prefix."store_carts.order_id = ".$this->info('name').".order_id )
        WHERE ".$prefix."store_carts.cart_id IS NULL";

    $db = $this->getAdapter();
//    $db->query($sql);

    return parent::init();
  }

  public function getUkey()
  {
    do {
      $ukey = Engine_Api::_()->store()->generate_random_letters(10);
      $select = $this->select()->where("ukey = ?", $ukey);
    } while ( $this->fetchRow($select) );

    return $ukey;
  }

  public function getOrderByUkey( $ukey )
  {
    return $this->fetchRow( $this->select()->where('ukey = ?', $ukey) );
  }

  protected function _fetch(Zend_Db_Table_Select $select)
  {
    // Decrypt each column
    $rows = parent::_fetch($select);
    foreach( $rows as $index => $data ) {
      $rows[$index] = $this->_unserializeColumns($data);
    }

    return $rows;
  }
}