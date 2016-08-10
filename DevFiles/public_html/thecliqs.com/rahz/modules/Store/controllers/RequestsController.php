<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: RequestsController.php 5/16/12 4:55 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_RequestsController extends Store_Controller_Action_User
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
      !$page->isStore() ||
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
     * @var $page  Page_Model_Page
     * @var $table Store_Model_DbTable_Requests
     */
    $page = $this->view->page;
    $table = Engine_Api::_()->getDbTable('requests', 'store');

    $select = $table
      ->select()
      ->where('page_id = ?', $page->getIdentity());

    $statuses = array('' => ' ');

    $requests = $table->fetchAll($select);
    foreach ($requests as $request) {
      $statuses[$request->status] = Zend_Registry::get('Zend_Translate')->_(ucfirst($request->status));
    }

    $values = array();
    $this->view->filterForm = $filterForm = $this->getFilterForm($statuses);

    if ($filterForm->isValid($this->_getAllParams())) {
      $values = $filterForm->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'request_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $select->order($values['order'] . ' ' . $values['order_direction']);

    if (!empty($values['status'])) {
      $select
        ->where('status = ?', $values['status']);
    }

    $valuesCopy = array_filter($values);
    $this->view->formValues = $valuesCopy;

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->paginator = $paginator;

    /**
     * @var $pageApi Store_Api_Page
     */
    $pageApi = Engine_Api::_()->getApi('page', 'store');
    $this->view->balances = $pageApi->getBalance($page->getIdentity());
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
  }

  public function requestAction()
  {
    /**
     * @var $page     Page_Model_Page
     * @var $settings Core_Model_DbTable_Settings
     * @var $balance Store_Model_Balance
     */
    $page = $this->view->page;
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $balance = Engine_Api::_()->getItem('store_balance', $this->_getParam('balance_id'));
    $allowedAmt = (double)$settings->getSetting('store.request.amount', 100);

    if ($balance->getBalance() < $allowedAmt) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 5000,
        'messages' => Array($this->view->translate("STORE_YOU_DO_NOT_HAVE_ENOUGH_MONEY_FOR_REQUESTING %1s", $this->view->toCurrency($allowedAmt)))
      ));
      return ;
    }

    $this->view->form = $form = new Store_Form_Statistics_Request(array('params' => array(
      'current' => $balance->getBalance(),
      'allowed' => $allowedAmt
    )));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      return;
    }

    $values = $form->getValues();

    /**
     * @var $table   Store_Model_DbTable_Requests
     * @var $request Store_Model_Request
     */
    $table = Engine_Api::_()->getDbTable('requests', 'store');
    $db = $table->getDefaultAdapter();

    $db->beginTransaction();
    try {
      $request = $table->createRow(array(
        'page_id' => $page->getIdentity(),
        'amt' => $values['request_amt'],
        'request_message' => $values['request_message'],
        'request_date' => new Zend_Db_Expr('NOW()')
      ));

      if ($request->save()) {
        $balance->decrease($request->amt);
        $balance->increaseRequested($request->amt);
      }
      $db->commit();
    } catch (Exception $e) {
      if (APPLICATION_ENV == 'development')
        throw $e;
      else
        $form->addError('STORE_An unexpected error has occurred. Please, try again.');
      return;
    }

    // Add notification
//    Engine_Api::_()->getDbtable('notifications', 'activity')
//      ->addNotification($user, $viewer, $viewer, 'friend_follow');

    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => $this->view->url(array('action' => 'index')),
      'parentRedirectTime' => 1000,
      'smoothboxClose' => 1000,
      'messages' => Array($this->view->translate('STORE_Your request has been successfully completed.'))
    ));
  }

  public function cancelAction()
  {
    /**
     * @var $page    Page_Model_Page
     * @var $request Store_Model_Request
     */
    $page = $this->view->page;
    $request_id = $this->_getParam('request_id');

    if (null == ($request = Engine_Api::_()->getItem('store_request', $request_id)) || !$request->isOwner($page)) {
      $this->_forward('success', 'utility', 'core', array(
        'parentRefresh' => 2000,
        'smoothboxClose' => 2000,
        'messages' => Array($this->view->translate('STORE_No request was found with the provided ID.'))
      ));
    }

    $this->view->form = $form = new Store_Form_Statistics_Cancel(array('request' => $request));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      return;
    }

    $request->cancel();

    $this->_forward('success', 'utility', 'core', array(
      'parentRefresh' => 2000,
      'smoothboxClose' => 2000,
      'messages' => Array($this->view->translate('STORE_Your request has been successfully cancelled'))
    ));
  }

  public function detailAction()
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
        'parentRedirect' => $this->view->url(array(
          'module' => 'store',
          'controller' => 'requests',
          'action' => 'index'
        ), 'default', true),
        'parentRedirectTime' => 1000,
        'messages' => Array($this->view->translate('STORE_No request was found with the provided ID.'))
      ));
    }

    $this->view->request = $request;
    $this->view->page = $page;

    /**
     * @var $order Store_Model_Order;
     * @var $order Store_Model_Gateway;
     */
    if (in_array($request->status, array('completed', 'pending')) &&
      null != ($order = $request->getOrderId()) &&
      null != ($gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id))
    ) {
      $this->view->order = $order;
      $this->view->gateway = $gateway;
      $this->view->user = $order->getOwner();
    }

    if ($request->status != 'waiting') {
      return;
    }

    /**
     * @var $table   Store_Model_DbTable_Apis
     */
    $table = Engine_Api::_()->getDbTable('apis', 'store');
    if ($table->getEnabledGatewayCount($page->getIdentity()) <= 0) {
      return;
    }
  }

  public function getFilterForm($statuses)
  {
    $filterForm = new Engine_Form();
    $filterForm->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form');

    $filterForm
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'store_filter_form inner',
    ));

    $status = new Zend_Form_Element_Select('status');
    $status
      ->setLabel('Status')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($statuses);

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit', 'style' => 'padding: 2px'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $filterForm->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $filterForm->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    $filterForm->addElements(array(
      $status,
      $submit
    ));

    // Set default action
    $filterForm->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    return $filterForm;
  }
}
