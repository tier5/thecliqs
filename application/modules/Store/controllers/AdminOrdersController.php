<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminOrdersController.php 5/17/12 6:10 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminOrdersController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_orders');
    $this->view->api = Engine_Api::_()->store();
  }

  public function indexAction()
  {
    // Test curl support
    if (!function_exists('curl_version') ||
      !($info = curl_version())
    ) {
      $this->view->error = $this->view->translate('The PHP extension cURL ' .
        'does not appear to be installed, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }
    // Test curl ssl support
    elseif (!($info['features'] & CURL_VERSION_SSL) ||
      !in_array('https', $info['protocols'])
    ) {
      $this->view->error = $this->view->translate('The installed version of ' .
        'the cURL PHP extension does not support HTTPS, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }
    // Check for enabled payment gateways
    elseif (Engine_Api::_()->getDbtable('gateways', 'store')->getEnabledGatewayCount() <= 0) {
      $this->view->error = $this->view->translate('There are currently no ' .
        'enabled payment gateways. You must %1$sadd one%2$s before this ' .
        'page is available.', '<a href="' .
        $this->view->escape($this->view->url(array('module' => 'store',
          'controller' => 'settings',
          'action' => 'gateway'), 'admin_default')) .
        '">', '</a>');
    }

    // Make form
    $this->view->formFilter = $formFilter = new Store_Form_Admin_Transaction_OrderFilter();

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'orderitem_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order = $filterValues['order'];
    $this->view->direction = $filterValues['direction'];

    /**
     * Initialize select
     *
     * @var $table Store_Model_DbTable_Orderitems
     */
    $table = Engine_Api::_()->getDbtable('orderitems', 'store');
    $prefix = $table->getTablePrefix();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('oi' => $table->info('name')))
      ->joinInner(array('o' => $prefix . 'store_orders'), 'o.order_id=oi.order_id', array('o.ukey', 'o.user_id', 'o.payment_date'))
//      ->joinInner(array('t'=> $prefix . 'store_transactions'), 'transaction_idorder_id=oi.order_id', array('t.timestamp',
//      'payment_state'=> 't.state'))
      ->where('oi.item_type=?', 'store_product')
      ->where("oi.status IN('processing', 'completed', 'shipping', 'delivered')");

    // Add filter values
    if (!empty($filterValues['ukey'])) {
      $select
        ->where('o.ukey LIKE ?', '%' . $filterValues['ukey'] . '%');
    }
    if (!empty($filterValues['name'])) {
      $select
        ->where('oi.name LIKE ?', '%' . $filterValues['name'] . '%');
    }
    if (!empty($filterValues['member'])) {
      $select
        ->joinLeft(array('u' => $prefix . 'users'), 'u.user_id=t.user_id', array())
        ->where('u.displayname LIKE ?', '%' . $filterValues['member'] . '%');
    }
    if (!empty($filterValues['status'])) {
      $select->where('oi.status = ?', $filterValues['status']);
    }
    if (($user_id = $this->_getParam('user_id', @$filterValues['user_id']))) {
      $this->view->filterValues['user_id'] = $user_id;
      $select->where('t.user_id = ?', $user_id);
    }
    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    // Preload info
    $userIds = array();
    $orderIds = array();
    $productIds = array();
    foreach ($paginator as $item) {
      if (!empty($item->user_id)) {
        $userIds[] = $item->user_id;
      }

      if (!empty($item->order_id)) {
        $orderIds[] = $item->order_id;
      }

      if (!empty($item->order_id)) {
        $productIds[] = $item->item_id;
      }
    }

    $userIds = array_unique($userIds);
    $orderIds = array_unique($orderIds);
    $productIds = array_unique($productIds);

    // Preload users
    $users = array();
    if (!empty($userIds)) {
      foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;

    // Preload orders
    $orders = array();
    if (!empty($orderIds)) {
      foreach (Engine_Api::_()->getItemTable('store_order')->find($orderIds) as $order) {
        $orders[$order->order_id] = $order;
      }
    }
    $this->view->orders = $orders;

    // Preload products
    $products = array();
    if (!empty($productIds)) {
      foreach (Engine_Api::_()->getItemTable('store_product')->find($productIds) as $product) {
        if ($product == null) continue;
        $products[$product->product_id] = $product;
      }
    }
    $this->view->products = $products;
  }

  public function detailAction()
  {
    $item_id = $this->_getParam('orderitem_id');

    /**
     * @var $item Store_Model_Orderitem
     */
    if (null == ($item = Engine_Api::_()->getItem('store_orderitem', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
          'redirect' => $this->view->url(array('action' => 'order'), 'admin_default', true),
          'redirectTime' => 1000,
          'messages' => Array($this->view->translate("STORE_No order found with the provided id.")
        ))
      );
      return ;
    }

    /**
     * Preload Items
     *
     * @var $order Store_Model_Order
     */
    $this->view->item = $item;
    $this->view->user = $user = $item->getOwner();
    $this->view->order = $order = $item->getParent();
    $this->view->product = $item->getItem();
    $this->view->storeApi = $storeApi = Engine_Api::_()->store();

    $this->view->gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id);

    //Shipping Details
    if (!$item->isItemDigital()) {
      if (isset($order->shipping_details) &&
        isset($order->shipping_details['location_id_1']) &&
        null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
      ) {
        $this->view->country = $country;

        if (isset($order->shipping_details['location_id_2']) &&
          null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
        ) {
          $this->view->state = $state;
        }
      }
    }
  }

  public function statusAction()
  {
    $item_id = $this->_getParam('orderitem_id');

    /**
     * @var $item Store_Model_Orderitem
     */
    if (null == ($item = Engine_Api::_()->getItem('store_orderitem', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'messages' => Array($this->view->translate("STORE_No order found with the provided id."))
      ));
      return ;
    }

    if (!in_array($item->status, array('processing', 'shipping'))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'messages' => Array($this->view->translate("STORE_Status cannot be changed."))
      ));
      return ;
    }

    $this->view->form = $form = new Store_Form_Statistics_ChangeStatus(array('item' => $item));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      return;
    }

    $status = $form->getValue('status');
    $tmp_st = $item->status;
    if ($status == 'cancelled') {
      if ($item->onCancel() == $tmp_st) {
        $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => false,
          'smoothboxClose' => 2000,
          'messages' => Array(Zend_Registry::get('Zend_Translate')->_("STORE_You can not make a cancellation, since you do not have the required amount."))
        ));
        return ;
      }
    } else {
      $item->onComplete();
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRefresh' => 1000,
      'smoothboxClose' => 1000,
      'messages' => Array($this->view->translate("STORE_Status have been successfully changed.")
      ))
    );
  }
}
