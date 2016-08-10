<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_Stripe extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: Stripe');
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_STRIPE_DESCRIPTION');
    $description = vsprintf($description, array(
    	'https://stripe.com',
      	'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'stripe-callback',
        ), 'ynpayment_subscription', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Elements
    $this->addElement('Text', 'stripe_secret_key', array(
      'label' => 'Secret Key',
      'description' => 'The Secret Key.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
    $this->addElement('Text', 'stripe_public_key', array(
      'label' => 'Public Key',
      'description' => 'The Public Key.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}