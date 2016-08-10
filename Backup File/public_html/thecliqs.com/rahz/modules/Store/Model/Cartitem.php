<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Cartitem.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Cartitem extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_parent_type = 'store_cart';

  protected $_type = 'store_cartitem';

  /**
   * @return Core_Model_Item_Abstract|null|Store_Model_Product
   */
  public function getProduct()
  {
    /**
     * @var $product Store_Model_Product
     */
    if (null != ($product = Engine_Api::_()->getItem('store_product', $this->product_id))) {
      return $product;
    }

    return null;
  }

  /**
   * @return Core_Model_Item_Abstract|null|Store_Model_Cart
   */
  public function getCart()
  {
    /**
     * @var $cart Store_Model_Cart
     */
    $cart = Engine_Api::_()->getItem('store_cart', $this->cart_id);

    return $cart;
  }

  /**
   * he@todo Works only for Store_Model_Product;
   *
   * @return boolean
   */
  public function isItemDigital()
  {
    /**
     * @var $table Store_Model_DbTable_Products
     */
    $table = Engine_Api::_()->getDbTable('products', 'store');

    return $table->select()
      ->from($table, new Zend_Db_Expr('TRUE'))
      ->where('product_id = ?', $this->product_id)
      ->where('type = ?', 'digital')
      ->query()
      ->fetchColumn();
  }

  public function isItemQuantityEnough()
  {
    if( $this->isItemDigital() ) return true;

    /**
     * @var $table Store_Model_DbTable_Products
     */
    $table = Engine_Api::_()->getDbTable('products', 'store');

    return $table->select()
      ->from($table, new Zend_Db_Expr('TRUE'))
      ->where('product_id = ?', $this->product_id)
      ->where("quantity >= ?", $this->qty)
      ->query()
      ->fetchColumn();
  }

  public function isUserLocationSupported()
  {
    if( $this->isItemDigital() ) return true;

    /**
     * @var $table Store_Model_DbTable_Cartitems
     */
    $table  = $this->getTable();
    $prefix = $table->getTablePrefix();
    return $table
      ->select()
      ->setIntegrityCheck(false)
      ->from(array('i'=> $table->info('name')), "IF((s.productship_id && p.page_id=ls.page_id),TRUE,FALSE)")
      ->joinInner(array('c' => $prefix . 'store_carts'), 'c.cart_id = i.cart_id', array())
      ->joinLeft(array('p' => $prefix . 'store_products'), 'i.product_id = p.product_id', array())
      ->joinLeft(array('d' => $prefix . 'store_details'), 'd.user_id = c.user_id', array())
      ->joinLeft(array('s'=> $prefix . 'store_productships'),
      's.product_id = i.product_id AND ( (s.location_id = d.location_id_2) || (s.location_id = d.location_id_1 && d.location_id_2 = 0))', array())
      ->joinLeft(array('l' => $prefix . 'store_locations'), 'l.location_id = s.location_id', array())
      ->joinLeft(array('ls' => $prefix . 'store_locationships'), 'l.location_id = ls.location_id AND p.page_id=ls.page_id', array())
      ->where('i.cartitem_id = ?', $this->getIdentity())
      ->query()
      ->fetchColumn();
  }

  public function isCheckable($via_credits = false)
  {
    if ($this->isItemDigital()) return true;

    if (!$this->isItemQuantityEnough()) return false;

    if (!$this->isUserLocationSupported()) return false;

    if ($via_credits && !$this->isStoreCredit()) return false;

    return true;
  }

  public function getPrice()
  {
    if (null == ($product = $this->getProduct())) {
      return 0;
    }

    return $product->getPrice();
  }

  public function getShippingPrice($location_id)
  {
    if(null != ($product = $this->getProduct())){
      return $product->getShippingPrice($location_id);
    }

    return 0;
  }

  public function getTax()
  {
    if (null == ($product = $this->getProduct())) {
      return 0;
    }

    return $product->getTax();
  }

  public function isStoreCredit()
  {
    if (null == ($product = $this->getProduct())) {
      return false;
    }

    return $product->isStoreCredit();
  }
}