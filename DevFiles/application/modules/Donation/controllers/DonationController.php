<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 31.07.12
 * Time: 14:30
 * To change this template use File | Settings | File Templates.
 */
class Donation_DonationController extends Core_Controller_Action_Standard
{
  protected $_subject;

  protected $_data;

  protected $_status;

  protected $_session;

  public function init()
  {
    $this->_session = new Zend_Session_Namespace('Donation_Transaction');
    $this->_subject = null;
    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('object_id');
      if (null !== $id) {
        $this->_subject = Engine_Api::_()->getItem('donation', $id);
        Engine_Api::_()->core()->setSubject($this->_subject);
      }
    }
    $this->view->donation = $this->_subject;
  }


  public function donateAction()
  {
    if(!$this->_subject || $this->_subject->status != 'active' || !$this->_subject->approved){
      return $this->_forward('requiresubject', 'error', 'core');
    }

    //If members can not to make donation anonymously and viewer is quest
    if(!$this->_subject->allow_anonymous && !$this->_helper->requireUser()->isValid()){
      return;
    }

    $this->view->viewer = Engine_Api::_()->user()->getViewer();

    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $predefine_list = array();

    if($this->_subject->predefine_list)
    {
      $predefine_list = explode(',',$this->_subject->predefine_list);
    }
    $this->view->predefine_list = $predefine_list;

    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }

  public function checkoutAction()
  {
    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();

    $values = $this->_getAllParams();
    unset($values['action']);
    unset($values['module']);
    unset($values['controller']);
    unset($values['rewrite']);
    unset($values['format']);

    if(!isset($values['amount'])){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view
        ->translate('DONATION_Please choose amount!');
      return;
    }
    elseif(!filter_var($values['amount'], FILTER_VALIDATE_FLOAT)){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view
        ->translate('DONATION_Please enter amount!');
      return;
    }
    elseif($values['amount']<$values['min_amount']){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view
        ->translate('DONATION_Please choose amount more than %s',$values['min_amount']);
      return;
    }
    elseif($values['anon'] == 'true' && !$user->getIdentity()){
       if(preg_match("/^[A-Z][a-zA-Z -]+$/", $values['name']) === 0){
        $this->view->status       = 0;
        $this->view->errorMessage = $this->view
          ->translate('DONATION_Please enter a valid name');
        return;
      }
      elseif(!filter_var($values['email'], FILTER_VALIDATE_EMAIL)){
        $this->view->status       = 0;
        $this->view->errorMessage = $this->view
          ->translate('DONATION_Please enter a valid email');
        return;
      }
    }
    if($values['anon'] == 'true' && $user->getIdentity()){
      $values['name'] = $this->view->translate('DONATION_Anonym');
      $values['email'] = $user->email;
    }
    $this->_session->__set('donation_info', $values);
    $this->view->status = 1;
    $this->view->link   = $this->view->url(array('action' => 'process', 'object' => 'donation', 'object_id' => $values['object_id']), 'donation_donate', true);
  }
  public function processAction()
  {
    $values = $this->_session->__get('donation_info');
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    //Get Gateway
    $gatewayId = $values['gateway_id'];

    /**
     * @var $gateway Payment_Model_Gateway
     */
    $this->view->gateway = $gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId);


    /**
     * @var $donation Donation_Model_Donation
     */

    $donation = Engine_Api::_()->getItem('donation',$values['object_id']);
    // Process


    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();
    if ($values['anon'] == 'true') {
      $user_id = 0;
    }
    else {
      $user_id = (int)$user->getIdentity();
    }
    $ordersTable->insert(array(
      'user_id' => $user_id,
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => $donation->getType(),
      'source_id' => $donation->getIdentity(),
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    if ($gateway->getTitle() == 'PayPal') {
      $params = array(
        'cmd' => '_donations',
        'item_name' => $donation->getTitle(),
        'business' => $donation->getPayPalEmail(),   //PayPal email address
        'notify_url' => $schema . $host
          . $this->view->url(array(),'donation_ipn')
          . '?order_id=' . $order_id
          . '&state=' . 'ipn',
        'return' => $schema . $host
          . $this->view->url(array('action' => 'return'))
          . '?order_id = '.$order_id
          .'&state=' . 'return',
        'cancel_return' => $schema . $host
          . $this->view->url(array('action' => 'return'))
          . '?order_id=' . $order_id
          . '&state=' . 'cancel',
        'rm' => 1,
        'currency_code' => $currency,
        'no_note' => 1,
        'cbt' => $this->view->translate('DONATION_Go Back to The Site'),
        'no_shipping' => 1,
        'bn' => 'PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest',
        'amount' => $values['amount'],
      );
    }


    else{
       $params = array(
         'sid' => 'burya_seller',
         'total' => $values['amount'],
         'tco_currency' => 'USD',
         'id_type' => 1,
         'cart_order_id' => 1,
         'demo' => 'Y',
       );
    }
    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $params;

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction()
  {
    // Get order
    if( //!$this->_page ||
      !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
      !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
      !$gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) {

      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Donation gateway plugin
    $str = str_replace('Payment', 'Donation', get_class($plugin));
    $plugin = new $str( $gateway );

    try{
      $status = $plugin->onCreateTransaction($order,$this->_getAllParams(),$this->_session->__get('donation_info'));
    }
    catch(Exception $e){
       $status = 'failed';
      $this->_session->errorMessage = $e->getMessage();
    }
    return $this->_finishPayment($status);
  }

  public function _finishPayment($state = 'completed')
  {
    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $this->_session->unsetAll();
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
    $this->_session->unsetAll();
  }

  public function promoteAction()
  {
     //todo may be use any code for checking privacy

    if(!$this->_subject){
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => false,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('DONATION_SUBJECT_ERROR'))
      ));
      return ;
    }
    $this->view->base_url = 'http://' . $_SERVER['HTTP_HOST'];
    $this->view->like_button = $this->_getParam('like_button', 1);
    $this->view->donate_button = $this->_getParam('donate_button', 1);
    $this->view->show_supporters = $this->_getParam('show_supporters', 1);
  }

  public function showDonateBoxAction()
  {
    $this->_helper->layout->disableLayout();

    if(!$this->_subject){
      $this->view->html = '';
      return;
    }

    $this->view->donate_box_url = $this->view->url(array(
      'controller' => 'donation',
      'action' => 'donate-box',
      'object' => 'donation',
      'object_id' => $this->_subject->getIdentity(),
      'like_button' => $this->_getParam('like_button', 1),
      'donate_button' => $this->_getParam('donate_button', 1),
      'show_supporters' => $this->_getParam('show_supporters', 1),
    ),'donation_extended', true);

  }

  public function donateBoxAction()
  {
    if(!$this->_subject){
      $this->view->html = '';
      return;
    }

    $this->_helper->layout->disableLayout();
    $this->view->base_url = 'http://' . $_SERVER['HTTP_HOST'];

    $params = array(
      'resource_type' => $this->_subject->getType(),
      'limit' => 4,
      'resource_id' => $this->_subject->getIdentity(),
    );

    // Get supporters count
    $this->view->supporters_count = $supporters_count = Engine_Api::_()->like()->getLikeCount($this->_subject);

    //Get supporters
    $select = Engine_Api::_()->like()->getLikesSelect($params);
    $this->view->supporters = $supporters = $select->query()->fetchAll();
    $this->view->like_button = $this->_getParam('like_button', 1);
    $this->view->donate_button = $this->_getParam('donate_button', 1);
    $this->view->show_supporters = $this->_getParam('show_supporters', 1);
    $this->view->html = $this->view->render('_composeDonateBox.tpl');

  }
}
