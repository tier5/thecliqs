<?php
class Ynjobposting_Form_Company_Sponsor extends Engine_Form
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
  	$str_desc = $view -> translate('It costs %s to sponsor company in 1 day', $view -> locale()->toCurrency($this->_fee, $currency));
    $this
      ->setTitle('Sponsor Company')
	  ->setDescription($str_desc)
      ->setAttrib('class', 'global_form_popup')
      ;
	
	$this->addElement('Text', 'day', array(
      'label' => 'How many days do you want to sponsor company?',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
          new Engine_Validate_AtLeast(1),
       ),
	));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Sponsor',
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
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

