<?php
class Yncontest_Form_ManageRule_Create extends Engine_Form {
	protected $_field;

	public function init() {
		$this -> setMethod('post');
		$this->setTitle("Add New Rule");
		
		$this -> addElement('text', 'rule_name', array(
			'label' => 'Rule titles*',
			 'required' => true,
			  'class' => 'text', 
			  'maxlength' => 64,
			   'filters' => array('StringTrim'),
			    'validators' => array('NotEmpty', )
		));
		
		// Init descriptions
	    $this->addElement('Textarea', 'description', array(
	      'label' => 'Description',
	      'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	        //new Engine_Filter_HtmlSpecialChars(),
	        new Engine_Filter_EnableLinks(),
	      ),
	    ));
		
		// Buttons
		$this -> addElement(
			'Button', 'submit', 
				array('label' => 'Save', 'type' => 'submit',
				 'ignore' => true,
				  'decorators' => array('ViewHelper')));

		$this -> addElement('Cancel', 'cancel',
		 array('label' => 'cancel', 'link' => true, 
		 'prependText' => ' or ', 'href' => '', 'onClick' => 'javascript:parent.Smoothbox.close();', 'decorators' => array('ViewHelper')));
		 
	}
	
	
}