<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PayPal.php 23.01.12 15:15 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Plugin_Gateway_PayPal extends Payment_Plugin_Gateway_PayPal
{
  public function getGateway()
  {
    if( null === $this->_gateway ) {
      $class = 'Engine_Payment_Gateway_PayPal';
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

  public function createCreditTransaction( Credit_Model_Order $co,	array $params = array())
 	{
    // Check that gateways match
    if( $co->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    if (!$co->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    /**
     * Get related info
     *
     * @var $view Zend_View
     * @var $api Credit_Api_Core
     */

    $view = Zend_Registry::get('Zend_View');
    $currency = $this->getGateway()->getCurrency();
    $payment_requests = array();
    $payment_item = array();

    $payment_item = array_merge($payment_item, array(
      'L_PAYMENTREQUEST_0_NAME0' => $view->translate('Buying %s credits', (int)$co->credit),
      'L_PAYMENTREQUEST_0_DESC0' => $view->translate('Buying Credit from %s', $view->layout()->siteinfo['title']),
      'L_PAYMENTREQUEST_0_AMT0' => $co->price
    ));

    $seller = $this->_gatewayInfo->config;

    $payment_requests = array_merge($payment_requests, array_merge(array(
      'PAYMENTREQUEST_0_AMT' => $co->price,
      'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID' => $seller,
      'PAYMENTREQUEST_0_DESC' => $view->translate('Buying %s credits', $co->credit),
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'order',
      'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $co->getIdentity(),
      'PAYMENTREQUEST_0_INVNUM' => $params['credit_order_id'],
      'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
      'PAYMENTREQUEST_0_NOTIFYURL' => $params['ipn_url'],
    ), $payment_item));

 		$params['driverSpecificParams']['PayPal'] = $payment_requests;
    $transaction = $this->createTransaction($params);
 		return $transaction;
 	}

  /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  public function onCreditTransactionReturn(Credit_Model_Order $co, array $params = array())
 	{
     // Check that gateways match
     if( $co->gateway_id != $this->_gatewayInfo->gateway_id ) {
       throw new Engine_Payment_Plugin_Exception('Gateways do not match');
     }

     //Get created orders
     if (!$co->isOrderPending()) {
       throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
     }

     /**
      * Get related info
      *
      * @var $user User_Model_User
      * @var $item Credit_Model_Payment
      * @var $order Credit_Model_Order
      */

     // Check for cancel state - the user cancelled the transaction
     if( $params['state'] == 'cancel' ) {
       $co->onCancel();
       // Error
       throw new Payment_Model_Exception('Your payment has been cancelled and ' .
         'not been purchased. If this is not correct, please try again later.');
     }

     // Check params
     if ( empty($params['token']) ) {
       $co->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
         'transaction. Please try again later.');
     }

     // Get details
     try {
       $data = $this->getService()->detailExpressCheckout($params['token']);
     } catch( Exception $e ) {
       // Cancel order and subscription?
       $co->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
           'transaction. Please try again later.');
     }

     // Let's log it
     $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
         . print_r($data, true), Zend_Log::INFO);

     //Prepare variables
     $new_requests = array();

     $request = $data['PAYMENTREQUEST'][0];

     if ( null == ($order = Engine_Api::_()->getItem('credit_order', $request['PAYMENTREQUESTID'])) ) {
       $co->onPaymentFailure();

       throw new Payment_Model_Exception('CREDIT_No orders found');
     }

     if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
       $co->onPaymentFailure();

       throw new Payment_Model_Exception('Gateways do not match');
     }

     // Check order states
     if ($order->status == 'completed') {
       return 'completed';
     }

     $new_requests = array_merge($new_requests, array(
       'PAYMENTREQUEST_0_AMT' => $request['AMT'],
       'PAYMENTREQUEST_0_PAYMENTACTION' => 'sale',
       'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order->getIdentity(),
       'PAYMENTREQUEST_0_CURRENCYCODE' => $request['CURRENCYCODE'],
     ));

     try {
       $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], $new_requests);
     } catch( Exception $e ) {
       // Log the error
       $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
           . $e->__toString(), Zend_Log::ERR);

       // Cancel order and subscription?
       $co->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
           'transaction. Please try again later.');
     }

 		 // Let's log it
 		 $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
 				. print_r($rdata, true), Zend_Log::INFO);

     $info = $rdata['PAYMENTINFO'][0];

     $order = Engine_Api::_()->getItem('credit_order', $info['PAYMENTREQUESTID']);
     $this->getGateway()->getLog()->log('TJError: '
      				. print_r($order->toArray(), true), Zend_Log::INFO);
     // Get payment state
     $paymentStatus = null;
     $orderStatus = null;

     switch( strtolower($info['PAYMENTSTATUS']) ) {
       case 'created':
       case 'pending':
         $paymentStatus = 'pending';
         $orderStatus = 'complete';
         break;

       case 'completed':
       case 'processed':
       case 'canceled_reversal': // Probably doesn't apply
         $paymentStatus = 'okay';
         $orderStatus = 'complete';
         break;

       case 'denied':
       case 'failed':
       case 'voided': // Probably doesn't apply
       case 'reversed': // Probably doesn't apply
       case 'refunded': // Probably doesn't apply
       case 'expired':  // Probably doesn't apply
       default: // No idea what's going on here
         $paymentStatus = 'failed';
         $orderStatus = 'failed'; // This should probably be 'failed'
         break;
     }

     // Update order with profile info and complete status?
     $order->status = $orderStatus;
     $order->gateway_transaction_id = $info['TRANSACTIONID'];
     $order->save();

     // Insert transaction
     /**
      * @var $transactionsTable Credit_Model_DbTable_Transactions
      */

     $transactionsTable = Engine_Api::_()->getDbTable('transactions', 'credit');
     $db = $transactionsTable->getAdapter();
     $db->beginTransaction();

     try {
       $transactionsTable->insert(array(
         'order_id' => $order->getIdentity(),
         'user_id' => $order->user_id,
         'gateway_id' => $order->gateway_id,
         'creation_date' => new Zend_Db_Expr('NOW()'),
         'state' => $paymentStatus,
         'gateway_transaction_id' => $info['TRANSACTIONID'],
         'credits' => $order->credit,
         'price' => $order->price,
         'currency' => $info['CURRENCYCODE'],
       ));

       $db->commit();
     } catch( Exception $e) {
       $db->rollBack();
       throw $e;
     }

     // Check payment status
     if( $paymentStatus == 'okay' ) {

       // Update credit info
       $co->gateway_id = $this->_gatewayInfo->gateway_id;
       $co->gateway_transaction_id = $info['TRANSACTIONID'];

       // Payment success
       $co->onPaymentSuccess();

       $status = 'completed';
     }
     else if( $paymentStatus == 'pending' ) {

       // Update credit info
       $co->gateway_id = $this->_gatewayInfo->gateway_id;
       $co->gateway_transaction_id = $info['TRANSACTIONID'];

       // Payment pending
       $co->onPaymentPending();

       $status = 'pending';
     }
     else if( $paymentStatus == 'failed' ) {
       // Cancel order and subscription?
       $co->onPaymentFailure();

       if ($co instanceof Credit_Model_Order) {
         $co->onPaymentFailure();
         throw new Payment_Model_Exception('There was an error processing your ' .
                             'transaction. Please try again later.');
       }

       $co->onPaymentFailure();
       // Payment failed
       throw new Payment_Model_Exception('Your payment could not be ' .
                    'completed. Please ensure there are sufficient available funds ' .
                    'in your account.');
     }
     else {
       throw new Payment_Model_Exception('There was an error processing your ' .
                    'transaction. Please try again later.');
     }

     return $status;
 	}
}