<?php
/**
 *
 * @author MinhNC
 */
class Ynpayment_PayController extends Core_Controller_Action_Standard
{
	protected $_order;
	protected $_cancel_route;
	protected $_return_route;
	protected $_module;
	protected $_session;

	public function init()
	{
		$params = $this -> _getAllParams();
		$isCallback = FALSE;
		// check callback
		if (isset($params['action']) && in_array($params['action'], array('skrill-callback', 'ccbill-callback', 'heidelpay-callback'))) 
		{
			$isCallback = TRUE;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// If no user, redirect to home?
		if ((!$viewer || !$viewer -> getIdentity()) && !$isCallback)
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}
		$this -> _session = new Zend_Session_Namespace('Ynpayment_PayPackage');
		
		$this -> _module  = $moduleName = $this -> _getParam('m', $this -> _session -> module);
		$this -> _cancel_route = $this -> _getParam('cancel_route', $this -> _session -> cancel_route);
		$this -> _return_route = $this -> _getParam('return_route', $this -> _session -> return_route);
		
		$orderId = $this -> _getParam('order_id',$this -> _session -> order_id);
		if($moduleName && $orderId)
		{
			$this -> _order = Engine_Api::_() -> getDbTable('orders', $moduleName) -> findRow($orderId);
		}
	}

