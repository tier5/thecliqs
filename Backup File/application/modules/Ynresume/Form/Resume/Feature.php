<?php
class Ynresume_Form_Resume_Feature extends Engine_Form
{
  	protected $_fee;
	
	public function getFee()
	{
	return $this -> _fee;
	}
	
	public function setFee($fee)
	{
	$this -> _fee = $fee;
	} 
	
  public function init()
  {
  	$currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency');
	$view = Zend_Registry::get('Zend_View');
  	$str_desc = $view -> translate('It costs %s to feature resume in 1 day', $view -> locale()->toCurrency($this->_fee, $currency));
    $this
      ->setTitle("Feature Resume")
	  ->setDescription($str_desc)
      ->setAttrib('class', 'global_form_popup')
      ;
	
	$this->addElement('Text', 'day', array(
      'label' => 'How many days do you want to feature resume?',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
          new Engine_Validate_AtLeast(1),
       ),
	));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Register',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

