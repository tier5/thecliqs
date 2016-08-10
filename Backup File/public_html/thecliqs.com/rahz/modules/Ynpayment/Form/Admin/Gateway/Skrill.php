<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_Skrill extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: Skrill');
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_SKRILL_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.skrill.com/en/'
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Elements
    $this->addElement('Text', 'skrill_pay_to_email', array(
      'label' => 'Merchant Email',
      'description' => 'Email address of the Merchant’s moneybookers.com account.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
    $this->addElement('Text', 'skrill_secret', array(
      'label' => 'Secret Word',
      'description' => 'The secret word submitted in the ‘Merchant Tools’ section of the Merchant’s online Skrill account.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}