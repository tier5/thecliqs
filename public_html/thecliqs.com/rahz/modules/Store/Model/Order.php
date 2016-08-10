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

class Store_Model_Order extends Store_Model_Item_Abstract
{
  protected $_type = 'store_order';

  protected $_searchTriggers = false;

  /**
   * @var $_user User_Model_User
   */
  protected $_user;

  protected $_statusChanged;

  /**
   * @return Store_Model_Cart|Store_Model_Request|Store_Model_Item_Abstract
   */
  public function getItem()
  {
    return Engine_Api::_()->getItem($this->item_type, $this->item_id);
  }

  /**
   * @return null|User_Model_User
   */
  public function getUser()
  {
    if (empty($this->user_id)) {
      return null;
    }
    if (null === $this->_user) {
      $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
    }
    return $this->_user;
  }

  /**
   * @return int
   */
  public function getItemsCount()
  {
    /**
     * @var $table Store_Model_DbTable_Orderitems
     */
    $table = Engine_Api::_()->getDbTable('orderitems', 'store');
    return (int)$table->select()
      ->from($table, new Zend_Db_Expr('COUNT(orderitem_id)'))
      ->where('order_id = ?', $this->order_id)
      ->query()
      ->fetchColumn();
  }

  /**
   * @return bool
   */
  public function hasItems()
  {
    return ($this->getItemsCount() > 0) ? true : false;
  }

  /**
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getItems($page_id = null)
  {
    /**
     * @var $table Store_Model_DbTable_Orderitems
     */
    $table  = Engine_Api::_()->getDbTable('orderitems', 'store');
    $select = $table->select()
      ->where('order_id = ?', $this->order_id)
    ;
    if ($page_id !== null) {
      $select
        ->where('page_id = ?', $page_id)
      ;
    }

