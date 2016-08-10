<?php
class Ynbusinesspages_Form_Event_Manage extends Engine_Form
{
	public function init()
	{
		$this
      ->setMethod('get')
      ->setAttrib('class', 'global_form f1')
			 ->setAttrib('id', 'filter_form')
      ;
		$this -> addElement('Text', 'text', array(
			'label' => 'Search:',
			'alt' => 'Search events'
		));

		$this -> addElement('Select', 'view', array(
			'label' => 'View:',
			'multiOptions' => array(
				'' => 'All My Events',
				'2' => 'Only Events I Lead',
			),
		));
		// Buttons
		$this -> addElement('Button', 'search', array(
			'label' => 'Search',
			'type' => 'submit',
			'decorators' => array('ViewHelper', ),
		));
	}

}
