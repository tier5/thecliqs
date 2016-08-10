<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Orderitem.php 3/27/12 8:08 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Orderitem extends Core_Model_Item_Abstract
{
  public $title;

  protected $_parent_type = 'store_order';

  protected $_searchTriggers = false;

  protected $_parent_id;

  public function init()
  {
    if (isset($this->name)) {
      $this->title = $this->name;
    }

    if (isset($this->order_id)) {
      $this->_parent_id = $this->order_id;
    }
  }

  /**
   * @return Store_Model_Product
   */
  public function getItem()
  {
    return Engine_Api::_()->getItem($this->item_type, $this->item_id);
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
      ->where('product_id = ?', $this->item_id)
      ->where('type = ?', 'digital')
      ->query()
      ->fetchColumn();
  }

  public function isDownloadable()
  {
    /**
     * @var $product Store_Model_Product
     */
    if (
      !$this->isItemDigital() ||
      $this->status != 'completed' ||
      null == ($product = $this->getItem())
    ) return false;

    $downloadCount = (int)Engine_Api::_()->getDbTable('settings', 'core')->getSetting('store.download.count', 10);

    if ($downloadCount > 0 && ((int)($downloadCount - $this->download_count) <= 0)) return false;

    return true;
  }

  public function onPaymentSuccess()
  {
    if (!in_array($this->status, array('initial', 'processing'))) {
      return $this->status;
    }

    // Change status
    if ($this->isItemDigital()) {
      $this->status = 'completed';
    } else {
      if ($this->status == 'initial') {
        $this->decreaseQuantity();
      }

      $this->status = 'shipping';
    }

    $this->save();

    $storeApi = Engine_Api::_()->store();
    $mode = $storeApi->getPaymentMode();

    if ($mode == 'client_site_store') {
      /**
       * @var $pageApi Store_Api_Page
       * @var $balance Store_Model_Balance
       */
      $pageApi = Engine_Api::_()->getApi('page', 'store');

      if ($pageApi->isStore($this->page_id)) {
        $balance = $pageApi->getBalance($this->page_id);
        $amt     = (double)($this->total_amt - $this->commission_amt) * $this->qty - $this->getGatewayFee();
        $balance->increase($amt);
      }
    }

    return $this->status;
  }

  public function onPaymentPending()
  {
    if ($this->status != 'initial')
      return $this->status;

    $this->decreaseQuantity();

    $this->status = 'processing';
    $this->save();

    return $this->status;
  }

  public function onPaymentFailure()
  {
    if (!in_array($this->status, 'processing', 'pending', 'shipping'))
      return $this->status;

    // he@todo Should we do this?
    return $this->cancel();
  }

  public function onPaymentRefund()
  {
    if (!in_array($this->status, 'processing', 'pending', 'shipping', 'completed'))

      return $this->onCancel();
  }

  public function cancel()
  {
    if (!in_array($this->status, array('shipping', 'processing')))
      return null;

    return $this->onCancel();
  }

  public function onCancel()
  {
    /**
     * @var $pageApi Store_Api_Page
     */
    $pageApi = Engine_Api::_()->getApi('page', 'store');

    if ($pageApi->isStore($this->page_id) && $this->gateway_transaction_id == null) {
      $balance = $pageApi->getBalance($this->page_id);
      $amt     = (double)($this->total_amt - $this->commission_amt) * $this->qty - $this->getGatewayFee();

      if ($balance->current_amt < $amt) {
        return $this->status;
      } else {
        $balance->decrease($amt);

        /**
         * @var $store_api Store_Api_Core
         * @var $credit_api Credit_Api_Core
         */
        $store_api = Engine_Api::_()->store();
        $credit_api = Engine_Api::_()->credit();
        if ($store_api->isCreditEnabled() && $this->item_type == 'store_product') {
          $credit_api->cancelOrder($this->getOwner(), Engine_Api::_()->store()->getCredits($this->total_amt));
        }
      }
    }

    $this->increaseQuantity();

    $this->status = 'cancelled';
    $this->save();

    return $this->status;
  }

  public function onComplete()
  {
    if ($this->isItemDigital()) {
      $this->status = 'completed';
    } else {
      $this->status = 'delivered';
    }

    $this->save();
    $this->checkOrderItems();
  }

  public function increaseQuantity()
  {
    if ($this->isItemDigital() || $this->item_type != 'store_product' || null == ($product = $this->getItem()))
      return false;

    $product->quantity += $this->qty;
    return $product->save();
  }

  public function decreaseQuantity()
  {
    if ($this->isItemDigital() || $this->item_type != 'store_product' || null == ($product = $this->getItem()))
      return false;

    $product->quantity -= $this->qty;
    return $product->save();
  }

  /**
   * @return null|Page_Model_Page
   */
  public function getStore()
  {
    if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) {
      return null;
    }

    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getDbtable('pages', 'page')->findRow($this->page_id);

    return $page;
  }

  public function getProduct()
  {
    return Engine_Api::_()->getItem($this->item_type, $this->item_id);
  }

  public function getOwner()
  {
    $parent = $this->getParent();

    return $parent->getOwner();
  }

  public function save()
  {
    $this->update_date = new Zend_Db_Expr('NOW()');
    return parent::save();
  }

  public function getGatewayFee()
  {
    /**
     * @var $table Store_Model_DbTable_Transactions
     */
    $table = Engine_Api::_()->getDbTable('transactions', 'store');
    $select = $table->select()
      ->where('order_id = ?', $this->order_id)
    ;

    if ($this->gateway_transaction_id) {
      $select
        ->where('gateway_transaction_id = ?', $this->gateway_transaction_id)
      ;
    }

    $transaction = $table->fetchRow($select);
    if (!$transaction) {
      return 0;
    }
    return (double) $this->total_amt * $this->qty * $transaction->gateway_fee / $transaction->amt;
  }

  public function checkOrderItems()
  {
    $order = $this->getParent();
    $orderItems = $order->getItems();
    foreach ($orderItems as $item) {
      if ($item->status == 'shipping') {
        return 0;
      }
    }
    $order->status = 'delivered';
    $order->save();
  }
}