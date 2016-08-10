<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Api_BitPay extends Ynpayment_Api_Paymentgateway 
{
    protected $_apiKey = '';
	
    public function initialize(Payment_Model_Gateway $gateway) 
    {
		$settings = (array) $gateway->config;
		if($settings)
		{
			 $this->_apiKey = $settings['bp_apiKey'];
		}
    }
    public function process_payment($package, $order_id, $notificationURL = NULL, $redirectURL = NULL) 
    {
		$BitPayLib = new Ynpayment_Api_BitPay_BitPayLib();
		$price = $package -> price;
		$posData = '{"orderId":"'.$order_id.'"}';
		$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency');
		if(!$notificationURL)
		{
			$notificationURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						          'action' => 'bitpay-return',
						        ), 'ynpayment_subscription', true);
		}
		if(!$notificationURL)
		{
			$redirectURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
						          'action' => 'bitpay-callback',
						        ), 'ynpayment_subscription', true);
		}
		$bpOptions = array(
			'apiKey' => $this->_apiKey,
			'verifyPos' => true,
			'notificationURL' => $notificationURL,
			'currency' => $currency,
			'redirectURL' =>  $redirectURL,
		);
		$response = $BitPayLib -> bpCreateInvoice($order_id, $price, $posData,$bpOptions, $options = array());
		return $response;
    }
}

?>