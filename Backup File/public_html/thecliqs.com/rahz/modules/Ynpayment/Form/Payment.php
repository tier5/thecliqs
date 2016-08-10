<?php
/**
 * 
 * @author MinhNC
 */
define("START_YEAR",2014);
define("END_YEAR",2040);

class Ynpayment_Form_Payment extends Engine_Form
{
	public function init()
	{
		$this->addElement('Heading', 'billing_info_heading', array(       
        'order' => -9,
            'label' => 'Billing Info',
            'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
            ),
        ));
		$this->addElement('Text', 'first_name', array(
		  'label' => 'First Name *',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '50'))
		  ),
		));
		$this -> first_name -> setAttrib('required', true);
		
		$this->addElement('Text', 'last_name', array(
		  'label' => 'Last Name *',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '50'))
		  ),
		));
		$this -> last_name -> setAttrib('required', true);
    
		$this->addElement('Text', 'address', array(
		  'label' => 'Address *',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '60'))
		  ),
		));
		$this -> address -> setAttrib('required', true);
    
		
		$this->addElement('Text', 'city', array(
		  'label' => 'City *',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '40'))
		  ),
		));
		$this -> city -> setAttrib('required', true);
    
		$this->addElement('Text', 'country_code', array(
		  'label' => 'Country *',
          'allowEmpty' => false,
          'description' => 'Up to 60 characters (no symbols).',
		  'required' => false,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '60'))
		  ),
		));
		$this -> country_code -> getDecorator('Description')->setOption('placement', 'append');
		$this -> country_code -> setAttrib('required', true);
		
		$this->addElement('Text', 'state', array(
		  'label' => 'State *',
          'allowEmpty' => false,
          'description' => 'Up to 40 characters (no symbols) or a valid two-character state code.',
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '40'))
		  ),
		));
		$this -> state -> getDecorator('Description')->setOption('placement', 'append');
		$this -> state -> setAttrib('required', true);
    
		$this->addElement('Text', 'zip', array(
		  'label' => 'Zip Code *',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '20'))
		  ),
		));
		$this -> zip -> setAttrib('required', true);
		
		$this->addElement('Heading', 'separator2', array(
		  'label' => 'Additional Info',
		  'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
            ),
		));
		
		$this->addElement('Text', 'phone', array(
		  'label' => 'Phone *',
          'allowEmpty' => false,
		  'required' => true,
		  'description' => 'For example, (123)123-1234',
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
		  ),
		));
		$this -> phone -> getDecorator('Description')->setOption('placement', 'append');
		$this -> phone -> setAttrib('required', true);
		
        $this->addElement('Text', 'email_address', array(
		  'label' => 'Email Address *',
          'allowEmpty' => false,
		  'required' => true,
		  'description' => ' For example, janedoe@customer.com',
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
			new Engine_Filter_StringLength(array('max' => '255'))
		  ),
		   'validators' => array(
		        'EmailAddress'
		      ),
		));
		$this -> email_address -> getDecorator('Description')->setOption('placement', 'append');
		$this -> email_address -> setAttrib('required', true);
		
		$this->addElement('Heading', 'separator3', array(
		  'label' => 'Credit Card Info',
		  'decorators' => array(
                'ViewHelper',
                array('Label', array('tag' => 'span')),
                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
            ),
		));
    
		$this->addElement('Text', 'credit_card_number', array(
		  'label' => 'Credit Card Number *',
		  'allowEmpty' => false,
		  'required' => true,
		  'validators' => array(
                array('NotEmpty', true),
			),
		  'filters' => array(
			new Engine_Filter_Censor(),
			'StripTags',
		  ),
		));
		$this -> credit_card_number -> setAttrib('required', true);
		$this->addElement('Text', 'CVV2', array(
		  'label' => 'Card Security Code *',
		  'allowEmpty' => false,
		  'required' => true,
		  'size'      => 4,
		  'maxlength' => 4,
		  'validators' => array(
                array('NotEmpty', true),
                array('Int', true),
                ),
		));
		$this -> CVV2 -> setAttrib('required', true);
    
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
			
		$this->addElement('Select', 'expiration_month', array(
		  'label' => 'Expiration Month *',
		  'multiOptions' => $moths,
		));
		
		$year = array();
		for($i = START_YEAR; $i < END_YEAR; $i++){
			$year[$i] = $i;
		}
		
		$this->addElement('Select', 'expiration_year', array(
		  'label' => 'Expiration Year',
		  'multiOptions' => $year,
		));
		
		$this->addElement('Button', 'submit', array(
                    'order' => 1000,
		  'label' => 'Confirm Payment',
		  'type' => 'submit',
		));
	}
}
 
?>