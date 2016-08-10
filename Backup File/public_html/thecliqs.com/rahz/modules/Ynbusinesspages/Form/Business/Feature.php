<?php
class Ynbusinesspages_Form_Business_Feature extends Engine_Form
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
  	$str_desc = $view -> translate('It costs %s to feature business in 1 day', $view -> locale()->toCurrency($this->_fee, $currency));
    $this
	  ->setDescription($str_desc)
      ->setAttrib('class', 'global_form_popup')
      ;
	
	$this->addElement('Text', 'day', array(
      'label' => 'How many days do you want to feature business?',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
          new Engine_Validate_AtLeast(1),
       ),
	));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Feature',
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    
    $this->addDisplayGroup(array('submit'), 'buttons');
  }
}