	public function processAdvancedAction()
	{
		unset($this -> _session -> module);
		unset($this -> _session -> cancel_route);
		unset($this -> _session -> return_route);
		unset($this -> _session -> order_id);
		
		$moduleName = $this -> _getParam('m', '');
		$this -> _cancel_route = $this -> _getParam('cancel_route', 'default');
		$this -> _return_route = $this -> _getParam('return_route', 'default');
		// Get order
		$orderId = $this -> _getParam('order_id', 0);
		if (!$moduleName || !$orderId)
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$this -> _module = $moduleName;
		$this -> _order = Engine_Api::_() -> getDbTable('orders', $moduleName) -> findRow($orderId);
		
		$this -> _session -> module = $moduleName;
		$this -> _session -> cancel_route = $this -> _cancel_route;
		$this -> _session -> return_route = $this -> _return_route;
		
		// If no product or product is empty, redirect to home?
		if (!$this -> _order || !$this -> _order -> getIdentity())
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$this -> _session -> order_id = $orderId;
		// Get gateway
		if (!($gateway = Engine_Api::_() -> getItem('payment_gateway', $this -> _order -> gateway_id)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$this -> _session -> gateway_id = $this -> _order -> gateway_id;
		if ($gateway -> title == "Authorize.Net" || $gateway -> title == "iTransact")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'authorize-process',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "CCBill")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'ccbill',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "Skrill")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'skrill',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "WebMoney")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'webmoney',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "BitPay")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'bitpay',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "Stripe")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'stripe',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "HeidelPay")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'heidelpay',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
		else
		if ($gateway -> title == "Braintree")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'braintree',
				'gateway_id' => $this -> _order -> gateway_id
			), 'ynpayment_paypackage', true);
		}
	}

	public function skrillAction()
	{
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
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
		if (!in_array($defaultLanguage, $languageList))
		{
			if ($defaultLanguage == 'auto' && isset($languageList['en']))
			{
				$defaultLanguage = 'en';
			}
			else
			{
				$defaultLanguage = null;
			}
		}
		$package = $this -> _order -> getSource();
		$settings = (array)$gateway -> config;
		if ($settings)
		{
			$pay_to_email = $settings['skrill_pay_to_email'];
			$return_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'skrill-return', ), 'ynpayment_paypackage', true) . '/order_id/' . $this -> _session -> order_id.'/m/'.$this -> _session -> module.'/return_route/'.$this -> _session -> return_route;
			$status_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'skrill-callback', ), 'ynpayment_paypackage', true). '/m/'.$this -> _session -> module;
			$language = $defaultLanguage;
			$detail1_description = $package -> getTitle();
			$detail1_text = $package -> getDescription();
		}
		$amount = $this -> _order -> price;
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');

		//Prepare data
		$data = array(
			"pay_to_email" => $pay_to_email,
			"transaction_id" => $this -> _session -> order_id,
			"return_url" => $return_url,
			"status_url" => $status_url,
			"language" => $language,
			"amount" => $amount,
			"currency" => $currency,
			"detail1_description" => $detail1_description,
			"detail1_text" => $detail1_text,
		);
		$this -> view -> transactionUrl = 'https://www.moneybookers.com/app/payment.pl';
		$this -> view -> transactionData = $data;
	}

	public function skrillCallbackAction()
	{
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/skrillCallback.log';
		$logFile1 = APPLICATION_PATH . '/temporary/log/result.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array(
			'enabled = ?' => 1,
			'title = ?' => 'Skrill'
		));
		if (!$activeGateway)
		{
			exit;
		}

		$status = $params['status'];
		$settings = (array)$activeGateway -> config;
		$secret = "";
		if ($settings)
		{
			$secret = $settings['skrill_secret'];
		}
		// validate
		$bVerified = "true";
		if (isset($params["md5sig"]))
		{
			$sValidateCode = $_POST['merchant_id'] . $_POST['transaction_id'] . strtoupper(md5($secret)) . $_POST['mb_amount'] . $_POST['mb_currency'] . $_POST['status'];
			if ($status == "2")
			{
				//processed
				$paymentStatus = 'okay';
			}
			else
			if ($status == "-2")
			{
				//failed
				$bVerified = "false";
			}

			if (strtoupper(md5($sValidateCode)) != $params["md5sig"])
			{
				$bVerified = "false";
			}
		}
		else
		{
			$bVerified = "false";
		}

		file_put_contents($logFile1, print_r($bVerified, true), FILE_APPEND);

		if ($bVerified === "true")
		{
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['mb_transaction_id'];
			$resp['amount'] = $params['mb_amount'];
			$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
			
			if(!empty($params['m']))
			{
				$orderTb = Engine_Api::_() -> getDbTable('orders', $params['m']);
				$order = $orderTb -> findRow($params['transaction_id']);
				$order -> onPackageTransactionReturn($resp);
			}
		}
		else
		{
			//Fail
			exit;
		}
	}

	public function skrillReturnAction()
	{
		$params = $this -> _getAllParams();
		$return_route = $params['return_route'];
		if(empty($params['m']))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
					'action' => 'finish',
					'state' => 'failed'
				), $return_route, true);
		}
		$orderTb = Engine_Api::_() -> getDbTable('orders', $params['m']);
		$order = $orderTb -> findRow($params['order_id']);
		$this -> _session -> unsetAll();
		if ($order -> status == 'completed')
		{
			// Redirect
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'completed',
				'order_id' => $params['order_id']
			), $return_route, true);
		}
		else
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'failed',
				'order_id' => $params['order_id']
			), $return_route, true);
		}

	}

	public function ccbillAction()
	{
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId);
		//Initial variables
		$clientAccnum = "";
		$clientSubacc = "";
		$formName = "";
		$salt = "";
		$settings = (array)$gateway -> config;
		if ($settings)
		{
			$clientAccnum = $settings['ccbill_accnum'];
			$clientSubacc = $settings['ccbill_subaccnum'];
			$formName = $settings['ccbill_form_id'];
			$salt = $settings['ccbill_salt'];
		}
		$package = $this -> _order -> getSource();
		$formPrice = $this -> _order -> price;
		$formPeriod = '365';
		$aCurrency = array(
			'USD' => "840",
			'EUR' => '978',
			"AUD" => "036",
			"CAD" => "124",
			"GBP" => "826",
			"JPY" => "392"
		);
		$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
		$currencyCode = $aCurrency[$currency];

		$single_billing = $formPrice . $formPeriod . $currencyCode . $salt;
		//GENERATING THE MD5 HASH
		$formDigest = md5($single_billing);

		//Prepare data
		$data = array(
			"clientAccnum" => $clientAccnum,
			"clientSubacc" => $clientSubacc,
			"formName" => $formName,
			"formPrice" => $formPrice,
			"orderId" => $this -> _session -> order_id,
			"moduleName" => $this -> _module,
			"formPeriod" => $formPeriod,
			"currencyCode" => $currencyCode,
			"formDigest" => $formDigest,
		);
		$this -> view -> transactionUrl = 'https://bill.ccbill.com/jpost/signup.cgi';
		$this -> view -> transactionData = $data;
	}

	public function ccbillCallbackAction()
	{
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/ccbillCallback.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array(
			'enabled = ?' => 1,
			'title = ?' => 'CCBill'
		));

		if (!$activeGateway)
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _session -> cancel_route, true);
		}
		$status = $params['status'];
		$settings = (array)$activeGateway -> config;
		$salt = "";
		if ($settings)
		{
			$salt = $settings['ccbill_salt'];
		}
		// validate
		$bVerified = true;
		if (isset($params["responseDigest"]))
		{
			$sValidateCode = "";
			if ($status == "ccbill-success")
			{
				$paymentStatus = 'okay';
				$sValidateCode = md5($params["subscription_id"] . "1" . $salt);
			}
			else
			if ($status == "ccbill-fail")
			{
				$sValidateCode = md5($params["denialId"] . "0" . $salt);
			}
			if ($sValidateCode != $params["responseDigest"])
			{
				$bVerified = false;
			}

		}
		else
		{
			$bVerified = false;
		}
		$return_route = $this -> _session -> return_route;
		if ($bVerified === true)
		{
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['subscription_id'];
			$resp['amount'] = $params['initialPrice'];
			$resp['currency'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
			
			if(!empty($params['moduleName']))
			{
				$orderTb = Engine_Api::_() -> getDbTable('orders', $params['moduleName']);
				$order = $orderTb -> findRow($params['orderId']);
				$order -> onPackageTransactionReturn($resp);
			}
			exit(1);
		}
		else
		{
			//Fail
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'failed'
			), $return_route, true);
		}
	}

	public function webmoneyAction()
	{
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		//Initial variables
		$LMI_PAYEE_PURSE = "";
		$LMI_SUCCESS_URL = "";
		$LMI_FAIL_URL = "";
		$LMI_PAYMENT_DESC = "";
		$package = $this -> _order -> getSource();
		$settings = (array)$gateway -> config;
		if ($settings)
		{
			$LMI_PAYEE_PURSE = $settings['wm_payee_purse'];
			$LMI_SUCCESS_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'webmoney-callback', ), 'ynpayment_paypackage', true);
			$LMI_SUCCESS_METHOD = '2';
			$LMI_FAIL_URL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'webmoney-callback', ), 'ynpayment_paypackage', true);
			$LMI_FAIL_METHOD = '2';
			$LMI_PAYMENT_DESC = $package -> getTitle() . ' (' . $package -> getDescription() . ')';
		}
		$LMI_PAYMENT_AMOUNT = $this -> _order -> price;

		//Prepare data
		$data = array(
			"LMI_PAYEE_PURSE" => $LMI_PAYEE_PURSE,
			"LMI_PAYMENT_NO" => $this -> _session -> order_id,
			"LMI_PAYMENT_AMOUNT" => $LMI_PAYMENT_AMOUNT,
			"LMI_SUCCESS_URL" => $LMI_SUCCESS_URL,
			"LMI_SUCCESS_METHOD" => $LMI_SUCCESS_METHOD,
			"LMI_FAIL_URL" => $LMI_FAIL_URL,
			"LMI_FAIL_METHOD" => $LMI_FAIL_METHOD,
			"LMI_PAYMENT_DESC" => $LMI_PAYMENT_DESC,
			"LMI_PAYMENT_DESC" => $LMI_PAYMENT_DESC,
		);
		$this -> view -> transactionUrl = 'https://merchant.wmtransfer.com/lmi/payment.asp';
		$this -> view -> transactionData = $data;
	}

	public function webmoneyCallbackAction()
	{
		$params = $this -> _getAllParams();
		$logFile = APPLICATION_PATH . '/temporary/log/webmoneyCallback.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array(
			'enabled = ?' => 1,
			'title = ?' => 'WebMoney'
		));

		if (!$activeGateway)
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _session -> cancel_route, true);
		}

		// validate
		$bVerified = true;
		if (isset($params['LMI_PAYMENT_NO']))
		{
			if (!empty($params['LMI_SYS_TRANS_NO']))
			{
				//success
				$paymentStatus = 'okay';
			}
			else
			{
				//fail
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
			$order_id = $params['LMI_PAYMENT_NO'];
			$settings = (array)$activeGateway -> config;
			$currency = "";

			if ($settings)
			{
				$LMI_PAYEE_PURSE = $settings['wm_payee_purse'];
				$currency_char = substr($LMI_PAYEE_PURSE, 0, 1);
				switch ($currency_char)
				{
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
			$resp['currency'] = $currency;
			return $this -> _helper -> redirector -> gotoRoute(array_merge(array(
				'action' => 'return',
				'order_id' => $order_id
			), $resp), $this -> _session -> return_route, true);
		}
		else
		{
			//Fail
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'failed'
			), $this -> _session -> return_route, true);
		}
	}

	public function bitpayAction()
	{
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		
		$package = $this -> _order;
		$BitPay = new Ynpayment_Api_BitPay();
		$BitPay -> initialize($gateway);
		$notificationURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						          'action' => 'bitpay-return',
						        ), 'ynpayment_paypackage', true);
		$redirectURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						          'action' => 'bitpay-callback',
						        ), 'ynpayment_paypackage', true);
		$response = $BitPay -> process_payment($package, $this -> _order -> getIdentity(), $notificationURL, $redirectURL, $this -> _order);
		if(isset($response['error']) && $response['error'])
		{
			$this -> _session -> errorMessage = $response['error']['message'];
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'finish', 'state' => 'failed'), $this -> _return_route, true);
		}
		elseif ($response['status'] != "new")
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'failed'
			), $this -> _return_route, true);
		}
		else
		{
			$this -> _redirect($response['url']);
		}
	}

	public function bitpayCallbackAction()
	{
		$params = $this -> _getAllParams();
		if ($params['status'] == "complete")
		{
			$order_id =   json_decode($params['posData']) -> orderId;
			$orderTb = Engine_Api::_() -> getDbTable('orders', $this -> _session -> module);
			$order = $orderTb -> findRow($order_id);
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['id'];
			$resp['amount'] = $params['price'];
			$resp['currency'] = $params['currency'];
			$status = $order -> onPackageTransactionReturn($resp);
		}
		else
		{
			exit(1);
		}
	}

	public function bitpayReturnAction()
	{
		$params = $this -> _getAllParams();
		$order_id =   json_decode($params['posData']) -> orderId;
		$orderTb = Engine_Api::_() -> getDbTable('orders', $this -> _session -> module);
		$order = $orderTb -> findRow($order_id);
		$return_route = $this -> _session -> return_route;
		$this -> _session -> unsetAll();
		if ($order -> status == 'complete')
		{
			// Redirect
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'completed',
				'order_id' => $order_id
			), $return_route, true);
		}
		else
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'finish',
				'state' => 'failed',
				'order_id' => $order_id
			), $return_route, true);
		}
	}

	public function stripeAction()
	{
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$package = $this -> _order -> getSource();
		$settings = (array)$gateway -> config;
		$STRIPE_PUBLIC_KEY = "";
		$STRIPE_SECRET_KEY = "";
		if ($settings)
		{
			$STRIPE_PUBLIC_KEY = $settings['stripe_public_key'];
			$STRIPE_SECRET_KEY = $settings['stripe_secret_key'];
		}
		$this -> view -> STRIPE_PUBLIC_KEY = $STRIPE_PUBLIC_KEY;

		$this -> view -> form = $form = new Ynpayment_Form_Stripe();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		// Stores errors:
		$errors = array();
		// Need a payment token:
		if (isset($_POST['stripeToken']))
		{
			$token = $_POST['stripeToken'];

			// Check for a duplicate submission, just in case:
			// Uses sessions, you could use a cookie instead.
			if (isset($_SESSION['token']) && ($_SESSION['token'] == $token))
			{
				$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
				$form -> addError($errors['token']);
				return;
			}
			else
			{
				// New submission.
				$_SESSION['token'] = $token;
			}
		}
		else
		{
			$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
			$form -> addError($errors['token']);
			return;
		}

		// Set the order amount somehow:
		$amount = ($this -> _order -> price) * 100;
		//  in cents

		// Validate other form data!

		// If no errors, process the order:
		if (empty($errors))
		{

			// create the charge on Stripe's servers - this will charge the user's card
			try
			{
				// set your secret key: remember to change this to your live secret key in production
				// see your keys here https://manage.stripe.com/account
				$Stripe = new Ynpayment_Api_Stripe_Stripe();
				$Stripe -> includeFiles();
				Stripe::setApiKey($STRIPE_SECRET_KEY);
				$viewer = Engine_Api::_() -> user() -> getViewer();
				// Charge the order:
				$charge = Stripe_Charge::create(array(
					"amount" => $amount, // amount in cents, again
					"currency" => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency'),
					"card" => $token,
					"metadata" => array('order_id' => $this -> _session -> order_id),
				));
				$return_route = $this -> _session -> return_route;
				// Check that it was paid:
				if ($charge -> paid == true)
				{

					// Store the order in the database.
					// Send the email.
					// Celebrate!
					$resp['authorized'] = TRUE;
					$resp['transaction_id'] = $charge -> id;
					$resp['amount'] = $charge -> amount/100;
					$resp['currency'] = strtoupper($charge -> currency);
					
					return $this -> _helper -> redirector -> gotoRoute(array_merge(array(
						'action' => 'return',
						'order_id' => $charge -> metadata['order_id']
					), $resp), $return_route, true);

				}
				else
				{
					// Charge was not paid!
					return $this -> _helper -> redirector -> gotoRoute(array(
						'action' => 'finish',
						'state' => 'failed'
					), $return_route, true);
				}

			}
			catch (Stripe_CardError $e)
			{
				// Card was declined.
				$e_json = $e -> getJsonBody();
				$err = $e_json['error'];
				$errors['stripe'] = $err['message'];
				$form -> addError($errors['stripe']);
				return;
			}
			catch (Stripe_ApiConnectionError $e)
			{
				// Network problem, perhaps try again.
				$form -> addError('Network problem, perhaps try again');
				return;
			}
			catch (Stripe_InvalidRequestError $e)
			{
				// You screwed up in your programming. Shouldn't happen!
				$form -> addError('You screwed up in your programming. Shouldn\'t happen!');
				return;
			}
			catch (Stripe_ApiError $e)
			{
				// Stripe's servers are down!
				$form -> addError('Stripe\'s servers are down!');
				return;
			}
			catch (Stripe_CardError $e)
			{
				// Something else that's not the customer's fault.
				$form -> addError('Something else that\'s not the customer\'s fault.');
				return;
			}
		} 
	}
	public function authorizeProcessAction() 
	{
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled))
		{
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$package = $this -> _order -> getSource();
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
		$return_route = $this -> _session -> return_route;
		$values = $this -> _getAllParams();
		$values['total'] = $this -> _order -> price;
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
				} 
				else 
				{
					// Unset certain keys
					return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $this -> _order -> getIdentity()), $resp), $return_route, true);
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
					return $this -> _helper -> redirector -> gotoRoute(array_merge(array('action' => 'return', 'order_id' => $this -> _order -> getIdentity()), $resp), $return_route, true);
				}
				break;
		}
	}

	public function heidelpayAction() {
		// Get gateway
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array(), $this -> _cancel_route, true);
		}
		$response_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'heidelpay-callback', ), 'ynpayment_paypackage', true). '/m/'.$this -> _session -> module.'/return_route/'.$this -> _session -> return_route;
		// Get package
		$package = $this -> _order -> getSource();
		$order_id = $this -> _order -> getIdentity();
		$HeidelPay = new Ynpayment_Api_HeidelPay();
		$HeidelPay -> initialize($gateway);
		
		$returnvalue = $HeidelPay -> process_payment($package, $order_id, null, $response_url);
		
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

	public function heidelpayCallbackAction() {
		// check pay subscription or recurring (if exist subscription => pay for recurring)
		// Get gateway
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$params = $this -> _getAllParams();
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'HeidelPay'));
		$return_route = $params['return_route'];
		$logFile = APPLICATION_PATH . '/temporary/log/result.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
		
		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		
		if ($params['PROCESSING_RESULT'] == "ACK") {
			// Process
			
			$resp['authorized'] = TRUE;
			$resp['transaction_id'] = $params['IDENTIFICATION_UNIQUEID'];
			$resp['amount'] = $params['PRESENTATION_AMOUNT'];
			$resp['currency'] =  $params['PRESENTATION_CURRENCY'];
			
			if(!empty($params['m']))
			{
				$orderTb = Engine_Api::_() -> getDbTable('orders', $params['m']);
				$order = $orderTb -> findRow($params['IDENTIFICATION_TRANSACTIONID']);
				$order -> onPackageTransactionReturn($resp);
			}
			
		} else {
			// Fail
			$url = $response_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'finish',
						        'state' => 'failed',
						        'order_id' => $params['IDENTIFICATION_TRANSACTIONID']
								), $return_route, true);
			echo $url;
		}
		
		$this -> _session -> unsetAll();
		if ($order -> status == 'completed')
		{
			// Redirect
			$url = $response_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'return',
						        'order_id' => $params['IDENTIFICATION_TRANSACTIONID']
								), $return_route, true);
			echo $url;
		}
		else
		{
			// Fail
			$url = $response_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						        'action' => 'finish',
						        'state' => 'failed',
						        'order_id' => $params['IDENTIFICATION_TRANSACTIONID']
								), $return_route, true);
			echo $url;
		}
	}
	
	public function braintreeAction() {
		// Get gateway
		$return_route = $this -> _session -> return_route;
		
		$gatewayId = $this -> _getParam('gateway_id', $this -> _session -> gateway_id);
		if (!$gatewayId || !($gateway = Engine_Api::_() -> getItem('payment_gateway', $gatewayId)) || !($gateway -> enabled)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$package = $this -> _order -> getSource();
		$order_id = $this -> _order -> getIdentity();

		$this -> view -> package = $package;

		$this -> view -> form = $form = new Ynpayment_Form_Braintree();
		
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
			
			return $this -> _helper -> redirector -> gotoRoute(array_merge(array(
					'action' => 'return',
					'order_id' => $result->transaction->orderId
				), $resp), $return_route, true);
		
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
}
