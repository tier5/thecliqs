<?php
/**
 * 
 * @author MinhNC
 */
define("START_YEAR",2014);
define("END_YEAR",2040);
class Ynpayment_Form_Braintree extends Engine_Form
{
	protected $_onetime = true;
	
	public function getOnetime()
	{
		return $this -> _onetime;
	}
	
	public function setOnetime($onetime)
	{
		$this -> _onetime = $onetime;
	} 
	
	
	public function init()
	{
		$this -> setTitle('Braintree Credit Card Transaction Form');
		$this -> setMethod("POST") -> setAttrib('id', 'braintree-payment-form');
		
		$this->addElement('Heading', 'separator3', array(
		  'label' => 'Credit Card Info',
		  'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
            ),
		));
		
		if(!$this -> _onetime)
		{
			$this->addElement('Text', 'first_name', array(
			  'label' => 'First Name',
			  'allowEmpty' => false,
			  'required' => true,
	          'value' => '',
			  'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
			  ),
			));
			
			$this->addElement('Text', 'last_name', array(
			  'label' => 'Last Name',
			  'allowEmpty' => false,
			  'required' => true,
	          'value' => '',
			  'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
			  ),
			));
			
			$this->addElement('Text', 'postal_code', array(
			  'label' => 'Postal Code',
			  'allowEmpty' => false,
			  'required' => true,
	          'value' => '',
			  'filters' => array(
				new Engine_Filter_Censor(),
				'StripTags',
			  ),
			));
		}
		
		$this->addElement('Text', 'number', array(
		  'label' => 'Card Number',
		  'size' => 20,
		  'data-encrypted-name' => 'number',
		  'allowEmpty' => false,
		  'required' => true,
		  'autocomplete' => 'off',
          'value' => '',
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '20'))
		  ),
		));
		
		$this->addElement('Text', 'cvv', array(
		  'label' => 'CVV',
		  'size' => 4,
		  'data-encrypted-name' => 'cvv',
		  'allowEmpty' => false,
		  'required' => true,
		  'autocomplete' => 'off',
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '4'))
		  ),
		));
    
		$moths = array(
			"01"	=> 'January',
			"02"	=> 'February',
			"03"	=> 'March',
			"04"	=> 'April',
			"05"	=> 'May',
			"06"	=> 'June',
			"07"	=> 'July',
			"08"	=> 'August',
			"09"	=> 'September',
			"10"	=> 'October',
			"11"	=> 'November',
			"12"	=> 'December',
			);
			
		$this->addElement('Select', 'month', array(
		  'label' => 'Expiration Month',
		  'multiOptions' => $moths,
		));
		
		$year = array();
		for($i = START_YEAR; $i < END_YEAR; $i++){
			$year[$i] = $i;
		}
		
		$this->addElement('Select', 'year', array(
		  'label' => 'Expiration Year',
		  'multiOptions' => $year,
		));
		
		$this->addElement('Button', 'submitBtn', array(
		  'label' => 'Submit Payment',
		  'type' => 'submit',
		));
	}
}
 
?>