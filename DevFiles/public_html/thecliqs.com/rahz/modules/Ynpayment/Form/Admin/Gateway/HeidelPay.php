<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Form_Admin_Gateway_HeidelPay extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: HeidelPay');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Sender
    $this->addElement('Text', 'hp_sender', array(
      'label' => 'Sender',
      'description' => 'Each Server which sends requests to the system has an own sender unique ID. The sender UID is no logical business orientated subdivision like the channel ID, but refers to physical installations of software. Please provide here the value you have received from the customer support department.',
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
     // Login
    $this->addElement('Text', 'hp_login', array(
      'label' => 'Login',
      'description' => 'User Id of the sending user. This user must be configured with SEND rights.', 
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	// Pwd
    $this->addElement('Text', 'hp_pwd', array(
      'label' => 'Password',
      'description' => 'Password of the sending user.', 
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
	// Chanel
    $this->addElement('Text', 'hp_channel', array(
      'label' => 'Chanel',
      'description' => 'The channel ID is a unique key for the identification of the unit which sends transactions into the system. Every merchant can have multiple channels for different purposes. Possible division criteria are for example different shops, organizational units, customer groups or countries. The channel ID doesn‟t refer to physical installations of the software like the sender ID but is a logical business orientated subdivision. Different channels help to analyze the entirety of transactions and to provide different system configurations for a nonuniform transaction base. The channel IDs are assigned by the account management.', 
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
	
  }
}