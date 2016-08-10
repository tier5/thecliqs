<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminRequestsController.php 5/10/12 11:46 AM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminRequestsController extends Store_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_requests');
  }

  public function indexAction()
  {
    $this->view->formFilter = $formFilter = new Store_Form_Admin_Requests_Filter();

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'request_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }

    $this->view->filterValues = $filterValues;
    $this->view->order        = $filterValues['order'];
    $this->view->direction    = $filterValues['direction'];

    /**
     * @var $table Store_Model_DbTable_Requests
     */
    $table  = Engine_Api::_()->getDbTable('requests', 'store');
    $prefix = $table->getTablePrefix();
    $select = $table
      ->select()
      ->from($table->info('name'))
      ->where('status != ?', 'cancelled')
      ->order('request_date DESC');

    // Add filter values
    if (!empty($filterValues['store'])) {
      $select
        ->joinRight($prefix.'page_pages', $prefix . 'page_pages.page_id=' . $prefix . 'store_requests.page_id', array())
        ->where(' (' . $prefix . 'page_pages.name LIKE ? OR ' . $prefix . 'page_pages.displayname LIKE ?) ', '%' . $filterValues['store'] . '%', '%' . $filterValues['store'] . '%');
    }
    if (!empty($filterValues['status'])) {
      $select->where($prefix . 'store_requests.status = ?', $filterValues['status']);
    }

    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //preload stores
    $pages = array();
    foreach ($paginator as $request) {
      $pages[$request->page_id] = Engine_Api::_()->getItem('page', $request->page_id);
    }
    $this->view->pages = $pages;
  }

  public function responseAction()
  {
    $request_id = $this->_getParam('request_id');

    /**
     * @var $request Store_Model_Request
     * @var $page    Page_Model_Page
     */
    if (null == ($request = Engine_Api::_()->getItem('store_request', $request_id)) ||
      null == ($page = $request->getOwner('page')) ||
      !Engine_Api::_()->getApi('page', 'store')->isStore($page->getIdentity())
    ) {
      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect'     => $this->view->url(array(
          'module'     => 'store',
          'controller' => 'requests',
          'action'     => 'index'
        ), 'admin_default', true),
        'parentRedirectTime' => 1000,
        'messages'           => Array($this->view->translate('STORE_No request was found with the provided ID.'))
      ));
    }

    $this->view->request = $request;
    $this->view->page    = $page;

    /**
     * @var $order Store_Model_Order;
     * @var $order Store_Model_Gateway;
     */

    if(in_array($request->status, array('completed', 'pending')) &&
      null != ($order = $request->getOrderId()) &&
      null != ($gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id))
    ) {
      $this->view->order = $order;
      $this->view->gateway = $gateway;
      $this->view->user = $order->getOwner();
    }

    if( $request->status != 'waiting' ) {
      return ;
    }

    /**
     * @var $table   Store_Model_DbTable_Apis
     */
    $table = Engine_Api::_()->getDbTable('apis', 'store');
    if ($table->getEnabledGatewayCount($page->getIdentity()) <= 0) {
      return ;
    }

    $this->view->form = $form = new Store_Form_Admin_Requests_Response(array('request'=> $request));

    // Check method/valid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values     = $form->getValues();
    $gateway_id = (int)$this->_getParam('gateway_id');
    /**
     * @var $orderTable Store_Model_DbTable_Orders
     * @var $settings Core_Model_DbTable_Settings
     * @var $viewer     User_Model_User
     */
    $orderTable = Engine_Api::_()->getDbTable('orders', 'store');
    $settings   = Engine_Api::_()->getDbTable('settings', 'core');
    $viewer     = Engine_Api::_()->user()->getViewer();

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $request->response_message = $values['response_message'];

      if (null == ($order = $request->getOrderId())) {
        $data = array(
          'user_id'     => $viewer->getIdentity(),
          'gateway_id'  => $gateway_id,
          'item_type'   => $request->getType(),
          'item_id'     => $request->getIdentity(),
          'item_amt'    => $request->amt,
          'total_amt'   => $request->amt,
          'currency'    => $settings->getSetting('payment.currency', 'USD')
        );

        $order = $orderTable->createRow();
        $order->setFromArray($data);
        $order->save();
      } else {
        $order->gateway_id       = $gateway_id;
        $order->status           = 'initial';
        $order->item_amt         = $request->amt;
        $order->tax_amt          = '0.00';
        $order->shipping_amt     = '0.00';
        $order->total_amt        = $request->amt;
        $order->commission_amt   = '0.00';
        $order->currency         = $settings->getSetting('payment.currency', 'USD');
        $order->shipping_details = '';
        $order->updateUkey();
      }

      $request->save();
      // Commit
      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      if (APPLICATION_ENV == 'development') {
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect'     => $this->view->url(array(
          'module'     => 'store',
          'controller' => 'requests',
          'action'     => 'response',
          'request_id' => $request->getIdentity(),
        ), 'admin_default', true),
        'parentRedirectTime' => 1000,
        'messages'           => Array($this->view->translate('STORE_An error has occurred while creating your order.'
          . ' Please, try again with another gateway.'))
      ));
    }

    return $this->_helper->redirector->gotoRoute(array(
        'module'     => 'store',
        'controller' => 'transaction',
        'order_id'   => $order->ukey
      ),
      'admin_default', true);
  }

  public function denyAction()
  {
    $request_id = $this->_getParam('request_id');

    /**
     * @var $request Store_Model_Request
     * @var $page    Page_Model_Page
     */
    if (null == ($request = Engine_Api::_()->getItem('store_request', $request_id)) ||
      null == ($page = $request->getOwner('page')) ||
      !Engine_Api::_()->getApi('page', 'store')->isStore($page->getIdentity())
    ) {
      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect'     => $this->view->url(array(
          'module'     => 'store',
          'controller' => 'requests',
          'action'     => 'index'
        ), 'admin_default', true),
        'parentRedirectTime' => 1000,
        'messages'           => Array($this->view->translate('STORE_No request was found with the provided ID.'))
      ));
    }

    $this->view->request = $request;
    $this->view->page    = $page;

    $this->view->form = $form = new Store_Form_Admin_Requests_Deny(array('request'=> $request));

    // Check method/valid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    /**
     * @var $balance Store_Model_Balance
     */
    $balance = Engine_Api::_()->getApi('page', 'store')->getBalance($page->getIdentity());

    $db = $request->getTable()->getDefaultAdapter();
    $db->beginTransaction();
    try {
      $request->response_message = $form->getValue('response_message');
      $request->onDeny();
      $request->save();

      $balance->decreaseRequested($request->amt);
      $balance->increase($request->amt);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();

      if (APPLICATION_ENV == 'development') {
        throw $e;
      } else {
        $form->addError('STORE_An unexpected error has occurred. Please, try again.');
        print_log($e->__toString());
      }
      return;
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRefresh'      => 1000,
      'smoothboxClose'     => 1000,
      'messages'           => Array($this->view->translate('STORE_The request has been denied.'))
    ));
  }
}
