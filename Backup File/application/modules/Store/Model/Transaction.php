<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Cart.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Transaction extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getStoreId()
  {
    /**
     * @var $table Store_Model_DbTable_Orderitems
     */

    $table = Engine_Api::_()->getDbTable('orderitems', 'store');
    $select = $table->select()
      ->where('gateway_transaction_id = ?', $this->gateway_transaction_id)
    ;

    $item = $table->fetchRow($select);
    if ($item) {
      return $item->page_id;
    }

    return null;
  }
}