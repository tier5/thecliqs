<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_PostBackController extends Core_Controller_Action_Standard
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
    $postBackLogFile = APPLICATION_PATH . '/temporary/log/payment-post-back.log';
    file_put_contents($postBackLogFile,
        date('c') . ': ' .
        print_r($params, true),
        FILE_APPEND);
	// check gateway supported
	if($gatewayType != 'iTransact')
	{
		// Gateway was not supported
	      file_put_contents($postBackLogFile,
	          date('c') . ': ' .
	          'Gateway was not supported.',
	          FILE_APPEND);
	      echo 'ERR';
	      exit(1);
	}
	// Get payment_status
  	if($params['status'] == 'ok')
  	{
    	$paymentStatus = 'okay';
  	}
	else 
	{
		// Gateway was not supported
	      file_put_contents($postBackLogFile,
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
      $activeGateway = $gatewayTable->fetchRow(array('enabled = ?' => 1, 'title = ?' => 'iTransact'));
    } 
    catch( Exception $e ) 
    {
      // Gateway detection failed
      file_put_contents($postBackLogFile,
          date('c') . ': ' .
          'Gateway detection failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Gateway could not be detected
    if( !$activeGateway ) 
    {
      file_put_contents($postBackLogFile,
          date('c') . ': ' .
          'Active gateway could not be detected.',
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }
	
    // Process
    try 
    {
    	$ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
		$transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');

	    // Fetch by orig_xid
	    if( !empty($params['orig_xid']) && !empty($params['xid'])) 
	    {
	      $transactions = $transactionsTable->fetchRow(array(
	        'gateway_id = ?' => $activeGateway -> gateway_id,
	        'gateway_transaction_id = ?' => $params['orig_xid'],
	      ));
		  if( !$transactions ) 
		  {
		      file_put_contents($postBackLogFile,
		          date('c') . ': ' .
		          'Transaction could not be detected.',
		          FILE_APPEND);
		      echo 'ERR';
		      exit(1);
		  }
	    }
		else 
		{
			file_put_contents($postBackLogFile,
		          date('c') . ': ' .
		          'Transaction could not be detected.',
		          FILE_APPEND);
		      echo 'ERR';
		      exit(1);
		}

    	// Process generic Silent Post Responce data ------------------------------------------------
     	// Insert transaction
		$transactionsTable -> insert(array(
			'user_id' => $transactions -> user_id, 
			'gateway_id' => $activeGateway -> gateway_id, 
			'timestamp' => new Zend_Db_Expr('NOW()'), 
			'order_id' => $transactions -> order_id, 
			'type' => 'payment', 
			'state' => $paymentStatus, 
			'gateway_transaction_id' => $params['xid'], 
			'gateway_parent_transaction_id' => $params['orig_xid'], 
			'amount' => $params['recur_total'], // @todo use this or gross (-fee)?
			'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency')));
			
		//Update user
		$order = $ordersTable->find($transactions -> order_id)->current();
		$subscription = $order->getSource();
		$subscription->onPaymentSuccess();
    } catch( Exception $e ) {
      // Silent post validation failed
      file_put_contents($postBackLogFile,
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