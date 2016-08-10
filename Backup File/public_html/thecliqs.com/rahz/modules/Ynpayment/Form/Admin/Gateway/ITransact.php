<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_ITransact extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: iTransact');
    
    $description = $this->getTranslator()->translate('YNPAYMENT_FORM_ADMIN_GATEWAY_ITRANSACT_DESCRIPTION');
    $description = vsprintf($description, array(
      'http://itransact.com/',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'iTransact'
        ), 'ynpayment_post_back', true),
      'https://secure.paymentclearing.com/cgi-bin/rc/recur/recipe_list/list.cgi'
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'itransact_gateway_id', array(
      'label' => 'Gateway ID',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'api_username', array(
      'label' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	$this->addElement('Text', 'api_key', array(
      'label' => 'API key',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}