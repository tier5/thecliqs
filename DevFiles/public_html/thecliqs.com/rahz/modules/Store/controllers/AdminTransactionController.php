<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminTransactionController.php 5/10/12 6:17 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminTransactionController extends Core_Controller_Action_Admin
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
   * @var Store_Model_Gateway|Store_Model_Api
   */
  protected $_gateway;

  /**
   * @var Store_Model_Order
   */
  protected $_order;


  /**
   * @var $_item Store_Model_Request
   */
  protected $_item;

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_requests');

    // Get user and session
    $this->_user    = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Store_Request_Transaction');

    // Get Store order
    $order_ukey = $this->_getParam('order_id', $this->_session->__get('order_id'));
    if (!$order_ukey || null == ($this->_order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey))
    ) {
      return $this->_redirector();
    }
    $this->_session->__set('order_id', $this->_order->ukey);

    /**
     * @var $table   Store_Model_DbTable_Apis
     */
    if (null == ($this->_item = $this->_order->getItem())) {
      return $this->_redirector();
    }
    $this->_session->__set('request_id', $this->_item->getIdentity());
    $table = Engine_Api::_()->getDbtable('apis', 'store');

    // Get Store gateway
    if (!$table->isGatewayEnabled($this->_item->page_id, $this->_order->gateway_id)) {
      return $this->_redirector();
    }

    $this->_gateway = $table->getGateway($this->_item->page_id, $this->_order->gateway_id);
  }

  public function indexAction()
  {
    return $this->_helper->redirector->gotoRoute(array(
      'module'     => 'store',
      'controller' => 'transaction',
      'action'     => 'process'
    ), 'admin_default', true);
  }

  public function processAction()
  {
    $item = $this->_item;
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
                               'controller' => 'ipn'), 'store_extended', true)
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
      $transaction = $plugin->createRequestTransaction($this->_order, $params);
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

    // Post will be handled by the view script
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
        $status = $plugin->onRequestTransactionReturn($this->_order, $this->_getAllParams());
      } catch (Store_Model_Exception $e) {
        if ('development' == APPLICATION_ENV) {
          throw $e;
        } else {
          $this->_session->__set('errorMessage', $e->getMessage());
          $status = 'failed';
        }
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

    // Clear session
    $errorMessage = $this->_session->__get('errorMessage');
    $errorName    = $this->_session->__get('errorName');
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

    $url = $this->view->escape($this->view->url(array(
        'module'       => 'store',
        'controller'   => 'requests',
        'action'       => 'response',
        'request_id'   => $this->_order->item_id),
      'admin_default', true));

    if (!in_array($status, array('completed', 'shipping', 'processing'))) {
      if (!$this->_session->__isset('errorMessage')) {
        $this->view->error = 'There was an error processing your transaction. Please try again later.';
      } else {
        $this->view->error     = $this->_session->__get('errorMessage');
        $this->view->errorName = $this->_session->__get('errorName');
      }
    }

    $this->view->continue_url = $url;

    $this->_session->unsetAll();
  }

  protected function _redirector()
  {
    if ($this->_getParam('request_id', $this->_session->__get('request_id', false))) {
      $request_id = $this->_getParam('request_id');
    }
    elseif ($this->_order instanceof Store_Model_Order) {
      $request_id = $this->_order->item_id;
    }

    $this->_session->unsetAll();

    if (isset($request_id)) {
      return $this->_helper->redirector->gotoRoute(array(
        'module'     => 'store',
        'controller' => 'requests',
        'action'     => 'response',
        'request_id' => $request_id,
      ), 'admin_default', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array(
        'module'     => 'store',
        'controller' => 'requests',
      ), 'admin_default', true);
    }
  }
}
