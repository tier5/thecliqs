<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TransactionController.php 23.01.12 11:47 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_TransactionController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Credit_Model_Order
   */
  protected $_co;


  public function init()
  {
    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Credit_Transaction');

    // Check viewer and user
    if( !$this->_user || !$this->_user->getIdentity() ) {
      if( $this->_session->__isset('user_id') ) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }

      // If no user, redirect to home?
      if( !$this->_user || !$this->_user->getIdentity() ) {
        return $this->_redirector();
      }
    }
    $this->_session->user_id = $this->_user->getIdentity();

    // Get Credit order
    $co_id = $this->_getParam('co_id', $this->_session->co_id);

    if ($co_id) {
      $this->_co = Engine_Api::_()->getItem('credit_order', $co_id);
    } else {
      $this->_redirector();
    }

    // If no product or product is empty, redirect to home?
    if( !$this->_co || !$this->_co->getIdentity()) {
      $this->_redirector();
    }

    $this->_session->__set('co_id', $this->_co->getIdentity());
  }

  public function indexAction()
  {
    return $this->_helper->redirector->gotoRoute(array('action' => 'process'), 'credit_transaction', true);
  }

  public function processAction()
  {
    /**
     * @var $gatewayTable Payment_Model_DbTable_Gateways
     * @var $gateway Payment_Model_Gateway
     * @var $api Credit_Api_Core
     */
    $api = Engine_Api::_()->credit();
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
      ->where('enabled = ?', 1)
      ->where('gateway_id = ?', $this->_co->gateway_id)
    ;

    if ( null == ($gateway = $gatewayTable->fetchRow($gatewaySelect)) ) {
      $this->_redirector();
    }

    // Prepare host info
    $schema = 'http://';
    if( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $localeParts = explode('_', $this->_user->language);
    if( count($localeParts) > 1 ) {
      $params['region'] = $localeParts[1];
    }
    $params['credit_order_id'] = $this->_co->getIdentity();
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $params['credit_order_id']
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $params['credit_order_id']
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn'))
      . '?order_id=' . $params['credit_order_id']
      . '&state=' . 'ipn';


    // Get gateway plugin
    /**
     * @var $plugin Credit_Plugin_Gateway_PayPal
     */

    $gatewayPlugin = $api->getGateway($this->_co->gateway_id);
    $plugin = $api->getPlugin($this->_co->gateway_id);
    $transaction = $plugin->createCreditTransaction($this->_co, $params);

    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    $this->_session->lock();

    // Handle redirection
    if( $transactionMethod == 'GET' ) {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction()
  {
    // Get order
    if( !$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('credit_order', $orderId)) ||
        !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) ) {
      return $this->_finishPayment('failed');
    }

    /**
    * @var $api Credit_Api_Core
    * @var $plugin Credit_Plugin_Gateway_PayPal | Credit_Plugin_Gateway_2Checkout
    */

    $api = Engine_Api::_()->credit();
    $plugin = $api->getPlugin($gateway->getIdentity());

    try {
      $status = $plugin->onCreditTransactionReturn($this->_co, $this->_getAllParams());
    } catch( Payment_Model_Exception $e ) {
      $status = 'failed';
      $this->_session->__set('errorMessage', $e->getMessage());
    }

    return $this->_finishPayment($status);
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');

    if ( $status == 'completed') {
      $url = $this->view->escape($this->view->url(array('action' => 'manage'), 'credit_general', true));
    } else {
      $url = $this->view->escape($this->view->url((array()), 'credit_general', true));
      $this->view->error = $this->_session->errorMessage;
    }

    $this->view->continue_url = $url;

    $this->_session->unsetAll();
  }

  protected function _finishPayment($status = 'completed')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // No user?
    if( !$this->_user ) {
      $this->_redirector();
    }

    // Log the user in, if they aren't already
    if( ($status == 'completed' ) &&
        $this->_user &&
        !$this->_user->isSelf($viewer) &&
        !$viewer->getIdentity() ) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
      $viewer = $this->_user;
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;

    $this->_session->unsetAll();
    $this->_session->__set('errorMessage', $errorMessage);

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $status));
  }

	protected function _redirector()
	{
    $this->_session->unsetAll();
		return $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
	}
}
