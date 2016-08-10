<?php

class Ynauction_Form_Faqs_Admin_Create extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Create FAQ') -> setDescription('Create a FAQ to display in Faqs pages.');

	
	$this->addElement('text','question',array(
		'label'=>'Title',
		'required'=>true,
		'maxlength'=>'255',		
		'filters'=>array('StringTrim'),
	));
	
	$this->addElement('text','ordering',array(
		'label'=>'Ordering',
		'filters'=>array('StringTrim'),
		'validators'=>array('Int'),
		'value'=>999,
	));
	
	$this->addElement('radio','status',array(
		'label'=>'Display this FAQ',
		'multiOptions'=>array(
			'hide'=>'No',
			'show'=>'Yes',
		),
		'value'=>'show',
	));
		
	$this->addElement('tinyMce','answer',array(
		'label'=>'Answer',
		'filters'=>array('StringTrim'),
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'ynauction','action'=>'index','controller'=>'faqs'), 'admin_default', true),
      'onclick' => '',
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
