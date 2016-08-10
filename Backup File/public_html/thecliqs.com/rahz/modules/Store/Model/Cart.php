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

class Store_Model_Cart extends Store_Model_Item_Abstract
{
  protected $_owner_type = 'user';

  protected $_child_type = 'store_cartitem';

  protected $_type = 'store_cart';

  protected $_searchTriggers = false;

  /**
   * @var $_child_table Store_Model_DbTable_Cartitems
   */
  protected $_child_table;

  protected $_statusChanged;

  /**
   * @var $_details_table Store_Model_DbTable_Details
   */
  protected $_details_table;

  public function init()
  {
    $this->_child_table = Engine_Api::_()->getItemTable($this->_child_type);

  }

  public function getChildTable()
  {
    return $this->_child_table;
  }


  public function getPrice()
  {
    if (!isset($this->_price)) {
      $table  = $this->getChildTable();
      $prefix = $table->getTablePrefix();

      $price = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('i'=> $table->info('name')), new Zend_Db_Expr('SUM(p.price*i.qty)'))
        ->joinInner(array('p'=> $prefix . 'store_products'), 'p.product_id = i.product_id', array())
        ->where('cart_id = ?', $this->cart_id)
        ->query()
        ->fetchColumn();

      $this->setPrice($price);
    }

    return parent::getPrice();
  }

  public function hasItem()
  {
    $table = $this->getChildTable();

    /**
     * @var $item Store_Model_Cartitem
     */
    if (null == $table->fetchRow(array('cart_id = ?'=> $this->cart_id))) return false;

    return true;
  }

  /**
   * @return Engine_Db_Table_Rowset
   **/
  public function getItems()
  {
    /**
     * @var $table Store_Model_DbTable_Cartitems
     */
    $table  = $this->getChildTable();
    $select = $table->select();

    if (null != ($user = Engine_Api::_()->getItem('user', $this->user_id))) {
      $detailsTable = Engine_Api::_()->getDbTable('details', 'store');

      if (null == ($location_id = $detailsTable->getDetail($user, 'state'))) {
        $location_id =(int) $detailsTable->getDetail($user, 'country');
      }

      if (isset($location_id)) {
        $select
          ->setIntegrityCheck(false)
          ->from(array('c' => $table->info('name')))
          ->joinInner(array('p'=> $table->getTablePrefix() . 'store_products'), 'p.product_id = c.product_id',
            array("IF(p.type='digital',1,0) AS digital", "IF(p.quantity>=c.qty,1,0) AS quantity_enough")
          )
          ->joinLeft(array('ps'=> $table->getTablePrefix() . 'store_productships'), 'ps.product_id = c.product_id AND ps.location_id = ' . $location_id, "IF((ps.productship_id>0 && ls.page_id = p.page_id),1,0) AS supported")
          ->joinLeft(array('l' => $table->getTablePrefix() . 'store_locations'), 'l.location_id = ps.location_id', array())
          ->joinLeft(array('ls'=> $table->getTablePrefix() . 'store_locationships'), 'l.location_id = ls.location_id and ls.page_id = p.page_id', array())
          ->order('digital DESC')
          ->order('quantity_enough DESC')
          ->order('supported DESC');
      }
    }

    $select->where('cart_id = ?', $this->cart_id);

    return $table->fetchAll($select);
  }

  /**
   * @return Integer
   **/
  public function getItemCount()
  {
    $table = $this->getChildTable();
    return $table->select()
      ->from($table, new Zend_Db_Expr('COUNT(*)'))
      ->where('cart_id = ?', $this->cart_id)
      ->query()
      ->fetchColumn();
  }

  /**
   * @return Page_Model_Page
   **/
  public function getStore()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) return null;

    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  /**
   * @return String
   **/
  public function getStoreTitle()
  {
    if ($this->page_id && (null != ($store = $this->getStore()))) {
      return $store->getTitle();
    }

    $translator = Zend_Registry::get('Zend_Translate');
    return $translator->translate('ADMIN_STORE_TITLE');
  }

  /**
   * @return String
   **/
  public function getStoreDescription()
  {
    if ($this->page_id && (null != ($store = $this->getStore()))) {
      return $store->getDescription();
    }

    return Zend_Registry::get('Zend_Translate')->translate('ADMIN_STORE_DESCRIPTION');
  }

  public function setActive($flag = true, $status = 'success')
  {
    $flag = (bool)$flag;

    if (!$flag) {

      /**
       * @var $item Store_Model_Cartitem
       */
      $ids = array();
      foreach ($this->getPurchasableItems() as $item) {
        $ids[] = $item->getIdentity();
      }
      if (count($ids)) {
        $ids_str = implode(', ', $ids);
        $this->getChildTable()->delete("cartitem_id IN($ids_str)");
      }

      if (0 != (int)($new_id = $this->getTable()->insert(array('user_id'=> $this->user_id)))) {
        $this->getChildTable()->update(array('cart_id' => $new_id), array('cart_id =?'=> $this->cart_id));
      }

      //hehe@todo Should we just update?
//      $this->getChildTable()->update(array('active' => 0), array('cart_id = ?'=>$this->cart_id));
    }

    parent::setActive($flag, $status);
  }

  public function getPurchasabilitySelect()
  {
    /**
     * @var $table Store_Model_DbTable_Cartitems
     */
    $table  = $this->getChildTable();
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('i'=> $table->info('name')))
      ->joinInner(array('p'=> $prefix . 'store_products'), 'p.product_id = i.product_id', array('via_credits'))
      ->where('p.quantity >= i.qty');

    $location_id = (int)$this->getShippingLocationId();
    $select
      ->joinLeft(array('l' => $prefix . 'store_productships'), 'l.product_id = i.product_id AND l.location_id = ' . $location_id, array())
      ->where('l.productship_id');

    $select
      ->orWhere('p.type=?', 'digital');

    $where = implode(' ', $select->getPart('where'));
    $select->reset('where');
    $select
      ->where($where)
      ->where('i.cart_id = ?', $this->cart_id);
    return $select;
  }

  public function getPurchasableItems()
  {
    /**
     * @var $table Store_Model_DbTable_Cartitems
     */
    $table  = $this->getChildTable();
    $select = $this->getPurchasabilitySelect();

    return $table->fetchAll($select);
  }

  public function getPurchasableItemsPrice()
  {
    $amt = 0;
    /**
     * @var $item Store_Model_Cartitem
     */
    foreach ($this->getPurchasableItems() as $item) {
      $amt += $item->getPrice() * $item->qty;
    }

    return (double)($amt === null) ? 0 : $amt;
  }

  public function getPurchasableItemsCount()
  {
    return $this->getPurchasableItems()->count();
  }

  public function getShippingPrice()
  {
    if (0 == ($location_id = (int)$this->getShippingLocationId())) {
      return 0;
    }

    $amt = 0;
    /**
     * @var $item Store_Model_Cartitem
     */
    foreach ($this->getPurchasableItems() as $item) {
      $amt += $item->getShippingPrice($location_id) * $item->qty;
    }

    return (double)($amt === null) ? 0 : $amt;
  }

  public function getTaxesPrice()
  {
    $amt = 0;
    /**
     * @var $item Store_Model_Cartitem
     */
    foreach ($this->getPurchasableItems() as $item) {
      $amt += $item->getTax() * $item->qty;
    }

    return (double)($amt === null) ? 0 : $amt;
  }

