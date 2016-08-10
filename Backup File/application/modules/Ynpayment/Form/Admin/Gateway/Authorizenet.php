<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_Authorizenet extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: Authorize.Net');
    
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_AUTHORIZENET_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://account.authorize.net/',
      'https://support.authorize.net/authkb/index?page=home#apilogin',
      'http://www.authorize.net/videos/', 
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'AuthorizeNet'
        ), 'ynpayment_silent_post', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'api_login', array(
      'label' => 'API Login ID',
      'description' => 'The merchant API Login ID is provided in the Merchant Interface 
		and must be stored securely.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'transaction_key', array(
      'label' => 'Transaction Key',
      'description' => 'The merchant Transaction Key is provided in the Merchant 
		Interface and must be stored securely. ',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	$types = array(
			"AUTH_CAPTURE"	=> 'Authorization and Capture',
			"AUTH_ONLY"	=> 'Authorization Only',
			"CAPTURE_ONLY"	=> 'Capture Only',
			"CREDIT"	=> 'Credit (Refund)',
			"PRIOR_AUTH_CAPTURE" => 'Prior Authorization and Capture',
			"VOID"	=> 'Void'
			);
	 $this->addElement('Select', 'transaction_settings', array(
      'label' => 'Credit Card Transaction Types',
      'description' => 'If the value submitted does not match a supported value, the 
transaction is rejected. If this field is not submitted or the value is blank, 
the payment gateway will process the transaction as an "Authorization and Capture".',
      'multiOptions' => $types,
      'value' => 'AUTH_CAPTURE'
    ));
  }
}