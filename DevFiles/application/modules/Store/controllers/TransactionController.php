<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TransactionController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_TransactionController extends Core_Controller_Action_Standard
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
   * @var Store_Model_Gateway | Store_Model_Api
   */
  protected $_gateway;

  /**
   * @var Store_Model_Order
   */
  protected $_order;

  public function init()
  {
    // Get user and session
    $this->_user    = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Store_Transaction');

    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      if ($this->_session->__isset('user_id')) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->__get('user_id'));
      }
      // If no user, redirect to home?
      if (!$this->_user || !$this->_user->getIdentity()) {
        return $this->_redirector();
      }
    }

    $this->_session->__set('user_id', $this->_user->getIdentity());

    // Get Store order
    $order_ukey = $this->_getParam('order_id', $this->_session->__get('order_id'));
    if (!$order_ukey || null == ($this->_order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey))) {
      return $this->_redirector();
    }

    $this->_session->__set('order_id', $this->_order->ukey);

    $mode = Engine_Api::_()->store()->getPaymentMode();

    if ($mode == 'client_store') {
      $gateway = Engine_Api::_()->getItem('store_gateway', $this->_order->gateway_id);
      if ($gateway->getTitle() != 'PayPal') {
        return $this->_redirector();
      }

      $apisTbl = Engine_Api::_()->getDbTable('apis', 'store');
      $stores = $this->_order->getStores();
      foreach ($stores as $page_id => $store) {
        $api = $apisTbl->getApi($page_id, $this->_order->gateway_id);
        if ($api && $api->enabled) {
          $this->_gateway = $gateway;
          break;
        }
      }
    } else {
      // Get Store gateway
      if (!Engine_Api::_()->getDbtable('gateways', 'store')->isGatewayEnabled($this->_order->gateway_id)) {
        return $this->_redirector();
      }
      $this->_gateway = Engine_Api::_()->getItem('store_gateway', $this->_order->gateway_id);
    }
  }

  public function indexAction()
  {
    return $this->_helper->redirector->gotoRoute(array('action' => 'process'), 'store_transaction', true);
  }

  public function processAction()
  {
    $item = $this->_order->getItem();
    if (!($item instanceof Store_Model_Item_Abstract) || $item->getPrice() <= 0) {
      return $this->_redirector();
    }

    // Unset unnecessary values
    $this->_session->__unset('order_id');
    $this->_session->__unset('errorMessage');
    $this->_session->__unset('token');

    /**
     * Make the order unique
     */
    $this->_order->updateUkey();
    $this->_session->__set('order_id', $this->_order->ukey);

    // Get gateway plugin

    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];
    $ukey = $this->_order->ukey;

    // Prepare transaction
    $params             = array();
    $params['language'] = $this->_user->language;
    $localeParts        = explode('_', $this->_user->language);
    if (count($localeParts) > 1) {
      $params['region'] = $localeParts[1];
    }
    $params['vendor_order_id'] = $ukey;
    $params['return_url']      = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $ukey
      . '&state=' . 'return';
    $params['cancel_url']      = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $ukey
      . '&state=' . 'cancel';
    $params['ipn_url']         = $schema . $host
      . $this->view->url(array('action'     => 'index',
                               'controller' => 'ipn',
                               'module' => 'store',
                               ), 'default', true)
      . '?order_id=' . $ukey
      . '&state=' . 'ipn';

    /**
     * Get gateway plugin
     *
     * @var $plugin Experts_Payment_Plugin_Abstract
     */
    $this->view->gatewayPlugin = $gatewayPlugin = $this->_gateway->getGateway();
    $plugin                    = $this->_gateway->getPlugin();

    try {
      $transaction = $plugin->createCartTransaction($this->_order, $params);
    } catch (Exception $e) {
      if ('development' == APPLICATION_ENV) {
        throw $e;
      } elseif (in_array($e->getCode(), array(10736, 10731))) {
        $this->_session->__set('errorMessage', array(
          'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR',
          $this->view->translate(
            'STORE_TRANSACTION_REPORT_FORM %1$scontact%2$s',
            '<a href="javascript:void(0);" onclick="goToContactPageAfterError();return false;">',
            '</a>'
          ),
          $e->getMessage()
        ));
        $this->_session->__set('errorName', $e->getCode());
      } else {
        $this->_session->__set('errorMessage', 'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR');
        print_log($e->__toString());
      }

      return $this->_finishPayment('failed');
    }

    // Pull transaction params
    $this->view->transactionUrl    = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData   = $transactionData = $transaction->getData();

    if (!$transaction->isValid()) {
      if ('development' == APPLICATION_ENV) {
        throw new Engine_Exception('Transaction is invalid');
      }

      return $this->_finishPayment('failed');
    }

    $this->_session->lock();

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }
  }

  public function returnAction()
  {
    /**
     * Get gateway plugin
     *
     * @var $plugin Experts_Payment_Plugin_Abstract
     */
    try {
      $plugin = $this->_gateway->getPlugin();

      try {
        $status = $plugin->onCartTransactionReturn($this->_order, $this->_getAllParams());
      } catch (Store_Model_Exception $e) {
        $this->_session->__set('errorMessage', $e->getMessage());
        $status = 'failed';
      }

    } catch (Exception $e) {
      if ('development' == APPLICATION_ENV) {
        throw $e;
      }

      $status = 'failed';
    }

    return $this->_finishPayment($status);
  }

  protected function _finishPayment($status = 'completed')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Log the user in, if they aren't already
    if (($status == 'completed') &&
      $this->_user &&
      !$this->_user->isSelf($viewer) &&
      !$viewer->getIdentity()
    ) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
      $viewer = $this->_user;
    }

    // Handle email verification or pending approval
    if ($viewer->getIdentity() && !$viewer->enabled) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();
      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
    }

    // Clear session
    $errorMessage = $this->_session->__get('errorMessage');
    $errorName = $this->_session->__get('errorName');
    $this->_session->unsetAll();
    $this->_session->__set('order_id', $this->_order->ukey);
    $this->_session->__set('user_id', $viewer->getIdentity());
    $this->_session->__set('errorMessage', $errorMessage);
    $this->_session->__set('errorName', $errorName);

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish',
                                                       'status' => $status));
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('status');

    if (in_array($status, array('completed', 'shipping', 'processing'))) {
      $url = $this->view->escape($this->view->url(array('order_id' => $this->_order->ukey), 'store_purchase', true));
    } else {
      $url = $this->view->escape($this->view->url(array('controller' => 'cart'), 'store_extended', true));

      if (!$this->_session->__isset('errorMessage')) {
        $this->view->error = 'There was an error processing your transaction. Please try again later.';
      } else {
        $this->view->error = $this->_session->__get('errorMessage');
        $this->view->errorName = $this->_session->__get('errorName');
      }
    }

    $this->view->continue_url = $url;

    $this->_session->unsetAll();
  }

  protected function _redirector()
  {
    $this->_session->unsetAll();
    return $this->_helper->redirector->gotoRoute(array('controller' => 'cart'), 'store_extended', true);
  }
}