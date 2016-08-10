<?php
class Ynjobposting_Plugin_Gateway_2Checkout extends Payment_Plugin_Gateway_2Checkout
{
	
  public function getGateway()
  {
    if( null === $this->_gateway ) 
    {
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

  public function createPackageTransaction(Ynjobposting_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get unique orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }
    $gatewayPlugin = $this->_gatewayInfo->getGateway();
    if ($this->_gatewayInfo->enabled &&
      method_exists($gatewayPlugin, 'createProduct') &&
      method_exists($gatewayPlugin, 'editProduct') &&
      method_exists($gatewayPlugin, 'detailVendorProduct')
    ) {
      // If it throws an exception, or returns empty, assume it doesn't exist?
      try {
        $info = $gatewayPlugin->detailVendorProduct($order->getGatewayIdentity($order->user_id, $order->price));
      } catch (Exception $e) {
        $info = false;
      }
      // Create
      if (!$info) {
      	$arr['user_id'] = $order->user_id;
		$arr['price'] = $order->price; 
        $gatewayPlugin->createProduct($order->getPackageParams($arr));
      }
    }
    // Do stuff to params
    $params['fixed'] = true;
    $params['skip_landing'] = true;

    // Lookup product id for this subscription
    $productInfo = $this->getService()->detailVendorProduct($order->getGatewayIdentity($order->user_id, $order->price));
    $params['product_id'] = $productInfo['product_id'];
    $params['quantity'] = 1;
    // Create transaction
    $transaction = $this->createTransaction($params);
    return $transaction;
  }

  public function onPackageTransactionReturn(Ynjobposting_Model_Order $order, array $params = array())
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
	$view = Zend_Registry::get('Zend_View');
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    //Get created orders
    if (!$order->isOrderPending()) {
      throw new Engine_Payment_Plugin_Exception('CREDIT_No orders found');
    }

    $user = $order->getUser();

    // Check order states
    if ($order->status == 'completed') {
      return 'completed';
    }
	
	// Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);
	
    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') 
    {
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
    if ($params['merchant_product_id'] != $order->getGatewayIdentity($order->user_id, $order->price)) {
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
      /*if (!$this->getGateway()->getTestMode()) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }*/
    }
    // Update order with profile info and complete status?
    $order->gateway_transaction_id = $params['order_number'];
    $order->save();
	
	$featured = $order -> featured;
	$package_id = $order -> package_id;
	
	 // Insert member transaction
	 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynjobposting');
     $db = $transactionsTable->getAdapter();
     $db->beginTransaction();
     try {
     	$description = "";
		$view = Zend_Registry::get('Zend_View');
     	if($order -> type == 'job')
		{
	     	Engine_Api::_() -> ynjobposting() -> buyJob($order -> item_id, $order -> package_id, $order -> number_day);
			//add feature
			if($package_id)
			{
				$package = $order -> getSource();
				$description = $view ->translate(array('Buy job in %s day', 'Buy job in %s days', $package -> valid_amount), $package -> valid_amount);
			}
			if($featured)
			{
				$description = $view ->translate(array('Feature job in %s day', 'Feature job in %s days', $order -> number_day), $order -> number_day);
			}
			if($featured & $package_id)
			{ 	
				$package = $order -> getSource();
				$description = $view ->translate(array('Buy job in %1s day - Feature job in %2s day', 'Buy job in %1s days - Feature job in %2s days', $package -> valid_amount, $order -> number_day), $package -> valid_amount, $order -> number_day);
			}
		}
		else 
		{
			Engine_Api::_() -> ynjobposting() -> buyCompany($order -> item_id, $order -> number_day);
			$description = $view ->translate(array('Sponsor company in %s day', 'Sponsor company in %s days', $order -> number_day), $order -> number_day);
		}
		//save transaction
     	$transactionsTable->insert(array(
	     	'creation_date' => date("Y-m-d"),
	     	'status' => 'completed',
	     	'gateway_id' => $this->_gatewayInfo->gateway_id,
	     	'amount' => $order->price,
	     	'currency' => $order->currency,
	     	'user_id' => $order->user_id,
	     	'type' => $order->type,
	     	'item_id' => $order->item_id,
	     	'payment_transaction_id' => $params['order_number'],
	     	'description' => $description,
		 ));
		 
		 //send notification to admin
		 if($order->type == 'company')
		 {
		 	$notificationType = 'ynjobposting_company_transaction';
			$item = Engine_Api::_() -> getItem('ynjobposting_company', $order->item_id);
		 }
	     elseif($order->type == 'job')
		 {
		 	$notificationType = 'ynjobposting_job_transaction';
			$item = Engine_Api::_() -> getItem('ynjobposting_job', $order->item_id);
		 }
		 $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		 $list_admin = Engine_Api::_()->user()->getSuperAdmins();
		 foreach($list_admin as $admin)
		 {
			 $notifyApi -> addNotification($admin, $item, $item, $notificationType);
		 }
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
	    'type' => 'Job Posting',
	    'state' => 'okay', 
	    'gateway_transaction_id' => $params['order_number'],
	    'amount' => $order->price, // @todo use this or gross (-fee)?
	    'currency' => $params['currency_code'],
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
