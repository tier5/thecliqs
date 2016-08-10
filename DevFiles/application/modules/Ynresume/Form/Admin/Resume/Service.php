<?php
class Ynresume_Form_Admin_Resume_Service extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Update Duration For "Who Viewed Me" Service')
          -> setDescription('This is the place you can add more days for your users to use "Who Viewed Me" service')
          -> setAttrib('class', 'global_form');
        
		
		$this -> addElement('Text', 'to', array(
			'autocomplete' => 'off',
			'label' => 'Select Member'
		));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 1,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);
		
		//title
	    $this->addElement('Text', 'number_service_day', array(
	      'label' => 'Number Service Day',
	      'description' => 'These members can user "Who Viewed Me" service for more days',
	      'required' => true,
	      'allowEmpty' => false,
	      'validators' => array(
	          new Engine_Validate_AtLeast(1),
	       ),
	    ));
		
		// Buttons
	    $this->addElement('Button', 'submit_button', array(
	      'label' => 'Save',
	      'onclick' => 'removeSubmit()',
	      'type' => 'submit',
	      'ignore' => true,
	      'values' => 1,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		
	   $this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	
	    $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
	      'decorators' => array(
	        'FormElements',
	        'DivDivDivWrapper',
	      ),
	    ));
	}

}