//tj@todo Realize this method!
  public function getCommissionAmt()
  {
    if (0 == ($location_id = $this->getShippingLocationId())) {
      return 0;
    }

    /**
     * @var $cartTbl     Store_Model_DbTable_Cartitems
     * @var $productsTbl Store_Model_DbTable_Products
     * @var $taxesTbl    Store_Model_DbTable_Taxes
     * @var $shipsTbl    Store_Model_DbTable_Productships
     */

    $cartTbl     = $this->getChildTable();
    $productsTbl = Engine_Api::_()->getDbTable('products', 'store');
    $taxesTbl    = Engine_Api::_()->getDbTable('taxes', 'store');
    $shipsTbl    = Engine_Api::_()->getDbTable('productships', 'store');

    $select = $cartTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $cartTbl->info('name')), 'SUM(p.price*t.percent/100)')
      ->joinInner(array('p' => $productsTbl->info('name')), 'p.product_id = c.product_id', array())
      ->joinLeft(array('t' => $taxesTbl->info('name')), 't.tax_id = p.tax_id', array())
      ->joinLeft(array('s'=> $shipsTbl->info('name')), 's.product_id = c.product_id AND s.location_id = ' . $location_id, array())
      ->where('s.productship_id || p.type = ?', 'digital')
      ->where('c.cart_id = ?', $this->cart_id);

    $price = $select->query()->fetchColumn();
    return ($price === null) ? 0 : $price;
  }

  public function getShippingDetails()
  {
    if (null == ($owner = $this->getOwner())) {
      return null;
    }

    $details = Engine_Api::_()->getDbTable('details', 'store')->getDetails($owner);

    if (null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($details['location_id_1']))) {
      $details['country'] = $country->location;
    }
    if (null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($details['location_id_2']))) {
      $details['state'] = $state->location;
    }

    return $details;
  }

  public function getShippingLocationId()
  {
    if (null == ($user = $this->getOwner())) {
      return 0;
    }

    if ($this->_details_table == null) {
      $this->_details_table = Engine_Api::_()->getDbTable('details', 'store');
    }

    if (null == ($location_id = $this->_details_table->getDetail($user, 'state'))) {
      $location_id = $this->_details_table->getDetail($user, 'country');
    }

    if (!isset($location_id)) {
      return 0;
    }

    return $location_id;
  }

  public function hasProduct($product_id)
  {
    return (boolean)$this->getChildTable()
      ->select()
      ->where('cart_id = ?', $this->getIdentity())
      ->where('product_id = ?', $product_id)
      ->query()
      ->fetchColumn();
  }

  public function getRowByProduct($product_id)
  {
    $select = $this->getChildTable()
      ->select()
      ->where('cart_id = ?', $this->getIdentity())
      ->where('product_id = ?', $product_id);
    return $this->getChildTable()->fetchRow($select);
  }
}