    return $table->fetchAll($select);
  }


  //Used for informing customer about this order
  public function getDetails()
  {
    /**
     * @var $translate Zend_Translate
     * @var $item      Store_Model_Orderitem
     */
    $translate = Zend_Registry::get('Zend_Translate');
    $details   = "";

    if ($this->item_type == 'store_cart') {
      $checkShippable = false;
      foreach ($this->getItems() as $item) {

        if (!$item->isItemDigital()) $checkShippable = true;

        $product = "
    " . $translate->_('Product') . ": " . $item->name . "
    " . $translate->_('Price') . ": " . $item->item_amt . "
    " . $translate->_('STORE_Quantity') . ": " . $item->qty;

        if (count($item->params) > 0) {
          $product .= "
    " . $translate->_('Parameters') . ": " . Engine_Api::_()->store()->params_string($item->params);
        }

        $details .= $product . "
    ";
      }
      $details .= "
    " . $translate->_('Tax Amount') . ": " . $this->tax_amt;

      if ($checkShippable) {
        $details .= "

    " . $translate->_("Shipping Amount") . ": " . $this->shipping_amt . "
    " . $translate->_("Shipping Details") . ":
        " . $translate->_("Customer") . " - " . $this->shipping_details['first_name'] . ' ' . $this->shipping_details['last_name'] . "
        " . $translate->_("Email") . " - " . $this->shipping_details['email'] . "
        " . $translate->_("Phone") . " - " . $this->shipping_details['phone_extension'] . "-" . $this->shipping_details['phone'] . "
        " . $translate->_("Country") . " - " . $this->shipping_details['country'] . "
        " . $translate->_("State") . " - " . $this->shipping_details['state'] . "
        " . $translate->_("Zip") . " - " . $this->shipping_details['zip'] . "
        " . $translate->_("City") . " - " . $this->shipping_details['city'] . "
        " . $translate->_("STORE_Address Line") . " - " . $this->shipping_details['address_line_1'] . "
        " . $translate->_("STORE_Address Line 2") . " - " . $this->shipping_details['address_line_2'];
      }

      $details .= "

    " . $translate->_("Total Amount") . ": " . $this->total_amt . "
    " . $translate->_("Currency") . ": " . strtoupper($this->currency);
    } elseif (null != ($item = $this->getItem()) && method_exists($item, 'getDetails')) {
      $details = $item->getDetails();
    }

    return $details;
  }

  public function getStores()
  {
    $stores = array();
    $stores[0] = Zend_Registry::get('Zend_Translate')->_('STORE_Admin\'s Store');
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return $stores;
    }

    if (!$this->getItems(0)->count()) {
      unset($stores[0]);
    }

    /**
     * @var $orderItemsTbl Store_Model_DbTable_Orderitems
     * @var $pagesTbl Page_Model_DbTable_Pages
     */
    $orderItemsTbl = Engine_Api::_()->getDbTable('orderitems', 'store');
    $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $pagesTbl->select()
      ->from(array('p' => $pagesTbl->info('name')))
      ->setIntegrityCheck(false)
      ->joinInner(array('oi' => $orderItemsTbl->info('name')), 'p.page_id=oi.page_id', array())
      ->where('oi.order_id = ?', $this->order_id)
    ;

    $pages = $pagesTbl->fetchAll($select);
    foreach ($pages as $page) {
      $stores[$page->getIdentity()] = $page->getTitle();
    }
    return $stores;
  }

  public function getStoreInfo($page_id = null, $column)
  {
    /**
     * @var $orderItemsTbl Store_Model_DbTable_Orderitems
     */
    $orderItemsTbl = Engine_Api::_()->getDbTable('orderitems', 'store');
    $select = $orderItemsTbl->select()
      ->from($orderItemsTbl, array(new Zend_Db_Expr('SUM('.$column.')')))
      ->where('order_id = ?', $this->order_id)
    ;

    if ($page_id !== null) {
      $select
        ->where('page_id = ?', $page_id);
    }

    return $select
      ->query()
      ->fetchColumn()
    ;
  }

  /**
   * @return Store_Model_Order
   */
  public function onPaymentSuccess()
  {
    $this->_statusChanged = false;

    $status = 'completed';

    if (null != ($item = $this->getItem())) {
      $item->setActive(false, 'success');
    }

    /**
     * @var $item Store_Model_Orderitem
     */
    foreach ($this->getItems() as $item) {
      if ($item->onPaymentSuccess() == 'shipping') {
        $status = 'shipping';
      }
    }

    // Change status
    if (in_array($this->status, array('initial', 'processing'))) {
      $this->status       = $status;
      $this->payment_date = new Zend_Db_Expr('NOW()');
      $this->save();

      $this->_statusChanged = true;
    }

    if ($this->offer_id && in_array($status, array('shipping', 'completed'))) {
      $isOffersEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers');

      /**
       * @var $offer Offers_Model_Offer
       */

      if ($isOffersEnabled && ($offer = Engine_Api::_()->getItem('offer', $this->offer_id))) {
        $subscription = $offer->getSubscription($this->user_id);
        $subscription->onUsed();
      }
    }

    return $this;
  }

  public function onPaymentPending()
  {
    $this->_statusChanged = false;

    if (null != ($item = $this->getItem())) {
      $item->setActive(false, 'pending');
    }

    /**
     * @var $item Store_Model_Orderitem
     */
    foreach ($this->getItems() as $item) {
      $item->onPaymentPending();
    }

    // Change status
    if ($this->status == 'initial') {
      $this->status       = 'processing';
      $this->payment_date = new Zend_Db_Expr('NOW()');
      $this->save();

      $this->_statusChanged = true;
    }

    return $this;
  }

  public function onPaymentFailure()
  {
    return $this;

    //he@todo Should we do this?
    return $this->cancel();
  }

  public function onRefund()
  {
    return $this->cancel();
  }

  /**
   * @return Boolean
   **/
  public function didStatusChange()
  {
    return $this->_statusChanged;
  }

  public function cancel()
  {
    // Cancel this row
    return $this->onCancel();
  }

  public function onCancel()
  {
    $this->_statusChanged = false;

    /**
     * @var $item Store_Model_Orderitem
     */
    foreach ($this->getItems() as $item) {
      $item->onPaymentFailure();
    }

    // Change status
    if (in_array($this->status, array('initial', 'processing'))) {
      $this->status         = 'cancelled';
      $this->_statusChanged = true;
      $this->save();
    }

    return $this;
  }

  public function save()
  {
    if ($this->ukey == null) {
      $this->ukey = $this->getTable()->getUkey();
    }

    return parent::save();
  }

  public function updateUkey()
  {
    $this->ukey = $this->getTable()->getUkey();
    $this->save();

    return $this->ukey;
  }

  public function getUkey()
  {
    if (isset($this->ukey)) return $this->ukey;

    return $this->getIdentity();
  }
}