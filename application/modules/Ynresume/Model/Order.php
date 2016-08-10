<?php
class Ynresume_Model_Order extends Core_Model_Item_Abstract
{
	protected $_searchTriggers = false;
	protected $_type = 'ynresume_order';
	protected $_statusChanged;
	
	public function getPackageParams($arr = array())
    {
        $params =array();
        $view = Zend_Registry::get('Zend_View');
        // General
        $params['name'] = $view -> translate('Buy Resume');
        $params['price'] = $arr['price'];
        $params['description'] = $view -> translate('Buy Resume from %s', $view -> layout() -> siteinfo['title']);
        $params['vendor_product_id'] = $this -> getGatewayIdentity($arr['user_id'], $arr['price']);
        $params['tangible'] = false;
        $params['recurring'] = false;
        return $params;
    }
    
    public function getGatewayIdentity($user_id = 0, $fee = 100)
    {
        return 'ynresume_' . $user_id . '_fee_' . $fee;
    }
	
	public function isOrderPending()
	{
		return ($this -> status == 'pending') ? true : false;
	}

	public function onPaymentPending()
	{
		$this -> _statusChanged = false;
		if (in_array($this -> status, array(
			'initial',
			'pending'
		)))
		{
			// Change status
			if ($this -> status != 'pending')
			{
				$this -> status = 'pending';
				$this -> _statusChanged = true;
			}
		}
		$this -> save();
		return $this;
	}

	public function onPaymentSuccess()
	{
		$this -> _statusChanged = false;
		$buyer = Engine_Api::_() -> getItem('user', $this -> user_id);

		// Change status
		if ($this -> status != 'completed')
		{
			$this -> status = 'completed';
			$this -> payment_date = new Zend_Db_Expr('NOW()');
			$this -> _statusChanged = true;
		}
		$this -> save();

		return $this;
	}

	public function onPaymentFailure()
	{
		$this -> _statusChanged = false;

		// Change status
		if ($this -> status != 'failed')
		{
			$this -> status = 'failed';
			$this -> payment_date = new Zend_Db_Expr('NOW()');
			$this -> _statusChanged = true;
		}
		$this -> save();

		return $this;
	}

	public function didStatusChange()
	{
		return $this -> _statusChanged;
	}

	public function cancel()
	{
		$this -> active = false;
		// Need to do this to prevent clearing the user's session
		$this -> onCancel();
		return $this;
	}

	public function onCancel()
	{
		$this -> _statusChanged = false;
		if (in_array($this -> status, array(
			'pending',
			'cancelled'
		)))
		{
			// Change status
			if ($this -> status != 'cancelled')
			{
				$this -> status = 'cancelled';
				$this -> _statusChanged = true;
			}
		}
		$this -> save();
		return $this;
	}

	public function isChecked()
	{
		if ($this -> status != 'completed')
			return false;
		$table = Engine_Api::_() -> getDbTable('transactions', 'ynresume');
		$select = $table -> select() -> setIntegrityCheck(false) -> from($table -> info('name'), 'transaction_id') -> where('gateway_transaction_id = ?', $this -> gateway_transaction_id) -> where('state = ?', 'okay');

		return (bool)$table -> fetchRow($select);
	}

	public function getSource()
	{
		return $this;
	}

	public function getUser()
	{
		return Engine_Api::_() -> getItem('user', $this -> user_id);
	}

	public function getGatewayTitle()
	{
		$gatewaysTable = Engine_Api::_() -> getDbTable('gateways', 'payment');
		$select = $gatewaysTable -> select() -> where('gateway_id = ?', $this -> gateway_id) -> limit(1);
		return $gatewaysTable -> fetchRow($select) -> title;
	}

	public function onPackageTransactionReturn(array $params = array())
	{
		// Get related info
		$user = $this -> getUser();
		$item = $this -> getSource();

		// Check order states
		if ($this -> status == 'completed')
		{
			return 'completed';
		}

		// Check for cancel state - the user cancelled the transaction
		if (isset($params['state']) && $params['state'] == 'cancel')
		{
			$this -> onCancel();
			// Error
			throw new Payment_Model_Exception('Your payment has been cancelled and ' . 'not been purchased. If this is not correct, please try again later.');
		}
		
		$service_day_number = $this -> service_day_number;
		$feature_day_number = $this -> feature_day_number;	
			
		 // Insert transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynresume');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	$description = "";
			$view = Zend_Registry::get('Zend_View');
			
			$description = "";
			if($service_day_number){
				Engine_Api::_() -> ynresume() -> serviceResume($this -> item_id, $this -> service_day_number);
				$description = $view ->translate(array("Register \"Who Viewed Me\" service for %s day", "Register \"Who Viewed Me\" service for %s days" , $this -> service_day_number), $this -> service_day_number);
			}
			elseif($feature_day_number) {
				Engine_Api::_() -> ynresume() -> featureResume($this -> item_id, $this -> feature_day_number);
				$description = $view ->translate(array("Feature resume for %s day", "Feature resume for %s days" , $this -> feature_day_number), $this -> feature_day_number);
			}
			
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => $this -> gateway_id,
		     	'amount' => $this->price,
		     	'currency' => $this->currency,
		     	'user_id' => $this->user_id,
		     	'item_id' => $this->item_id,
		     	'payment_transaction_id' => $params['transaction_id'],
		     	'description' => $description,
			 ));
			 
		    $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }

		// Insert transaction
		$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
		$transactionsTable -> insert(array(
			'user_id' => $this -> user_id,
			'gateway_id' => $this -> gateway_id,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'order_id' => $this -> order_id,
			'type' => 'Resume',
			'state' => 'okay',
			'gateway_transaction_id' => $params['transaction_id'],
			'amount' => (isset($params['amount'])?$params['amount']:$this -> price), // @todo use this or gross (-fee)?
			'currency' => $params['currency']
		));
		$this -> onPaymentSuccess();
		return 'completed';
	}
}
