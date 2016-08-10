<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Plugin_Gateway_Braintree extends Engine_Payment_Plugin_Abstract 
{
	protected $_gatewayInfo;
	protected $_gateway;
	// General
	/**
	 * Constructor
	 */
	public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo) 
	{
		$this -> _gatewayInfo = $gatewayInfo;
		// @todo
	}

	/**
	 * Get the service API
	 *
	 * @return Engine_Service_PayPal
	 */
	public function getService() {
		return $this -> getGateway() -> getService();
	}

	/**
	 * Get the gateway object
	 *
	 * @return Engine_Payment_Gateway
	 */
	public function getGateway() {
		if (null === $this -> _gateway) {
			$class = 'Engine_Payment_Gateway_PayPal';
			Engine_Loader::loadClass($class);
			$gateway = new $class( array('config' => (array)$this -> _gatewayInfo -> config, 'testMode' => $this -> _gatewayInfo -> test_mode, 'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), ));
			if (!($gateway instanceof Engine_Payment_Gateway)) {
				throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
			}
			$this -> _gateway = $gateway;
		}

		return $this -> _gateway;
	}

	// Actions

	/**
	 * Create a transaction object from specified parameters
	 *
	 * @return Engine_Payment_Transaction
	 */
	public function createTransaction(array $params) {
		$transaction = new Engine_Payment_Transaction($params);
		$transaction -> process($this -> getGateway());
		return $transaction;
	}

	// SEv4 Specific

	/**
	 * Process return of subscription transaction
	 *
	 * @param Payment_Model_Order $order
	 * @param array $params
	 */
	public function onSubscriptionTransactionReturn(Payment_Model_Order $order, array $params = array()) 
	{
		// Check that gateways match
		if ($order -> gateway_id != $this -> _gatewayInfo -> gateway_id) 
		{
			throw new Engine_Payment_Plugin_Exception('Gateways do not match');
		}

		// Get related info
		$user = $order -> getUser();
		$subscription = $order -> getSource();
		$package = $subscription -> getPackage();

		// Check subscription state
		if ($subscription -> status == 'active' || $subscription -> status == 'trial')
		{
			return 'active';
		} 
		else if ($subscription -> status == 'pending') 
		{
			return 'pending';
		}
		// Update order with profile info and complete status?
		$order -> state = 'complete';
		$order -> gateway_transaction_id = $params['transaction_id'];
		$order -> save();

		// Insert transaction
		$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
		$transactionsTable -> insert(array(
			'user_id' => $order -> user_id, 
			'gateway_id' => $this -> _gatewayInfo -> gateway_id, 
			'timestamp' => new Zend_Db_Expr('NOW()'), 
			'order_id' => $order -> order_id, 
			'type' => 'payment', 
			'state' => 'okay', 
			'gateway_transaction_id' => $params['transaction_id'], 
			'amount' => $params['amount'], // @todo use this or gross (-fee)?
			'currency' => $params['currency']));

		// Get benefit setting
		$giveBenefit = Engine_Api::_() -> getDbtable('transactions', 'payment') -> getBenefitStatus($user);

		//YN - Random a code
		$sid = 'abcdefghiklmnopqstvxuyz0123456789ABCDEFGHIKLMNOPQSTVXUYZ';
		$max = strlen($sid) - 1;
		$rCode = "";
		for ($i = 0; $i < 16; ++$i) {
			$rCode .= $sid[mt_rand(0, $max)];
		}

		// Update subscription info
		$subscription -> gateway_id = $this -> _gatewayInfo -> gateway_id;
		$subscription -> gateway_profile_id = ($params['transaction_id']) ? $params['transaction_id'] : $rCode;

		$subscription -> payment_date = date('Y-m-d H:i:s');
		// Payment success
		$subscription -> onPaymentSuccess();

		// send notification
		if ($subscription -> didStatusChange()) {
			Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($user, 'payment_subscription_active', array('subscription_title' => $package -> title, 'subscription_description' => $package -> description, 'subscription_terms' => $package -> getPackageDescription(), 'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'user_login', true), ));
		}
		return 'active';
	}


	/**
	 * Cancel a subscription (i.e. disable the recurring payment profile)
	 *
	 * @params $transactionId
	 * @return Engine_Payment_Plugin_Abstract
	 */
	public function cancelSubscription($transactionId, $note = null) {
		$profileId = null;

		if ($transactionId instanceof Payment_Model_Subscription) {
			$package = $transactionId -> getPackage();
			if ($package -> isOneTime()) {
				return $this;
			}
			$profileId = $transactionId -> gateway_profile_id;
		} else if (is_string($transactionId)) {
			$profileId = $transactionId;
		} else {
			// Should we throw?
			return $this;
		}

		try {
			$r = $this -> getService() -> cancelRecurringPaymentsProfile($profileId, $note);
		} catch( Exception $e ) {
			// throw?
		}

		return $this;
	}

	/**
	 * Generate href to a page detailing the order
	 *
	 * @param string $transactionId
	 * @return string
	 */
	public function getOrderDetailLink($orderId) 
	{
		// @todo make sure this is correct
		return '';
	}

	/**
	 * Generate href to a page detailing the transaction
	 *
	 * @param string $transactionId
	 * @return string
	 */
	public function getTransactionDetailLink($transactionId) 
	{
		// @todo make sure this is correct
		return '';
	}

	/**
	 * Get raw data about an order or recurring payment profile
	 *
	 * @param string $orderId
	 * @return array
	 */
	public function getOrderDetails($orderId) {

		try {
			return $this -> getTransactionDetails($orderId);
		} catch( Exception $e ) {
			echo $e;
		}

		return false;
	}

	/**
	 * Get raw data about a transaction
	 *
	 * @param $transactionId
	 * @return array
	 */
	public function getTransactionDetails($transactionId) {
		return $this -> getService() -> detailTransaction($transactionId);
	}
	// Forms

	/**
	 * Get the admin form for editing the gateway info
	 *
	 * @return Engine_Form
	 */
	public function getAdminGatewayForm() {
		return new Ynpayment_Form_Admin_Gateway_Braintree();
	}

	public function processAdminGatewayForm(array $values) {
		return $values;
	}
	public function createSubscriptionTransaction(User_Model_User $user,
      Zend_Db_Table_Row_Abstract $subscription,
      Payment_Model_Package $package,
      array $params = array()){}
	public function onSubscriptionTransactionIpn(
      Payment_Model_Order $order,
      Engine_Payment_Ipn $ipn){}
	public function createIpn(array $params){}
	public function onIpn(Engine_Payment_Ipn $ipn){}
}
