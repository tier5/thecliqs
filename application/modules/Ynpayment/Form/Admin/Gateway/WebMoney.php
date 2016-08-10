<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_WebMoney extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: WebMoney');
    
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_WEBMONEY_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://my.wmtransfer.com/'
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Elements
    $this->addElement('Text', 'wm_payee_purse', array(
      'label' => 'Merchant Purse',
      'description' => 'The merchant\'s purse to which the customer has to pay. Format is a letter and twelve digits. Presently, Z, R, and E purses are used in the service.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}