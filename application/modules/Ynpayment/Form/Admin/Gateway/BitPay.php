<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_BitPay extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: BitPay');
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_BITPAY_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://bitpay.com/home',
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Elements
    $this->addElement('Text', 'bp_apiKey', array(
      'label' => 'Merchant API Key',
      'description' => 'Api key you created at bitpay.com.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
  }
}