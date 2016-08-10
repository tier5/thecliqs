<?php
class Ynbusinesspages_Form_Admin_Creators_Create extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Add Creator')
          -> setDescription('Search member who will be added as a business creator.')
          -> setAttrib('class', 'global_form_popup');
          
		$this -> addElement('Text', 'to', array('autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 1,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);

		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Submit',
			'type' => 'submit',
			'order' => 3,
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'order' => 4,
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));

		$this -> addDisplayGroup(array(
			'submit_btn',
			'cancel'
		), 'buttons');
	}

}
