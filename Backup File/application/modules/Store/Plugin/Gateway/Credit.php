<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Credit.php 08.06.12 15:39 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Plugin_Gateway_Credit extends Experts_Payment_Plugin_Abstract
{
  // General

  /**
   * Get the gateway object
   *
   * @throws Engine_Exception
   * @return Experts_Payment_Gateway
   */
  public function getGateway()
  {
    if (null === $this->_gateway) {
      $class = 'Experts_Payment_Gateway_Credit';
      Engine_Loader::loadClass($class);
      $gateway = new $class();
      if (!($gateway instanceof Experts_Payment_Gateway)) {
        throw new Engine_Exception('Plugin class not instance of Experts_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }

    return $this->_gateway;
  }

  public function createCartTransaction(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_cart') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_cart'");
    }

    // Check if the cart has any items
    if (!$order->hasItems()) {
      throw new Experts_Payment_Plugin_Exception("Provided store_cart doesn't have any order items");
    }

    $transaction = $this->createTransaction($params);

    return $transaction;
  }

  public function onCartTransactionReturn(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_cart') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_cart'");
    }

    // Check if the cart has any items
    if (!$order->hasItems()) {
      throw new Experts_Payment_Plugin_Exception("Provided store_cart doesn't have any order items");
    }


    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      $order->onPaymentFailure();
      // Error
      throw new Store_Model_Exception('Your payment has been cancelled and ' .
        'not been purchased. If this is not correct, please try again later.');
    }

    if ($order->ukey != $params['ukey']) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    if ($params['confirm_id'] != $this->getCreditConfirmId($order->ukey)) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    $order->save();

    // Get payment status
    $paymentStatus = null;
    $orderStatus   = null;

    switch (strtolower($params['status'])) {
      case 'created':
      case 'pending':
        $paymentStatus = 'pending';
        break;

      case 'completed':
      case 'processed':
      case 'canceled_reversal': // Probably doesn't apply
        $paymentStatus = 'okay';
        break;

      case 'denied':
      case 'failed':
      case 'voided': // Probably doesn't apply
      case 'reversed': // Probably doesn't apply
      case 'refunded': // Probably doesn't apply
      case 'expired': // Probably doesn't apply
      default: // No idea what's going on here
        $paymentStatus = 'failed';
        break;
    }

    /**
     * Insert transaction
     *
     * @var $transactionsTable Store_Model_DbTable_Transactions
     * */
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'store');
    $db                = $transactionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $transactionsTable->insert(array(
        'item_id'                => $order->item_id,
        'item_type'              => $order->item_type,
        'order_id'               => $order->order_id,
        'user_id'                => $order->user_id,
        'timestamp'              => new Zend_Db_Expr('NOW()'),
        'state'                  => $paymentStatus,
        'gateway_id'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id' => $order->ukey,
        'gateway_fee'            => 0.00,
        'amt'                    => $order->total_amt,
        'currency'               => $order->currency,
        'via_credits'            => 1
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      print_log($e);
      throw $e;
    }

    $user = $order->getUser();

    // Check payment status
    if ($paymentStatus == 'okay') {

      // Payment success
      $order->onPaymentSuccess();

      // send notification
      if ($order->didStatusChange()) {
        try {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_cart_complete', array(
            'order_details' => $order->getDetails(),
            'order_link'    => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        } catch (Exception $e) {
          print_log($e, 'mail');
        }
      }
      $order->gateway_transaction_id = $order->ukey;
      $order->save();
      return $order->status;
    }
    else if ($paymentStatus == 'pending') {

      // Payment pending
      $order->onPaymentPending();

      // send notification
      if ($order->didStatusChange()) {
        try {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_cart_pending', array(
            'order_details' => $order->getDetails(),
            'order_link'    => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        } catch (Exception $e) {
          print_log($e, 'mail');
        }
      }

      return $order->status;
    }
    else if ($paymentStatus == 'failed') {

      // Payment failed
      $order->onPaymentFailure();

      throw new Store_Model_Exception('Your payment could not be ' .
        'completed. Please ensure there are sufficient available funds ' .
        'in your account.');
    }
    else {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
  }


  public function onCartTransactionIpn(Store_Model_Order $order, Experts_Payment_Ipn $ipn)
  {
    return $this;
  }

  /**
   * Create a transaction for a order
   *
   * @param Store_Model_Order $order
   * @param array             $params
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Experts_Payment_Transaction
   */
  public function createRequestTransaction(Store_Model_Order $order, array $params = array())
  {

  }


  public function onRequestTransactionReturn(Store_Model_Order $order, array $params = array())
  {

  }

  /**
   * Process ipn of money request transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Store_Plugin_Gateway_PayPal
   */
  public function onRequestTransactionIpn(
    Store_Model_Order $order,
    Experts_Payment_Ipn $ipn)
  {
    return $this;
  }

  // IPN
  /**
   * @param Experts_Payment_Ipn $ipn
   *
   * @return Experts_Payment_Plugin_Abstract|Store_Plugin_Gateway_PayPal
   * @throws Engine_Payment_Plugin_Exception
   */
  public function onIpn(Experts_Payment_Ipn $ipn)
  {
    $rawData = $ipn->getRawData();
  }

  public function processGatewayForm(array $values)
  {
    return $values;
  }

  /**
   * Get the service API
   *
   * @return Experts_Service_PayPal|Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getGateway()->getService();
  }

  /**
   * Get the admin form for editing the gateway info
   *
   * @param array $options
   *
   * @return Store_Form_Admin_Gateway_PayPal|Engine_Form
   */
  public function getAdminGatewayForm($options = array())
  {
    return null;
  }

  public function processAdminGatewayForm(array $values)
  {
    return $values;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @param array $params
   *
   * @return Experts_Payment_Ipn
   */
  public function createIpn(array $params)
  {
    $ipn = new Experts_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }

  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @param $transactionId
   *
   * @return Experts_Payment_Plugin_Abstract
   */
  public function cancelOrder($transactionId)
  {
    // TODO: Implement cancelOrder() method.
  }

  /**
   * Generate href to a page detailing the order
   *
   * @param $orderId
   *
   * @return string
   */
  public function getOrderDetailLink($orderId)
  {
    // TODO: Implement getOrderDetailLink() method.
  }

  /**
   * Generate href to a page detailing the transaction
   *
   * @param string $transactionId
   *
   * @return string
   */
  public function getTransactionDetailLink($transactionId)
  {
    // TODO: Implement getTransactionDetailLink() method.
  }

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
   *
   * @return array
   */
  public function getOrderDetails($orderId)
  {
    // TODO: Implement getOrderDetails() method.
  }

  /**
   * Get raw data about a transaction
   *
   * @param $transactionId
   *
   * @return array
   */
  public function getTransactionDetails($transactionId)
  {
    // TODO: Implement getTransactionDetails() method.
  }

  /**
   * @return Store_Form_Gateway_PayPal
   */
  public function getGatewayForm()
  {
    return null;
  }

  public function getCreditConfirmId($ukey)
  {
    $row = Engine_Api::_()->getDbTable('logs', 'credit')->fetchRow(array('body = ?' => $ukey));
    return $row->log_id;
  }
}
