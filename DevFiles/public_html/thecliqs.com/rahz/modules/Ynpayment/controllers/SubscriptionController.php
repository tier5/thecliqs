<?php
/**
 *
 * @author MinhNC
 */
class Ynpayment_SubscriptionController extends Core_Controller_Action_Standard {
	/**
	 * @var User_Model_User
	 */
	protected $_user;

	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;

	/**
	 * @var Payment_Model_Order
	 */
	protected $_order;

	/**
	 * @var Payment_Model_Gateway
	 */
	protected $_gateway;

	/**
	 * @var Payment_Model_Subscription
	 */
	protected $_subscription;

	/**
	 * @var Payment_Model_Package
	 */
	protected $_package;

	public function init() {
		
		$params = $this -> _getAllParams();
		$isCallback = FALSE;
		// check callback
		if (isset($params['action']) && in_array($params['action'], array('skrill-callback', 'stripe-callback', 'ccbill-callback', 'heidelpay-callback', 'heidelpay-registration', 'heidelpay-recurring', 'braintree-callback'))) {
			$isCallback = TRUE;
		}
		// If there are no enabled gateways or packages, disable
		if (Engine_Api::_() -> getDbtable('gateways', 'payment') -> getEnabledGatewayCount() <= 0 || Engine_Api::_() -> getDbtable('packages', 'payment') -> getEnabledNonFreePackageCount() <= 0) {
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}
		// Get user and session
		$this -> _user = Engine_Api::_() -> user() -> getViewer();
		$this -> _session = new Zend_Session_Namespace('Payment_Subscription');

		// Check viewer and user
		if ((!$this -> _user || !$this -> _user -> getIdentity()) && !$isCallback) {
			if (!empty($this -> _session -> user_id)) {
				$this -> _user = Engine_Api::_() -> getItem('user', $this -> _session -> user_id);
			}
			// If no user, redirect to home?
			if (!$this -> _user || !$this -> _user -> getIdentity()) {
				$this -> _session -> unsetAll();
				return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
			}
		}
		
	}

	public function indexAction() {
		return $this -> _forward('choose');
	}

	public function chooseAction() {
		// Check subscription status
		// Unset certain keys
		unset($this -> _session -> package_id);
		unset($this -> _session -> subscription_id);
		unset($this -> _session -> gateway_id);
		unset($this -> _session -> order_id);
		unset($this -> _session -> errorMessage);

		// Check for default plan
		$this -> _checkDefaultPaymentPlan();

		// Make form
		$this -> view -> form = $form = new Payment_Form_Signup_Subscription( array('isSignup' => false, 'action' => $this -> view -> url(), ));

		// Check method/valid
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Get package
		if (!($packageId = $this -> _getParam('package_id', $this -> _session -> package_id)) || !($package = Engine_Api::_() -> getItem('payment_package', $packageId))) {
			return;
		}
		$this -> view -> package = $package;

		// Process
		$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
		$user = $this -> _user;
		$currentSubscription = $subscriptionsTable -> fetchRow(array('user_id = ?' => $user -> getIdentity(), 'active = ?' => true, ));

		// Cancel any other existing subscriptions
		Engine_Api::_() -> getDbtable('subscriptions', 'payment') -> cancelAll($user, 'User cancelled the subscription.', $currentSubscription);

		// Insert the new temporary subscription
		$db = $subscriptionsTable -> getAdapter();
		$db -> beginTransaction();

		try {
			$subscription = $subscriptionsTable -> createRow();
			$subscription -> setFromArray(array('package_id' => $package -> package_id, 'user_id' => $user -> getIdentity(), 'status' => 'initial', 'active' => false, // Will set to active on payment success
			'creation_date' => new Zend_Db_Expr('NOW()'), ));
			$subscription -> save();

			// If the package is free, let's set it active now and cancel the other
			if ($package -> isFree()) {
				$subscription -> setActive(true);
				$subscription -> onPaymentSuccess();
				if ($currentSubscription) {
					$currentSubscription -> cancel();
				}
			}
			$subscription_id = $subscription -> subscription_id;
			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> _session -> subscription_id = $subscription_id;

		// Check if the user is good (this will happen if they choose a free plan)
		$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
		if ($package -> isFree() && $subscriptionsTable -> check($this -> _user)) {
			return $this -> _finishPayment($package -> isFree() ? 'free' : 'active');
		}

		// Otherwise redirect to the payment page
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
	}

	public function gatewayAction() {
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}
		$this -> view -> subscription = $subscription;

		// Check subscription status
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}

