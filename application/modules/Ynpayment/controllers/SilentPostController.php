<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_SilentPostController extends Core_Controller_Action_Standard
{
  public function __call($method, array $arguments)
  {
    $params = $this->_getAllParams();
    $gatewayType = $params['action'];
    unset($params['module']);
    unset($params['controller']);
    unset($params['action']);
    unset($params['rewrite']);
    unset($params['gateway_id']);
    if( !empty($gatewayType) && 'index' !== $gatewayType )
    {
      $params['gatewayType'] = $gatewayType;
    } 
    else 
    {
      $gatewayType = null;
    }
	
    // Log silent response
    $silentPostLogFile = APPLICATION_PATH . '/temporary/log/payment-silent-post.log';
    file_put_contents($silentPostLogFile,
        date('c') . ': ' .
        print_r($params, true),
        FILE_APPEND);
	// check gateway supported
	if($gatewayType != 'AuthorizeNet')
	{
		// Gateway was not supported
	      file_put_contents($silentPostLogFile,
	          date('c') . ': ' .
	          'Gateway was not supported.',
	          FILE_APPEND);
	      echo 'ERR';
	      exit(1);
	}
	// Get payment_status
  	if($params['x_response_code'] == 1)
  	{
    	$paymentStatus = 'okay';
  	}
	else 
	{
		// Gateway was not supported
	      file_put_contents($silentPostLogFile,
	          date('c') . ': ' .
	          'Payments processing failed.',
	          FILE_APPEND);
	      echo 'ERR';
	      exit(1);
	}
	
    try 
    {
      // Get gateway
      $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      $activeGateway = $gatewayTable->fetchRow(array('enabled = ?' => 1, 'title = ?' => 'Authorize.Net'));
    } 
    catch( Exception $e ) 
    {
      // Gateway detection failed
      file_put_contents($silentPostLogFile,
          date('c') . ': ' .
          'Gateway detection failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Gateway could not be detected
    if( !$activeGateway ) 
    {
      file_put_contents($silentPostLogFile,
          date('c') . ': ' .
          'Active gateway could not be detected.',
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }
	
    // Process
    try 
    {
    	$ynsubscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'ynpayment');
    	$ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
		$transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');

	    // Fetch by x_subscription_id
	    if( !empty($params['x_subscription_id']) && isset($params['x_subscription_paynum']) ) 
	    {
	      $ynsubscription = $ynsubscriptionTable->fetchRow(array(
	        'gateway_id = ?' => $activeGateway -> gateway_id,
	        'getaway_subscription_id = ?' => $params['x_subscription_id'],
	      ));
		  if( !$ynsubscription ) 
		  {
		      file_put_contents($silentPostLogFile,
		          date('c') . ': ' .
		          'Subscription could not be detected.',
		          FILE_APPEND);
		      echo 'ERR';
		      exit(1);
		  }
	    }
		else 
		{
			file_put_contents($silentPostLogFile,
		          date('c') . ': ' .
		          'Subscription could not be detected.',
		          FILE_APPEND);
		      echo 'ERR';
		      exit(1);
		}

    	// Process generic Silent Post Responce data ------------------------------------------------
     	// Insert transaction
		$transactionsTable -> insert(array(
			'user_id' => $ynsubscription -> user_id, 
			'gateway_id' => $activeGateway -> gateway_id, 
			'timestamp' => new Zend_Db_Expr('NOW()'), 
			'order_id' => $ynsubscription -> order_id, 
			'type' => 'payment', 
			'state' => $paymentStatus, 
			'gateway_transaction_id' => $params['x_trans_id'], 
			'amount' => $params['x_amount'], // @todo use this or gross (-fee)?
			'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency')));
			
		//Update user
		$order = $ordersTable->find($ynsubscription -> order_id)->current();
		$subscription = $order->getSource();
		$subscription->onPaymentSuccess();
    } catch( Exception $e ) {
      // Silent post validation failed
      file_put_contents($silentPostLogFile,
          date('c') . ': ' .
          'Processing failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Exit
    echo 'OK';
    exit(0);
  }
}