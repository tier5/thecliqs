<?php
class Ynlistings_Form_TransferOwner extends Engine_Form {
	public function init() {
		$this -> setTitle('Listing Owner Transfer') -> setDescription('Please choose a member you want to transfer the owner role to:') -> setAttrib('class', 'global_form_popup') -> setMethod('post') -> setAttrib('id', 'ynlistings_transfer_owner');

		$this -> addElement('Text', 'to', array('autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'required' => true,
			'allowEmpty' => false,
			'style' => 'margin-top:-5px',
			'order' => 1,
			'validators' => array('NotEmpty'),
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);

		$this -> addElement('Button', 'submit', array(
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
			'submit',
			'cancel'
		), 'buttons');
	}

}