		// Get subscription
		if (!$this -> _user || !($subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id)) || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId)) || $subscription -> user_id != $this -> _user -> getIdentity() || !($package = Engine_Api::_() -> getItem('payment_package', $subscription -> package_id))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}
		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Unset certain keys
		unset($this -> _session -> gateway_id);
		unset($this -> _session -> order_id);

		// Gateways
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
		$gateways = $gatewayTable -> fetchAll($gatewaySelect);

		$gatewayPlugins = array();
		foreach ($gateways as $gateway) {
			// Check billing cycle support
			if (!$package -> isOneTime()) {
				$sbc = $gateway -> getGateway() -> getSupportedBillingCycles();
				if (!in_array($package -> recurrence_type, array_map('strtolower', $sbc))) {
					continue;
				}
			}
			$gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway(), );
		}
		$this -> view -> gateways = $gatewayPlugins;
	}

	public function authorizeProcessAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId);
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		$this -> view -> form = $form = new Ynpayment_Form_Payment();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		if (!preg_match("/^\([0-9]{3}\)[0-9]{3}-[0-9]{4}$/", $posts['phone'])) {
			$form -> phone -> addError($this -> view -> translate("Please enter a valid phone number!"));
			return;
		}

		$values = $this -> _getAllParams();
		$values['total'] = $package -> price;
		switch ($gateway -> title) {
			case 'Authorize.Net' :
				$athorizeAIM = new Ynpayment_Api_AuthorizeNetAIM();
				$athorizeAIM -> initialize($gateway, (array)$values);
				$resp = $athorizeAIM -> process_payment();
				if ((isset($resp['failed']) && $resp['failed']) || (isset($resp['error_message']) && $resp['error_message'])) {
					if (!empty($resp['error_message'])) {
						$form -> addError($resp['error_message']);
					} else {
						$form -> addElement('Dummy', 'foo', array('order' => -999, 'content' => '<div class="bill_error">' . $this -> view -> translate("There has been a problem with your transaction. Please verify your payment details and try again.") . ' <br/>' . $this -> view -> translate("If the problem persists, please try a different credit card or <a href='%s'>Contact us</a>", Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'core', 'controller' => 'help', 'action' => 'contact'), 'default', true)) . '</div>'));
					}
					return;
				} else {
					// Unset certain keys
					unset($this -> _session -> package_id);
					unset($this -> _session -> subscription_id);
					unset($this -> _session -> gateway_id);

					//Check one time
					if (!$package -> isOneTime()) {
						//Create Ynpayment Subscription
						$athorizeARB = new Ynpayment_Api_AuthorizeNetARB();
						$athorizeARB -> initialize($gateway, (array)$values);
						$subscriptionId = $athorizeARB -> create_subscription($package);
						if ($subscriptionId) {
							$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'ynpayment');
							$db = $subscriptionsTable -> getAdapter();
							$db -> beginTransaction();
							try {
								$subscription = $subscriptionsTable -> createRow();
								$subscription -> setFromArray(array('getaway_subscription_id' => $subscriptionId, 'gateway_id' => $gatewayId, 'package_id' => $package -> package_id, 'order_id' => $order_id, 'user_id' => $this -> _user -> getIdentity(), 'creation_date' => new Zend_Db_Expr('NOW()'), ));
								$subscription -> save();
								$db -> commit();
							} catch( Exception $e ) {
								$db -> rollBack();
								throw $e;
							}
						}
					}
					return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $order_id), $resp));
				}
				break;
			case 'iTransact' :
				$iTransact = new Ynpayment_Api_ITransact();
				$iTransact -> initialize($gateway, (array)$values);
				$resp = $iTransact -> process_payment($package);
				if ((isset($resp['failed']) && $resp['failed']) || (isset($resp['error_message']) && $resp['error_message'])) {
					if (!empty($resp['error_message'])) {
						$form -> addError($resp['error_message']);
					} else {
						$form -> addElement('Dummy', 'foo', array('order' => -999, 'content' => '<div class="bill_error">' . $this -> view -> translate("There has been a problem with your transaction. Please verify your payment details and try again.") . ' <br/>' . $this -> view -> translate("If the problem persists, please try a different credit card or <a href='%s'>Contact us</a>", Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('module' => 'core', 'controller' => 'help', 'action' => 'contact'), 'default', true)) . '</div>'));
					}
					return;
				} else {
					// Unset certain keys
					unset($this -> _session -> package_id);
					unset($this -> _session -> subscription_id);
					unset($this -> _session -> gateway_id);

					return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $order_id), $resp));
				}
				break;
		}
	}

	public function processAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		if ($gateway -> title == "Authorize.Net" || $gateway -> title == "iTransact") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'authorize-process', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		}
		//CCBill
		else if ($gateway -> title == "CCBill") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'ccbill', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "Skrill") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'skrill', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "WebMoney") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'webmoney', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "BitPay") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'bitpay', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "Stripe") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'stripe', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "HeidelPay") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'heidelpay', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		} else if ($gateway -> title == "Braintree") {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'braintree', 'gateway_id' => $gatewayId), 'ynpayment_subscription', true);
		}
		$this -> view -> gateway = $gateway;

		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}
		$this -> view -> subscription = $subscription;

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		// Process
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id, ));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		// Unset certain keys
		unset($this -> _session -> package_id);
		unset($this -> _session -> subscription_id);
		unset($this -> _session -> gateway_id);

		// Get gateway plugin
		$this -> view -> gatewayPlugin = $gatewayPlugin = $gateway -> getGateway();
		$plugin = $gateway -> getPlugin();

		// Prepare host info
		$schema = 'http://';
		if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
			$schema = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];

		// Prepare transaction
		$params = array();
		$params['language'] = $this -> _user -> language;
		$localeParts = explode('_', $this -> _user -> language);
		if (count($localeParts) > 1) {
			$params['region'] = $localeParts[1];
		}
		$params['vendor_order_id'] = $order_id;
		$params['return_url'] = $schema . $host . $this -> view -> url(array('action' => 'return')) . '?order_id=' . $order_id . '&state=' . 'return';
		$params['cancel_url'] = $schema . $host . $this -> view -> url(array('action' => 'return')) . '?order_id=' . $order_id . '&state=' . 'cancel';
		$params['ipn_url'] = $schema . $host . $this -> view -> url(array('action' => 'index', 'controller' => 'ipn')) . '?order_id=' . $order_id;

		// Process transaction
		$transaction = $plugin -> createSubscriptionTransaction($this -> _user, $subscription, $package, $params);

		// Pull transaction params
		$this -> view -> transactionUrl = $transactionUrl = $gatewayPlugin -> getGatewayUrl();
		$this -> view -> transactionMethod = $transactionMethod = $gatewayPlugin -> getGatewayMethod();
		$this -> view -> transactionData = $transactionData = $transaction -> getData();

		// Handle redirection
		if ($transactionMethod == 'GET') {
			$transactionUrl .= '?' . http_build_query($transactionData);
			return $this -> _helper -> redirector -> gotoUrl($transactionUrl, array('prependBase' => false));
		}

		// Post will be handled by the view script
	}

	public function returnAction() {
		// Get order
		if (!$this -> _user || !($orderId = $this -> _getParam('order_id', $this -> _session -> order_id)) || !($order = Engine_Api::_() -> getItem('payment_order', $orderId)) || $order -> user_id != $this -> _user -> getIdentity() || $order -> source_type != 'payment_subscription' || !($subscription = $order -> getSource()) || !($package = $subscription -> getPackage()) || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id))) {
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}
		// Get gateway plugin
		$this -> view -> gatewayPlugin = $gatewayPlugin = $gateway -> getGateway();
		$plugin = $gateway -> getPlugin();
		// Process return
		unset($this -> _session -> errorMessage);
		try {
			$status = $plugin -> onSubscriptionTransactionReturn($order, $this -> _getAllParams());
		} catch( Payment_Model_Exception $e ) {
			$status = 'failure';
			$this -> _session -> errorMessage = $e -> getMessage();
		}

		return $this -> _finishPayment($status);
	}

	public function finishAction() {
		$this -> view -> status = $status = $this -> _getParam('state');
		$this -> view -> error = $this -> _session -> errorMessage;
	}

	protected function _checkSubscriptionStatus(Zend_Db_Table_Row_Abstract $subscription = null) {
		if (!$this -> _user) {
			return false;
		}

		if (null === $subscription) {
			$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
			$subscription = $subscriptionsTable -> fetchRow(array('user_id = ?' => $this -> _user -> getIdentity(), 'active = ?' => true, ));
		}

		if (!$subscription) {
			return false;
		}

		if ($subscription -> status == 'active' || $subscription -> status == 'trial') {
			if (!$subscription -> getPackage() -> isFree()) {
				$this -> _finishPayment('active');
			} else {
				$this -> _finishPayment('free');
			}
			return true;
		} else if ($subscription -> status == 'pending') {
			$this -> _finishPayment('pending');
			return true;
		}

		return false;
	}

	protected function _finishPayment($state = 'active') {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user = $this -> _user;

		// No user?
		if (!$this -> _user) {
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}

		// Log the user in, if they aren't already
		if (($state == 'active' || $state == 'free') && $this -> _user && !$this -> _user -> isSelf($viewer) && !$viewer -> getIdentity()) {
			Zend_Auth::getInstance() -> getStorage() -> write($this -> _user -> getIdentity());
			Engine_Api::_() -> user() -> setViewer();
			$viewer = $this -> _user;
		}

		// Handle email verification or pending approval
		if ($viewer -> getIdentity() && !$viewer -> enabled) {
			Engine_Api::_() -> user() -> setViewer(null);
			Engine_Api::_() -> user() -> getAuth() -> getStorage() -> clear();

			$confirmSession = new Zend_Session_Namespace('Signup_Confirm');
			$confirmSession -> approved = $viewer -> approved;
			$confirmSession -> verified = $viewer -> verified;
			$confirmSession -> enabled = $viewer -> enabled;
			return $this -> _helper -> _redirector -> gotoRoute(array('action' => 'confirm'), 'user_signup', true);
		}

		// Clear session
		$errorMessage = $this -> _session -> errorMessage;
		$userIdentity = $this -> _session -> user_id;
		$this -> _session -> unsetAll();
		$this -> _session -> user_id = $userIdentity;
		$this -> _session -> errorMessage = $errorMessage;

		// Redirect
		if ($state == 'free') {
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		} else {
			$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'finish',
						        'state' => $state,
								), 'ynpayment_subscription', true);
			return $this->_redirect($url);
		}
	}

	protected function _checkDefaultPaymentPlan() {
		// No user?
		if (!$this -> _user) {
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}

		// Handle default payment plan
		try {
			$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
			if ($subscriptionsTable) {
				$subscription = $subscriptionsTable -> activateDefaultPlan($this -> _user);
				if ($subscription) {
					return $this -> _finishPayment('free');
				}
			}
		} catch( Exception $e ) {
			// Silence
		}

		// Fall-through
	}

	public function skrillAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId);
		//Initial variables
		$pay_to_email = "";
		$return_url = "";
		$status_url = "";
		$language = "";
		$detail1_description = "";
		$detail1_text = "";

		// Languages
		$translate = Zend_Registry::get('Zend_Translate');
		$languageList = $translate -> getList();

		$defaultLanguage = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.locale.locale', 'en');
		if (!in_array($defaultLanguage, $languageList)) {
			if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
				$defaultLanguage = 'en';
			} else {
				$defaultLanguage = null;
			}
		}

		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);

		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}

		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		$settings = (array)$gateway -> config;
		if ($settings) {
			$pay_to_email = $settings['skrill_pay_to_email'];
			$return_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'skrill-return', ), 'ynpayment_subscription', true) . '/order_id/' . $order_id;
			$status_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'skrill-callback', ), 'ynpayment_subscription', true);
			$language = $defaultLanguage;
			$detail1_description = $package -> title;
			$detail1_text = $package -> getPackageDescription();
		}
		$amount = $package -> price;
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');

		if (!$package -> isOneTime()) {
			$length = 0;
			$recurrence = $package -> recurrence;
			$formRecurringPrice = $package -> price;
			switch ($package -> recurrence_type) {
				case 'day' :
					$length = $recurrence;
					break;
				case 'week' :
					$length = $recurrence * 7;
					break;
				case 'month' :
					$length = $recurrence * 30;
					break;
				case 'year' :
					$length = $recurrence * 30 * 12;
					break;
			}
			$rec_amount = $package -> price;
			$rec_period = $length;
			$formRebills = $package -> duration;

			//Prepare data
			$data = array("pay_to_email" => $pay_to_email, "transaction_id" => $order_id, "return_url" => $return_url, "status_url" => $status_url, "language" => $language, "rec_amount" => $rec_amount, "rec_period" => '1', "currency" => $currency, "detail1_description" => $detail1_description, "detail1_text" => $detail1_text, );

		} else {
			//Prepare data
			$data = array("pay_to_email" => $pay_to_email, "transaction_id" => $order_id, "return_url" => $return_url, "status_url" => $status_url, "language" => $language, "amount" => $amount, "currency" => $currency, "detail1_description" => $detail1_description, "detail1_text" => $detail1_text, );
		}
		$this -> view -> transactionUrl = 'https://www.moneybookers.com/app/payment.pl';
		$this -> view -> transactionData = $data;
	}

	public function skrillCallbackAction() {
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/skrillCallback.log';
		$logFile1 = APPLICATION_PATH . '/temporary/log/result.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'Skrill'));

		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}

		$status = $params['status'];

		$settings = (array)$activeGateway -> config;

		$secret = "";
		if ($settings) {
			$secret = $settings['skrill_secret'];
		}

		// validate
		$bVerified = "true";
		if (isset($params["md5sig"])) {
			$sValidateCode = $_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper(md5($secret)) . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];
			if ($status == "2") {
				//processed
				$paymentStatus = 'okay';
			} else if ($status == "-2") {
				//failed
				$bVerified = "false";
			}

			if (strtoupper(md5($sValidateCode)) != $params["md5sig"]) {
				$bVerified = "false";
			}

		} else {
			$bVerified = "false";
		}

		file_put_contents($logFile1, print_r($bVerified, true), FILE_APPEND);

		if ($bVerified === "true") {
			// Process
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			file_put_contents($logFile1, print_r($params['transaction_id'], true), FILE_APPEND);
			$select = $ordersTable -> select() -> where('order_id = ?', $params['transaction_id']) -> where("state = 'complete'") -> limit(1);
			if ($order = $ordersTable -> fetchRow($select)) {
				file_put_contents($logFile1, print_r('Recurring', true), FILE_APPEND);
				// Recurring
				// Insert transaction
				$transactionsTable -> insert(array('user_id' => $order -> user_id, //recheck
				'gateway_id' => $activeGateway -> gateway_id, 'timestamp' => new Zend_Db_Expr('NOW()'), 'order_id' => $params['transaction_id'], 'type' => 'payment', 'state' => $paymentStatus, 'gateway_transaction_id' => $params['mb_transaction_id'], 'amount' => $params['amount'], 'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency')));

				//Update user
				$subscription = $order -> getSource();
				$subscription -> onPaymentSuccess();
				exit(1);
			} else {
				file_put_contents($logFile1, print_r('first pay', true), FILE_APPEND);
				//first pay
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $params['mb_transaction_id'];
				$resp['amount'] = $params['mb_amount'];
				$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');

				$order = Engine_Api::_() -> getItem('payment_order', $params['transaction_id']);
				$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
				$gatewayPlugin = $gateway -> getGateway();
				$plugin = $gateway -> getPlugin();
				$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);

			}
		} else {
			//Fail
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
	}

	public function skrillReturnAction() {
		$params = $this -> _getAllParams();
		$order = Engine_Api::_() -> getItem('payment_order', $params['order_id']);
		if ($order -> state == 'complete') 
		{
			if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity()) 
			{
				Zend_Auth::getInstance() -> getStorage() -> write($order -> user_id);
				Engine_Api::_() -> user() -> setViewer();
			}
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'active'));
		} else {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}

	}

	public function ccbillAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId);
		//Initial variables
		$clientAccnum = "";
		$clientSubacc = "";
		$formName = "";
		$salt = "";
		$settings = (array)$gateway -> config;
		if ($settings) {
			$clientAccnum = $settings['ccbill_accnum'];
			$clientSubacc = $settings['ccbill_subaccnum'];
			$formName = $settings['ccbill_form_id'];
			$salt = $settings['ccbill_salt'];
		}

		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);

		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {

			return;
		}

		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		$formPrice = $package -> price;
		$formPeriod = '365';
		$aCurrency = array('USD' => "840", 'EUR' => '978', "AUD" => "036", "CAD" => "124", "GBP" => "826", "JPY" => "392");
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
		$currencyCode = $aCurrency[$currency];

		if (!$package -> isOneTime()) {
			//time for each billing
			$length = 0;
			$recurrence = $package -> recurrence;
			switch ($package -> recurrence_type) {
				case 'day' :
					$length = $recurrence;
					break;
				case 'week' :
					$length = $recurrence * 7;
					break;
				case 'month' :
					$length = $recurrence * 30;
					break;
				case 'year' :
					$length = $recurrence * 30 * 12;
					break;
			}
			$formRecurringPeriod = $length;

			//total time billing
			$length_duration = 0;
			$formRebills = 0;
			$duration = $package -> duration;
			switch ($package -> duration_type) {
				case 'week' :
					$length_duration = $duration * 7;
					break;
				case 'month' :
					$length_duration = $duration * 30;
					break;
				case 'year' :
					$length_duration = $duration * 30 * 12;
					break;
			}
			if ($duration == 0) {
				$formRebills = 99;
			} else {
				$formRebills = ceil($length_duration / $length);
			}

			$formPeriod = $formRecurringPeriod;

			//price
			$formRecurringPrice = $package -> price;

			//For recurring transactions
			$recurring = $formPrice . $formPeriod . $formRecurringPrice . $formRecurringPeriod . $formRebills . $currencyCode . $salt;

			//GENERATING THE MD5 HASH
			$formDigest = md5($recurring);

			//Prepare data
			$data = array("clientAccnum" => $clientAccnum, "clientSubacc" => $clientSubacc, "formName" => $formName, "formPrice" => $formPrice, "orderId" => $order_id, "formPeriod" => $formPeriod, "formRecurringPrice" => $formRecurringPrice, "formRecurringPeriod" => $formRecurringPeriod, "currencyCode" => $currencyCode, "formRebills" => $formRebills, "formDigest" => $formDigest, );

		} else {
			//For single billing transactions
			$single_billing = $formPrice . $formPeriod . $currencyCode . $salt;
			//GENERATING THE MD5 HASH
			$formDigest = md5($single_billing);

			//Prepare data
			$data = array("clientAccnum" => $clientAccnum, "clientSubacc" => $clientSubacc, "formName" => $formName, "formPrice" => $formPrice, "orderId" => $order_id, "formPeriod" => $formPeriod, "currencyCode" => $currencyCode, "formDigest" => $formDigest, );
		}
		$this -> view -> transactionUrl = 'https://bill.ccbill.com/jpost/signup.cgi';
		$this -> view -> transactionData = $data;
	}

	public function ccbillCallbackAction() 
	{
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/ccbillCallback.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		if(isset($params['moduleName']) && $params['moduleName'])
		{
			return $this -> _forward('ccbillCallback', 'pay');
		}
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'CCBill'));

		if (!$activeGateway) 
		{
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$status = $params['status'];
		$settings = (array)$activeGateway -> config;
		$salt = "";
		if ($settings) {
			$salt = $settings['ccbill_salt'];
		}
		// validate
		$bVerified = true;
		if (isset($params["responseDigest"])) {
			$sValidateCode = "";
			if ($status == "ccbill-success") {
				$paymentStatus = 'okay';
				$sValidateCode = md5($params["subscription_id"] . "1" . $salt);
			} else if ($status == "ccbill-fail") {
				$sValidateCode = md5($params["denialId"] . "0" . $salt);
			}
			file_put_contents($logFile, date('c') . ': ' . print_r($sValidateCode, true), FILE_APPEND);

			if ($sValidateCode != $params["responseDigest"]) {
				$bVerified = false;
			}

		} 
		else 
		{
			$bVerified = false;
		}
		if ($bVerified === true) 
		{
			// Process
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			$order_id = $params['orderId'];

			$select = $ordersTable -> select() -> where('order_id = ?', $order_id) -> where("state = 'complete'") -> limit(1);
			if ($order = $ordersTable -> fetchRow($select)) {
				// Recurring
				// Insert transaction
				$transactionsTable -> insert(array('user_id' => $order -> user_id, 'gateway_id' => $activeGateway -> gateway_id, 'timestamp' => new Zend_Db_Expr('NOW()'), 'order_id' => $order -> order_id, 'type' => 'payment', 'state' => $paymentStatus, 'gateway_transaction_id' => $params['subscription_id'], 'amount' => $params['initialPrice'], // @todo use this or gross (-fee)?
				'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency')));

				//Update user
				$subscription = $order -> getSource();
				$subscription -> onPaymentSuccess();
				exit(1);
			} else {
				//first pay
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $params['subscription_id'];
				$resp['amount'] = $params['initialPrice'];
				$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
				
				$order = Engine_Api::_() -> getItem('payment_order', $params['orderId']);
				$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
				$gatewayPlugin = $gateway -> getGateway();
				$plugin = $gateway -> getPlugin();
				$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
				
				exit(1);
			}
		} else {
			//Fail
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
	}

	public function webmoneyAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		//Initial variables
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);

		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$LMI_PAYEE_PURSE = "";
		$LMI_SUCCESS_URL = "";
		$LMI_FAIL_URL = "";
		$LMI_PAYMENT_DESC = "";

		$settings = (array)$gateway -> config;
		if ($settings) {
			$LMI_PAYEE_PURSE = $settings['wm_payee_purse'];
			$LMI_SUCCESS_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'webmoney-callback', ), 'ynpayment_subscription', true);
			$LMI_SUCCESS_METHOD = '2';
			$LMI_FAIL_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'webmoney-callback', ), 'ynpayment_subscription', true);
			$LMI_FAIL_METHOD = '2';
			$LMI_PAYMENT_DESC = $package -> title . ' (' . $package -> getPackageDescription() . ')';
		}

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {

			return;
		}

		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();
		$LMI_PAYMENT_AMOUNT = $package -> price;
		
		//Prepare data
		$data = array("LMI_PAYEE_PURSE" => $LMI_PAYEE_PURSE, "LMI_PAYMENT_NO" => $order_id, "LMI_PAYMENT_AMOUNT" => $LMI_PAYMENT_AMOUNT, "LMI_SUCCESS_URL" => $LMI_SUCCESS_URL, "LMI_SUCCESS_METHOD" => $LMI_SUCCESS_METHOD, "LMI_FAIL_URL" => $LMI_FAIL_URL, "LMI_FAIL_METHOD" => $LMI_FAIL_METHOD, "LMI_PAYMENT_DESC" => $LMI_PAYMENT_DESC, "LMI_PAYMENT_DESC" => $LMI_PAYMENT_DESC, );
		$this -> view -> transactionUrl = 'https://merchant.wmtransfer.com/lmi/payment.asp';
		$this -> view -> transactionData = $data;
	}

	public function webmoneyCallbackAction() {
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/webmoneyCallback.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'WebMoney'));

		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}

		// validate
		$bVerified = true;
		if (isset($params['LMI_PAYMENT_NO'])) {
			if (!empty($params['LMI_SYS_TRANS_NO'])) {
				//success
				$paymentStatus = 'okay';
			} else {
				//fail
				$bVerified = false;
			}
		} else {
			$bVerified = false;
		}
		if ($bVerified === true) {
			// Process
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			$order_id = $params['LMI_PAYMENT_NO'];

			if ($order = $ordersTable -> fetchRow(array('order_id' => $order_id, 'state' => 'complete'))) {
				// Recurring
				// Insert transaction
			} else {
				//first pay
				$subscription = $order -> getSource();
				$package = $subscription -> getPackage();
				$settings = (array)$activeGateway -> config;
				$currency = "";

				if ($settings) {
					$LMI_PAYEE_PURSE = $settings['wm_payee_purse'];
					$currency_char = substr($LMI_PAYEE_PURSE, 0, 1);
					switch ($currency_char) {
						case 'Z' :
							$currency = "USD";
							break;
						case 'E' :
							$currency = "EUR";
							break;
						case 'R' :
							$currency = "RUB";
							break;
						default :
							$currency = "USD";
							break;
					}

				}
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $params['LMI_SYS_TRANS_NO'];
				$resp['amount'] = $package -> price;
				$resp['currency'] = $currency;
				if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity()) 
				{
					Zend_Auth::getInstance() -> getStorage() -> write($order -> user_id);
					Engine_Api::_() -> user() -> setViewer();
				}
				return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $order_id), $resp));
			}
		} else {
			//Fail
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
	}

	public function bitpayAction() {
		// Get gateway

		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		$BitPay = new Ynpayment_Api_BitPay();
		$BitPay -> initialize($gateway);

		$response = $BitPay -> process_payment($package, $order_id);
		if(isset($response['error']) && $response['error'])
		{
			$this -> _session -> errorMessage = $response['error']['message'];
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
		elseif (isset($response['status']) && $response['status'] != "new") 
		{
			$this -> _session -> errorMessage = $response['error']['message'];
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
		else 
		{
			$this -> _redirect($response['url']);
		}
	}

	public function bitpayCallbackAction() {
		$params = $this -> _getAllParams();
		if ($params['status'] == "complete") {
			$order_id =  json_decode($params['posData']) -> orderId;
			$order = Engine_Api::_() -> getItem('payment_order', $order_id);
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['id'];
			$resp['amount'] = $params['price'];
			$resp['currency'] = $params['currency'];

			$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
			$gatewayPlugin = $gateway -> getGateway();
			$plugin = $gateway -> getPlugin();
			$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
		} else {
			exit(1);
		}
	}

	public function bitpayReturnAction() {
		$params = $this -> _getAllParams();
		$order_id =  json_decode($params['posData']) -> orderId;
		$order = Engine_Api::_() -> getItem('payment_order', $order_id);
		if ($order -> state == 'complete') {
			if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity()) 
			{
				Zend_Auth::getInstance() -> getStorage() -> write($order -> user_id);
				Engine_Api::_() -> user() -> setViewer();
			}
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'active'));
		} else {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
	}

	public function stripeAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		

		$settings = (array)$gateway -> config;
		$STRIPE_PUBLIC_KEY = "";
		$STRIPE_SECRET_KEY = "";
		if ($settings) {
			$STRIPE_PUBLIC_KEY = $settings['stripe_public_key'];
			$STRIPE_SECRET_KEY = $settings['stripe_secret_key'];
		}

		$this -> view -> STRIPE_PUBLIC_KEY = $STRIPE_PUBLIC_KEY;

		$this -> view -> form = $form = new Ynpayment_Form_Stripe();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();
		// Stores errors:
		$errors = array();

		// Need a payment token:
		if (isset($_POST['stripeToken'])) {

			$token = $_POST['stripeToken'];

			// Check for a duplicate submission, just in case:
			// Uses sessions, you could use a cookie instead.
			if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
				$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
				$form -> addError($errors['token']);
				return;
			} else {// New submission.
				$_SESSION['token'] = $token;
			}

		} else {
			$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
			$form -> addError($errors['token']);
			return;
		}

		// Set the order amount somehow:
		$amount = $package -> price * 100;
		//  in cents

		// Validate other form data!

		// If no errors, process the order:
		if (empty($errors)) {

			// create the charge on Stripe's servers - this will charge the user's card
			try {

				// set your secret key: remember to change this to your live secret key in production
				// see your keys here https://manage.stripe.com/account
				$Stripe = new Ynpayment_Api_Stripe_Stripe();
				$Stripe -> includeFiles();
				Stripe::setApiKey($STRIPE_SECRET_KEY);
				if (!$package -> isOneTime()) {
					$customer = Stripe_Customer::create(array("card" => $token, "plan" => $package -> package_id, "email" => $this -> _user -> email, "metadata" => array('order_id' => $order_id), ));
					$customer_info = Stripe_Customer::retrieve($customer -> id);
						$order_id = $customer_info -> metadata['order_id'];
						$select = $ordersTable -> select() -> where('order_id = ?', $order_id) -> limit(1);
						$order = $ordersTable -> fetchRow($select);
							if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity()) 
							{
								Zend_Auth::getInstance() -> getStorage() -> write($order -> user_id);
								Engine_Api::_() -> user() -> setViewer();
							}
							return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'active'));
				} else {
					// Charge the order:
					$charge = Stripe_Charge::create(array("amount" => $amount, // amount in cents, again
					"currency" => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency'), "card" => $token, "metadata" => array('order_id' => $order_id), ));
				}

				// Check that it was paid:
				if ($charge -> paid == true) {

					// Store the order in the database.
					// Send the email.
					// Celebrate!
					$resp['authorized'] = TRUE;
					$resp['transaction_id'] = $charge -> id;
					$resp['amount'] = $charge -> amount/100;
					$resp['currency'] = strtoupper($charge -> currency);
					if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity()) 
					{
						Zend_Auth::getInstance() -> getStorage() -> write($order -> user_id);
						Engine_Api::_() -> user() -> setViewer();
					}
					return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $charge -> metadata['order_id']), $resp));

				} else {// Charge was not paid!
					return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
				}

			} catch (Stripe_CardError $e) {
				// Card was declined.
				$e_json = $e -> getJsonBody();
				$err = $e_json['error'];
				$errors['stripe'] = $err['message'];
				$form -> addError($errors['stripe']);
				return;
			} catch (Stripe_ApiConnectionError $e) {
				// Network problem, perhaps try again.
				$form -> addError('Network problem, perhaps try again');
				return;
			} catch (Stripe_InvalidRequestError $e) {
				// You screwed up in your programming. Shouldn't happen!
				$form -> addError('You screwed up in your programming. Shouldn\'t happen!');
				return;
			} catch (Stripe_ApiError $e) {
				// Stripe's servers are down!
				$form -> addError('Stripe\'s servers are down!');
				return;
			} catch (Stripe_CardError $e) {
				// Something else that's not the customer's fault.
				$form -> addError('Something else that\'s not the customer\'s fault.');
				return;
			}

		} // A user form submission error occurred, handled below.
	}

	public function stripeCallbackAction() {
		$Stripe = new Ynpayment_Api_Stripe_Stripe();
		$Stripe -> includeFiles();
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'Stripe'));

		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$settings = (array)$activeGateway -> config;

		$STRIPE_SECRET_KEY = "";
		if ($settings) {
			$STRIPE_SECRET_KEY = $settings['stripe_secret_key'];
		}
		Stripe::setApiKey($STRIPE_SECRET_KEY);

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$params = json_decode($input);
		// Do something with $event_json

		if ($params -> type == "charge.succeeded") {
			$logFile1 = APPLICATION_PATH . '/temporary/log/result.log';
			file_put_contents($logFile1, print_r($params, true), FILE_APPEND);

			// Process
			$paymentStatus = 'okay';
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');

			$cus_id = $params -> data -> object -> customer;
			$customer_info = Stripe_Customer::retrieve($cus_id);
			$order_id = $customer_info -> metadata['order_id'];
			$select = $ordersTable -> select() -> where('order_id = ?', $order_id) -> where("state = 'complete'") -> limit(1);
			if ($order = $ordersTable -> fetchRow($select)) {
				// Recurring
				// Insert transaction
				$transactionsTable -> insert(array('user_id' => $order -> user_id, //recheck
				'gateway_id' => $activeGateway -> gateway_id, 'timestamp' => new Zend_Db_Expr('NOW()'), 'order_id' => $order_id, 'type' => 'payment', 'state' => $paymentStatus, 'gateway_transaction_id' => $params -> data -> object -> id, 'amount' => $params -> data -> object -> amount, 'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency')));

				//Update user
				$subscription = $order -> getSource();
				$subscription -> onPaymentSuccess();
				exit(1);
			} else {
				
				//first pay
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $params -> data -> object -> id;
				$resp['amount'] = $params -> data -> object -> amount/ 100;
				$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
				$order = Engine_Api::_() -> getItem('payment_order', $order_id);
				$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
				$gatewayPlugin = $gateway -> getGateway();
				$plugin = $gateway -> getPlugin();
				$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
			}

		}
		http_response_code(200);
		// PHP 5.4 or greater
	}
	
	public function heidelpayAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();

		$HeidelPay = new Ynpayment_Api_HeidelPay();
		$HeidelPay -> initialize($gateway);
		
		if ($package -> isOneTime())
		{
			$returnvalue = $HeidelPay -> process_payment($package, $order_id);
		}
		else 
		{
			//note: only support recurring monthly payment
			$recurrence = $package -> recurrence;
			if ($package -> recurrence_type == 'month' && $recurrence == 1) 
			{
				$returnvalue = $HeidelPay -> registration($package, $order_id);
			}
			else 
			{
				$returnvalue = $HeidelPay -> process_payment($package, $order_id);
			}
		}
		
		$processingresult=$returnvalue['POST.VALIDATION'];
		$redirectURL=$returnvalue['FRONTEND.REDIRECT_URL'];
		// everything ok, redirect to the WPF,
		if ($processingresult=="ACK")
		{
			if (strstr($redirectURL,"http")) // redirect url is returned ==> everything ok
			{
				header("Location: $redirectURL");
			}
			else // error-code is returned ... failure
			{
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
			}
		}// there is a connection-problem to the ctpe server ... redirect to error page (change the URL to YOUR error page)
			else
		{
				return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'));
		}
	}
	
	public function heidelpayRegistrationAction(){
		
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/recurring.log';
		file_put_contents($logFile, print_r($params, true), FILE_APPEND);
		
		if ($params['PROCESSING_RESULT'] == "ACK")
		{
			$order = Engine_Api::_() -> getItem('payment_order', $params['IDENTIFICATION_TRANSACTIONID']);
			if(!$order)
			{
				//Fail
				$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
							        'action' => 'finish',
							        'state' => 'failed',
									), 'ynpayment_subscription', true);
				return $this->_redirect($url);
			}
			
			$gatewayId = $order -> gateway_id;
			if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
				//Fail
				$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
							        'action' => 'gateway',
									), 'ynpayment_subscription', true);
				return $this->_redirect($url);
			}
			
			// Get subscription
			$subscription = $order -> getSource();
		
			// Get package
			$package = $subscription -> getPackage();
			if (!$package || $package -> isFree()) {
				//Fail
				$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
							        'action' => 'choose',
									), 'ynpayment_subscription', true);
				return $this->_redirect($url);
			}
		
			$this -> view -> subscription = $subscription;
			$this -> view -> package = $package;
		
			// Check subscription?
			if ($this -> _checkSubscriptionStatus($subscription)) {
				return;
			}
			
			$HeidelPay = new Ynpayment_Api_HeidelPay();
			$HeidelPay -> initialize($gateway);
			$returnvalue = $HeidelPay -> process_payment_recurring($package, $params);
			
			// everything ok, redirect to the WPF,
			if ($returnvalue['Result']=="ACK")
			{
				
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $returnvalue['UniqueID'];
				$resp['amount'] = $returnvalue['Amount'];
				$resp['currency'] = $returnvalue['Currency'];

				$order = Engine_Api::_() -> getItem('payment_order', $returnvalue['TransactionID']);
				$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
				$gatewayPlugin = $gateway -> getGateway();
				$plugin = $gateway -> getPlugin();
				$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
				return $this -> _finishPayment($status);
				 
			}// there is a connection-problem to the ctpe server ... redirect to error page (change the URL to YOUR error page)
			else
			{
				//Fail
				$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
							        'action' => 'finish',
							        'state' => 'failed',
									), 'ynpayment_subscription', true);
				return $this->_redirect($url);
			}
		}
		else 
		{
			//Fail
			$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'finish',
						        'state' => 'failed',
								), 'ynpayment_subscription', true);
			return $this->_redirect($url);
		}
	}
	
	public function heidelpayRecurringAction()
	{
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$params = $this -> _getAllParams();
		$data = file_get_contents("php://input");
		$logFile = APPLICATION_PATH . '/temporary/log/result.log';
		$logFile1 = APPLICATION_PATH . '/temporary/log/recurring.log';
		file_put_contents($logFile, print_r($params, true), FILE_APPEND);
		file_put_contents($logFile1, print_r($data, true), FILE_APPEND);
		
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'HeidelPay'));

		if (!$activeGateway) {
			return;
		}
		
		$HeidelPay =  new Ynpayment_Api_HeidelPay();
		$returnvalue = $HeidelPay -> _parse_return($data);
		if ($returnvalue['Result']=="ACK")
		{
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			$select = $ordersTable -> select() -> where('order_id = ?', $returnvalue['TransactionID']) -> where("state = 'complete'") -> limit(1);
			if ($order = $ordersTable -> fetchRow($select)) {
				// Recurring
				// Insert transaction
				$transactionsTable -> insert(array('user_id' => $order -> user_id, //recheck
				'gateway_id' => $activeGateway -> gateway_id, 'timestamp' => new Zend_Db_Expr('NOW()'), 'order_id' => $returnvalue['TransactionID'], 'type' => 'payment', 'state' => 'okay', 'gateway_transaction_id' => $returnvalue['UniqueID'], 'amount' => $returnvalue['Amount'], 'currency' => $returnvalue['Currency']));

				//Update user
				$subscription = $order -> getSource();
				$subscription -> onPaymentSuccess();
			}
		}
		
	}
	
	public function heidelpayCallbackAction() {
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$params = $this -> _getAllParams();
		$logFile1 = APPLICATION_PATH . '/temporary/log/result.log';
		file_put_contents($logFile1, print_r($params, true), FILE_APPEND);
		
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'HeidelPay'));

		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		
	
		if ($params['PROCESSING_RESULT'] == "ACK") {
			// Process
			
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['IDENTIFICATION_UNIQUEID'];
			$resp['amount'] = $params['PRESENTATION_AMOUNT'];
			$resp['currency'] = $params['PRESENTATION_CURRENCY'];

			$order = Engine_Api::_() -> getItem('payment_order', $params['IDENTIFICATION_TRANSACTIONID']);
			$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
			$gatewayPlugin = $gateway -> getGateway();
			$plugin = $gateway -> getPlugin();
			$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
			$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
		        'action' => 'finish',
		        'state' => $status,
				), 'ynpayment_subscription', true);
			echo $url;	
		} else {
			//Fail
			$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'finish',
						        'state' => 'failed',
								), 'ynpayment_subscription', true);
			echo $url;					
		}
	}

	public function braintreeAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		// Get subscription
		$subscriptionId = $this -> _getParam('subscription_id', $this -> _session -> subscription_id);
		if (!$subscriptionId || !($subscription = Engine_Api::_() -> getItem('payment_subscription', $subscriptionId))) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		// Get package
		$package = $subscription -> getPackage();
		if (!$package || $package -> isFree()) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'choose'));
		}

		$this -> view -> subscription = $subscription;
		$this -> view -> package = $package;

		// Check subscription?
		if ($this -> _checkSubscriptionStatus($subscription)) {
			return;
		}
		if ($package -> isOneTime()) {
			$this -> view -> form = $form = new Ynpayment_Form_Braintree();
		}
		else{
			$this -> view -> form = $form = new Ynpayment_Form_Braintree(array('onetime' => false));
		}
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())){
			return;
		}
		$values = $this->_getAllParams();
		//$values = $form -> getValues();
			//print_r($values);die;
		// Create order
		$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
		if (!empty($this -> _session -> order_id)) {
			$previousOrder = $ordersTable -> find($this -> _session -> order_id) -> current();
			if ($previousOrder && $previousOrder -> state == 'pending') {
				$previousOrder -> state = 'incomplete';
				$previousOrder -> save();
			}
		}
		$ordersTable -> insert(array('user_id' => $this -> _user -> getIdentity(), 'gateway_id' => $gateway -> gateway_id, 'state' => 'pending', 'creation_date' => new Zend_Db_Expr('NOW()'), 'source_type' => 'payment_subscription', 'source_id' => $subscription -> subscription_id));
		$this -> _session -> order_id = $order_id = $ordersTable -> getAdapter() -> lastInsertId();
		
		$Braintree = new Ynpayment_Api_Braintree_Braintree();
		$Braintree -> includeFiles();
		$settings = (array)$gateway -> config;
		
		$braintree_merchant_id = $settings['braintree_merchant_id'];
		$braintree_public_key = $settings['braintree_public_key'];
		$braintree_private_key = $settings['braintree_private_key'];
		$this -> view -> braintree_cse_key = $braintree_cse_key = $settings['braintree_cse_key']; 
		
		$braintree_webhook_url = $settings['braintree_webhook_url'];
		
		//check test_mode
		$test_mode = $gateway -> test_mode;
		if($test_mode == 1){
			$environment = "sandbox";
		}	
		else {
			$environment = "production";
		}
		
		Braintree_Configuration::environment($environment);
		Braintree_Configuration::merchantId($braintree_merchant_id);
		Braintree_Configuration::publicKey($braintree_public_key);
		Braintree_Configuration::privateKey($braintree_private_key);
		
		if ($package -> isOneTime()) {
			
			$amount = $package -> price;
			$result = Braintree_Transaction::sale(array(
			    "amount" => $package -> price,
			    'orderId' => $order_id,
			    "creditCard" => array(
			        "number" => $values["number"],
			        "cvv" => $values["cvv"],
			        "expirationMonth" => $values["month"],
			        "expirationYear" => $values["year"]
			    ),
			    "options" => array(
			        "submitForSettlement" => true
			    )
			));
			if ($result->success) {
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $result->transaction->id;
				$resp['amount'] = $result->transaction->amount;
				$resp['currency'] = strtoupper($result->transaction->currencyIsoCode);
				return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $result->transaction->orderId), $resp));
			} else if ($result->transaction) {
			    $form -> addError("Error: " . $result->message);
			    $form -> addError("Code: " . $result->transaction->processorResponseCode);
				return;
			} else {
			    $form -> addError("Validation errors:");
			    foreach (($result->errors->deepAll()) as $error) {
			        $form -> addError("- " . $error->message);
			    }
				return;
			}
		}	
		else{
			
			$result_customer = Braintree_Customer::create(array(
				"id" => $order_id, //use order_id represent for customer_id (for getting order_id later, in recurring payment)
			    "firstName" => $values["first_name"],
			    "lastName" => $values["last_name"],
			    "creditCard" => array(
			        "number" => $values["number"],
			        "expirationMonth" => $values["month"],
			        "expirationYear" => $values["year"],
			        "cvv" => $values["cvv"],
			        "billingAddress" => array(
			            "postalCode" => $values["postal_code"]
			        )
			    )
			));
			
			if ($result_customer->success) {
				
				//create customer successfully
				try {
					
					//pay recurring payment
				    $customer_id = $result_customer->customer->id;
				    $customer = Braintree_Customer::find($customer_id);
				    $payment_method_token = $customer->creditCards[0]->token;
				
				    $result_recurring = Braintree_Subscription::create(array(
				        'paymentMethodToken' => $payment_method_token,
				        'planId' => $package -> getIdentity()
				    ));
				
				    if ($result_recurring->success) {
				       //$form -> addError("Success! Subscription " . $result_recurring->subscription->id . " is " . $result_recurring->subscription->status);
					   if($result_recurring->subscription->status == 'Active'){
					   	   $state = 'active';
					   }		
					   else {
						   $state = 'failed';
					   }
					   return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => $state));
					} else {
				        $form -> addError("Validation errors:");
				        foreach (($result_recurring->errors->deepAll()) as $error) {
				            $form -> addError("- " . $error->message);
				        }
				    }
					
				} catch (Braintree_Exception_NotFound $e) {
				    $form -> addError("Failure: no customer found with ID " . $customer_id);
				}
				
			} else if ($result_customer->transaction) {
				
			    $form -> addError("Error: " . $result_customer->message);
			    $form -> addError("Code: " . $result_customer->transaction->processorResponseCode);
				return;
				
			} else {
				
			    $form -> addError("Validation errors:");
			    foreach (($result_customer->errors->deepAll()) as $error) {
			        $form -> addError("- " . $error->message);
			    }
				return;
			}
		}
	}
	
	public function braintreeCallbackAction() {
		// Get gateway
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'Braintree'));
		
		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$params = $this -> _getAllParams();
		
		$settings = (array)$activeGateway -> config;	
		$gateway = Engine_Api::_() -> getItem('payment_gateway', $activeGateway -> gateway_id);
		
		$Braintree = new Ynpayment_Api_Braintree_Braintree();
		$Braintree -> includeFiles();
		
		$braintree_merchant_id = $settings['braintree_merchant_id'];
		$braintree_public_key = $settings['braintree_public_key'];
		$braintree_private_key = $settings['braintree_private_key'];
		
		//check test_mode
		$test_mode = $gateway -> test_mode;
		if($test_mode == 1){
			$environment = "sandbox";
		}	
		else {
			$environment = "production";
		}
		
		Braintree_Configuration::environment($environment);
		Braintree_Configuration::merchantId($braintree_merchant_id);
		Braintree_Configuration::publicKey($braintree_public_key);
		Braintree_Configuration::privateKey($braintree_private_key);
		
		
		if(isset($params["bt_challenge"])) {
			//verify webhook url
		    echo(Braintree_WebhookNotification::verify($params["bt_challenge"]));
		}
		
		if(isset($params["bt_signature"]) && isset($params["bt_payload"])) 
		{
		    $webhookNotification = Braintree_WebhookNotification::parse(
		        $params["bt_signature"], $params["bt_payload"]
		    );
		   
		    // Process
			$paymentStatus = 'okay';
			$ordersTable = Engine_Api::_() -> getDbtable('orders', 'payment');
			$transactionsTable = Engine_Api::_() -> getDbtable('transactions', 'payment');
			
			$order_id = $webhookNotification -> subscription -> transactions[0] -> customer['id']; //order_id belong to this customer
			$select = $ordersTable -> select() -> where('order_id = ?', $order_id) -> where("state = 'complete'") -> limit(1);

			if ($order = $ordersTable -> fetchRow($select)) {
				// Recurring
				// Insert transaction
				$transactionsTable -> insert(array(
					'user_id' => $order -> user_id, //recheck
					'gateway_id' => $activeGateway -> gateway_id, 
					'timestamp' => new Zend_Db_Expr('NOW()'), 
					'order_id' => $order_id, 
					'type' => 'payment', 
					'state' => $paymentStatus, 
					'gateway_transaction_id' => $webhookNotification -> subscription -> transactions[0] -> id, 
					'amount' => $webhookNotification -> subscription -> transactions[0] -> amount,
					'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency')
				));

				//Update user
				$subscription = $order -> getSource();
				$subscription -> onPaymentSuccess();
				exit(1);
				
			} else {
				
				//first pay
				$resp['authorized'] = TRUE;
				$resp['transaction_id'] = $webhookNotification -> subscription -> transactions[0] -> id;
				$resp['amount'] = $webhookNotification -> subscription -> transactions[0] -> amount;
				$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
				$order = Engine_Api::_() -> getItem('payment_order', $order_id);
				$gateway = Engine_Api::_() -> getItem('payment_gateway', $order -> gateway_id);
				$gatewayPlugin = $gateway -> getGateway();
				$plugin = $gateway -> getPlugin();
				$status = $plugin -> onSubscriptionTransactionReturn($order, $resp);
			}
		}
	}
}
