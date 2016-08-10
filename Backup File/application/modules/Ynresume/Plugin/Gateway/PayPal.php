<?php
class Ynresume_Plugin_Gateway_PayPal extends Payment_Plugin_Gateway_PayPal
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

  public function createPackageTransaction(Ynresume_Model_Order $order, array $params = array())
 	{
    // Check that gateways match
    if( $order -> gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    /**
     * Get related info
     *
     * @var $view Zend_View
     */

    $view = Zend_Registry::get('Zend_View');
    $currency = $this->getGateway()->getCurrency();
    $payment_requests = array();
    $payment_item = array();

    $payment_item = array_merge($payment_item, array(
      'L_PAYMENTREQUEST_0_NAME0' => $view->translate('Buy Resume'),
      'L_PAYMENTREQUEST_0_DESC0' => $view->translate('Buy Resume from %s', $view->layout()->siteinfo['title']),
      'L_PAYMENTREQUEST_0_AMT0' => $order->price
    ));

    $seller = $this->_gatewayInfo->config;

    $payment_requests = array_merge($payment_requests, array_merge(array(
      'PAYMENTREQUEST_0_AMT' => $order->price,
      'PAYMENTREQUEST_0_SELLERPAYPALACCOUNTID' => $seller,
      'PAYMENTREQUEST_0_DESC' => $view->translate('Buy Resume'),
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'order',
      'PAYMENTREQUEST_0_PAYMENTREQUESTID' => $order->getIdentity(),
      'PAYMENTREQUEST_0_INVNUM' => $params['order_id'],
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

  public function onPackageTransactionReturn(Ynresume_Model_Order $order, array $params = array())
  {
 	 $viewer = Engine_Api::_()->user()->getViewer();
	 $view = Zend_Registry::get('Zend_View');
     // Check that gateways match
     if( $order ->gateway_id != $this->_gatewayInfo->gateway_id ) {
       throw new Engine_Payment_Plugin_Exception('Gateways do not match');
     }

     //Get created orders
     if (!$order->isOrderPending()) {
       throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
     }

     // Check for cancel state - the user cancelled the transaction
     if( $params['state'] == 'cancel' ) {
       $order->onCancel();
       // Error
       throw new Payment_Model_Exception('Your payment has been cancelled and ' .
         'not been purchased. If this is not correct, please try again later.');
     }

     // Check params
     if ( empty($params['token']) ) {
       $order->onPaymentFailure();
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
       $order->onPaymentFailure();
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

     if ( null == ($order = Engine_Api::_()->getDbTable('orders', 'ynresume') -> findRow($request['PAYMENTREQUESTID'])) ) {
       $order->onPaymentFailure();

       throw new Payment_Model_Exception('CREDIT_No orders found');
     }

     if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
       $order->onPaymentFailure();

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
       $order->onPaymentFailure();
       // This is a sanity error and cannot produce information a user could use
       // to correct the problem.
       throw new Payment_Model_Exception('There was an error processing your ' .
           'transaction. Please try again later.');
     }

	 // Let's log it
	 $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
			. print_r($rdata, true), Zend_Log::INFO);

     $info = $rdata['PAYMENTINFO'][0];
     $order = Engine_Api::_()->getDbTable('orders', 'ynresume') -> findRow($info['PAYMENTREQUESTID']);
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
	 
	 $service_day_number = $order -> service_day_number;
	 $feature_day_number = $order -> feature_day_number;
	 
	 // Insert transaction
	 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynresume');
     $db = $transactionsTable->getAdapter();
     $db->beginTransaction();
     try {
     	$description = "";
		$view = Zend_Registry::get('Zend_View');
		
		if($service_day_number){
			Engine_Api::_() -> ynresume() -> serviceResume($order->item_id, $order -> service_day_number);
			$description = $view ->translate(array("Register \"Who Viewed Me\" service for %s day", "Register \"Who Viewed Me\" service for %s days" , $order -> service_day_number), $order -> service_day_number);
		}
		elseif($feature_day_number) {
			Engine_Api::_() -> ynresume() -> featureResume($order->item_id, $order -> feature_day_number);
			$description = $view ->translate(array("Feature resume for %s day", "Feature resume for %s days" , $order -> feature_day_number), $order -> feature_day_number);
		}
		//save transaction
     	$transactionsTable->insert(array(
	     	'creation_date' => date("Y-m-d"),
	     	'status' => 'completed',
	     	'gateway_id' => $this->_gatewayInfo->gateway_id,
	     	'amount' => $order->price,
	     	'currency' => $order->currency,
	     	'user_id' => $order->user_id,
	     	'item_id' => $order->item_id,
	     	'payment_transaction_id' => $info['TRANSACTIONID'],
	     	'description' => $description,
		 ));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
 
	 // Insert transaction
	 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
     $db = $transactionsTable->getAdapter();
     $db->beginTransaction();

     try {
     	$transactionsTable->insert(array(
	    'user_id' => $order->user_id,
	    'gateway_id' => $this->_gatewayInfo->gateway_id,
	    'timestamp' => new Zend_Db_Expr('NOW()'),
	    'order_id' => $order->getIdentity(),
	    'type' => 'Resume',
	    'state' => $paymentStatus,
	    'gateway_transaction_id' => $info['TRANSACTIONID'],
	    'amount' => $order->price, // @todo use this or gross (-fee)?
	    'currency' => $info['CURRENCYCODE'],
	  ));
		
       $db->commit();
     } catch( Exception $e) {
       $db->rollBack();
       throw $e;
     }
	 

     // Check payment status
     if( $paymentStatus == 'okay' ) {
       // Payment success
       $order->onPaymentSuccess();
       $status = 'completed';
     }
     else if( $paymentStatus == 'pending' ) {

       // Payment pending
       $order->onPaymentPending();
       $status = 'pending';
     }
     else if( $paymentStatus == 'failed' ) {
       // Cancel order and subscription?
       $order->onPaymentFailure();

       if ($order instanceof Ynresume_Model_Order) {
         $order->onPaymentFailure();
         throw new Payment_Model_Exception('There was an error processing your ' .
                             'transaction. Please try again later.');
       }

       $order->onPaymentFailure();
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