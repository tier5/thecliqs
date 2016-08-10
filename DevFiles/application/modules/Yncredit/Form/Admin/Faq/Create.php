<?php

class Yncredit_Form_Admin_Faq_Create extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Create FAQ') -> setDescription('Create a FAQ to display in Faqs pages.');
	$this->addElement('text','question',array(
		'label'=>'Question',
		'required'=>true,
		'maxlength'=>'255',		
		'filters'=>array('StringTrim'),
	));
	
	$this->addElement('tinyMce','answer',array(
		'label'=>'Answer',
		'filters'=>array('StringTrim'),
	));
	
	$this->addElement('text','ordering',array(
		'label'=>'Ordering',
		'filters'=>array('StringTrim'),
		'validators'=>array('Int'),
		'value'=> 1,
	));
	
	$this->addElement('radio','status',array(
		'label'=>'Display this FAQ',
		'multiOptions'=>array(
			'show'=>'Yes',
			'hide'=>'No',
		),
		'value'=>'show',
	));
	
	/**
	 * add button groups
	 */
	$this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
	}
}
