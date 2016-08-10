<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_Braintree extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: Braintree');
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_BRAINTREE_DESCRIPTION');
    $description = vsprintf($description, array(
    	'https://www.braintreegateway.com/login',
      	'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'braintree-callback',
        ), 'ynpayment_subscription', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Elements
	 $this->addElement('Text', 'braintree_merchant_id', array(
      'label' => 'Merchant ID',
      'description' => 'The Merchant ID.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
    $this->addElement('Text', 'braintree_public_key', array(
      'label' => 'Public Key',
      'description' => 'The Public Key.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	 $this->addElement('Text', 'braintree_private_key', array(
      'label' => 'Private Key',
      'description' => 'The Private Key.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	 $this->addElement('Text', 'braintree_cse_key', array(
      'label' => 'CSE Key',
      'description' => 'The CSE Key.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}