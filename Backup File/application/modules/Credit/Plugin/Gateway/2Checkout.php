<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: 2Checkout.php 26.01.12 12:25 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Plugin_Gateway_2Checkout extends Payment_Plugin_Gateway_2Checkout
{
  public function getGateway()
  {
    if( null === $this->_gateway ) {
      $class = 'Engine_Payment_Gateway_2Checkout';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config' => (array) $this->_gatewayInfo->config,
        'testMode' => $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if( !($gateway instanceof Engine_Payment_Gateway) ) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }

    return $this->_gateway;
  }

  public function createCreditTransaction(Credit_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get unique orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    /**
     * @var $package Credit_Model_Payment
     */

    $package = $order->getSource();

    //Try to update/create all product if enabled
    $gatewayPlugin = $this->_gatewayInfo->getGateway();
    if ($this->_gatewayInfo->enabled &&
      method_exists($gatewayPlugin, 'createProduct') &&
      method_exists($gatewayPlugin, 'editProduct') &&
      method_exists($gatewayPlugin, 'detailVendorProduct')
    ) {
      // If it throws an exception, or returns empty, assume it doesn't exist?
      try {
        $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
      } catch (Exception $e) {
        $info = false;
      }
      // Create
      if (!$info) {
        $gatewayPlugin->createProduct($package->getPaymentParams());
      }
    }

    // Do stuff to params
    $params['fixed'] = true;
    $params['skip_landing'] = true;

    // Lookup product id for this subscription
    $productInfo = $this->getService()->detailVendorProduct($package->getGatewayIdentity());
    $params['product_id'] = $productInfo['product_id'];
    $params['quantity'] = 1;

    // Create transaction
    $transaction = $this->createTransaction($params);
    return $transaction;
  }

  public function onCreditTransactionReturn(Credit_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get created orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    /**
     * @var $user User_Model_User
     * @var $item Credit_Model_Payment
     */

    $user = $order->getUser();
    $item = $order->getSource();

    // Check order states
    if ($order->status == 'completed') {
      return 'completed';
    }

    // Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);

    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      $order->onCancel();
      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
        'not been purchased. If this is not correct, please try again later.');
    }
    // Check for processed
    if (empty($params['credit_card_processed'])) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure product ids match
    if ($params['merchant_product_id'] != $item->getGatewayIdentity()) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure order ids match
    if ($params['order_id'] != $order->order_id &&
      $params['merchant_order_id'] != $order->order_id
    ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure vendor ids match
    if ($params['sid'] != $this->getGateway()->getVendorIdentity()) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Validate return
    try {
      $this->getGateway()->validateReturn($params);
    } catch (Exception $e) {
      if (!$this->getGateway()->getTestMode()) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }
    }

    // Update order with profile info and complete status?
    $order->status = 'complete';
    $order->gateway_transaction_id = $params['order_number'];
    $order->save();

    /**
     * @var $transactionsTable Credit_Model_DbTable_Transactions
     */
    $real_price = 0;
    if ($item instanceof Credit_Model_Payment) {
      $real_price = (float)$order->price;
    }

    $transactionsTable = Engine_Api::_()->getDbTable('transactions', 'credit');
    $db = $transactionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $transactionsTable->insert(array(
        'order_id' => $order->getIdentity(),
        'user_id' => $order->user_id,
        'gateway_id' => $order->gateway_id,
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'state' => 'okay',
        'gateway_transaction_id' => $params['order_number'],
        'credits' => $order->price,
        'price' => $real_price,
        'currency' => $this->getGateway()->getCurrency(),
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $order->onPaymentSuccess();
    return 'completed';
 	}
}
