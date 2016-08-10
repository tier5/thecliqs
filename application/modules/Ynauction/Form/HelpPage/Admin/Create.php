<?php

class Ynauction_Form_HelpPage_Admin_Create extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Create Help Page') -> setDescription('Create a help page.');

	
	$this->addElement('text','title',array(
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
		'label'=>'Show',
		'multiOptions'=>array(
			'hide'=>'No',
			'show'=>'Yes',
		),
		'value'=>'show',
	));
		
	$this->addElement('tinyMce','content',array(
		'label'=>'Content',
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'ynauction','action'=>'index','controller'=>'helps'), 'admin_default', true),
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
