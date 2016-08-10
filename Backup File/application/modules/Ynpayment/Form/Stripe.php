<?php
/**
 * 
 * @author MinhNC
 */
define("START_YEAR",2014);
define("END_YEAR",2040);
class Ynpayment_Form_Stripe extends Engine_Form
{
	public function init()
	{
		$this -> setDescription('You can pay using: Mastercard, Visa, American Express, JCB, Discover, and Diners Club. JavaScript Required! For security purposes, JavaScript is required in order to complete an order.');
		$this -> setMethod("POST") -> setAttrib('id', 'payment-form');
		
		$this->addElement('Heading', 'separator3', array(
		  'label' => 'Credit Card Info',
		  'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
            ),
		));
		
		$this->addElement('Text', 'card_number', array(
		  'label' => 'Card Number *',
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
		
		$this->addElement('Text', 'card_cvc', array(
		  'label' => 'CVC *',
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
			
		$this->addElement('Select', 'card_expiry_month', array(
		  'label' => 'Expiration Month *',
		  'multiOptions' => $moths,
		));
		
		$year = array();
		for($i = START_YEAR; $i < END_YEAR; $i++){
			$year[$i] = $i;
		}
		
		$this->addElement('Select', 'card_expiry_year', array(
		  'label' => 'Expiration Year',
		  'multiOptions' => $year,
		));
		
		$this->addElement('hidden', 'stripeToken', array(
		 	'order' => '100',
		));
		
		$this->addElement('Button', 'submitBtn', array(
		  'label' => 'Submit Payment',
		  'type' => 'button',
		));
	}
}
 
?>