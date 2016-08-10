<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: TransactionsController.php 5/16/12 5:05 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_TransactionsController extends Store_Controller_Action_User
{
  public function init()
  {
    /**
     * @var $page Page_Model_Page
     */
    if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
      Engine_Api::_()->core()->setSubject($page);
    }

    // Set up requires
    $this->_helper->requireSubject('page')->isValid();

    $this->view->page = $page = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }
  }

  public function indexAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this->view->page;

    // Make form
    $this->view->formFilter = $formFilter = new Store_Form_Transaction_Filter(array('page' => $page));

    $formFilter
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->setAttribs(array(
      'id'    => 'search_form',
      'class' => 'store_filter_form inner',
    ));

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'transaction_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order        = $filterValues['order'];
    $this->view->direction    = $filterValues['direction'];

    /**
     * Initialize select
     *
     * @var $table Store_Model_DbTable_Orderitems
     */
    $table  = Engine_Api::_()->getDbtable('orderitems', 'store');
    $prefix = $table->getTablePrefix();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('oi'=> $table->info('name')))
      ->joinInner(array('o'=> $prefix . 'store_orders'), 'o.order_id=oi.order_id', array())
      ->joinInner(array('t'=> $prefix . 'store_transactions'), 't.order_id=oi.order_id', array('t.user_id', 't.timestamp',
      'payment_state' => 't.state'))
      ->where('oi.item_type = ?', 'store_product')
      ->where("oi.status IN('completed', 'shipping', 'delivered')")
      ->group('oi.orderitem_id')
    ;

    // Add filter values
    if (!empty($filterValues['name'])) {
      $select
        ->where('oi.name LIKE ?', '%' . $filterValues['name'] . '%');
    }
    if (!empty($filterValues['member'])) {
      $select
        ->joinLeft(array('u'=> $prefix . 'users'), 'u.user_id=t.user_id', array())
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

    $select->where('oi.page_id = ?', $page->getIdentity());

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    /**
     * @var $pageApi Store_Api_Page
     */
    $this->view->api      = Engine_Api::_()->store();
    $pageApi              = Engine_Api::_()->getApi('page', 'store');
    $this->view->balances = $pageApi->getBalance($page->getIdentity());
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    // Preload info
    $userIds    = array();
    $orderIds   = array();
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

    $userIds    = array_unique($userIds);
    $orderIds   = array_unique($orderIds);
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
    /**
     * @var $page Page_Model_Page
     */
    $page    = $this->view->page;
    $item_id = $this->_getParam('orderitem_id');

    /**
     * @var $item Store_Model_Orderitem
     */
    if (null == ($item = Engine_Api::_()->getItem('store_orderitem', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'redirect'       => $this->view->url(array('controller' => 'transactions',
                                                   'page_id'=> $page->getIdentity()), 'store_extended', true),
        'redirectTime'   => 1000,
        'messages'       => Array($this->view->translate("STORE_No order found with the provided id.")
        )));
    }

    /**
     * Preload Items
     *
     * @var $order Store_Model_Order
     */
    $this->view->item     = $item;
    $this->view->user     = $user = $item->getOwner();
    $this->view->order    = $order = $item->getParent();
    $this->view->product  = $item->getItem();
    $this->view->api = $storeApi = Engine_Api::_()->store();

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

  public function changeStatusAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page    = $this->view->page;
    $item_id = $this->_getParam('orderitem_id');

    /**
     * @var $item Store_Model_Orderitem
     */
    if (null == ($item = Engine_Api::_()->getItem('store_orderitem', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'messages'       => Array($this->view->translate("STORE_No order found with the provided id.")
        )));
    }

    if (!in_array($item->status, array('processing', 'shipping'))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'messages'       => Array($this->view->translate("STORE_Status cannot be changed.")
        )));
    }

    $this->view->form = $form = new Store_Form_Statistics_ChangeStatus(array('item'=> $item));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      return;
    }

    $status = $form->getValue('status');

    if ($status == 'cancelled') {
      $item->onCancel();
    } else {
      $item->onComplete();
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRefresh'  => 1000,
      'smoothboxClose' => 1000,
      'messages'       => Array($this->view->translate("STORE_Status have been successfully changed.")
      )));
  }
}
