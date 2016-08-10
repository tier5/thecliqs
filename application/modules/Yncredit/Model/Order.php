<?php
class Yncredit_Model_Order extends Core_Model_Item_Abstract
{
	protected $_type = 'yncredit_order';
	protected $_statusChanged;
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

		$api = Engine_Api::_() -> yncredit();
		$api -> buyCredits($buyer, $this -> credit, $this -> getGatewayTitle());

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
		$table = Engine_Api::_() -> getDbTable('transactions', 'yncredit');
		$select = $table -> select() -> setIntegrityCheck(false) -> from($table -> info('name'), 'transaction_id') -> where('gateway_transaction_id = ?', $this -> gateway_transaction_id) -> where('state = ?', 'okay');

		return (bool)$table -> fetchRow($select);
	}

	public function getSource()
	{
		$table = Engine_Api::_() -> getDbTable('packages', 'yncredit');
		$select = $table -> select() -> where('package_id = ?', $this -> package_id) -> limit(1);
		$row = $table -> fetchRow($select);
		return $row;
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

		// Update order with profile info and complete status?
		$this -> gateway_transaction_id = $params['transaction_id'];
		$this -> save();

		// Insert transaction
		$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
		$transactionsTable -> insert(array(
			'user_id' => $this -> user_id,
			'gateway_id' => $this -> gateway_id,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'order_id' => $this -> order_id,
			'type' => 'Buy Credits',
			'state' => 'okay',
			'gateway_transaction_id' => $params['transaction_id'],
			'amount' => (isset($params['amount'])?$params['amount']:$this -> price), // @todo use this or gross (-fee)?
			'currency' => $params['currency']
		));
		$this -> onPaymentSuccess();
		return 'completed';
	}
}
