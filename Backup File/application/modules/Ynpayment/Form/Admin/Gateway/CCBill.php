<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_CCBill extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: CCBill');
    
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_CCBILL_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://admin.ccbill.com/loginMM.cgi',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'ccbill-callback',
          'status' => 'ccbill-success',
        ), 'ynpayment_subscription', true),
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'ccbill-callback',
          'status' => 'ccbill-fail',
        ), 'ynpayment_subscription', true)
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'ccbill_accnum', array(
      'label' => 'Client Main Account',
      'description' => 'Enter your main account at CCBill (6-digit number).',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'ccbill_subaccnum', array(
      'label' => 'Client Subaccount',
      'description' => 'Enter your subaccount at CCBill (4-digit number).',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	$this->addElement('Text', 'ccbill_salt', array(
      'label' => 'Salt Secret Key',
      'description' => 'Enter your Salt (secret key) at CCBill (an alphanumeric string, up to 32 characters long).',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	$this->addElement('Text', 'ccbill_form_id', array(
      'label' => 'Form Name',
      'description' => 'Enter your Form (form id) at CCBill (an alphanumeric string, 5 characters long).',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
  }
}