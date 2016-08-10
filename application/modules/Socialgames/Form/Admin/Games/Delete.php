<?php

class Socialgames_Form_Admin_Games_Delete extends Engine_Form{

	public function init()
	{
		$this->setTitle('Delete Item')
			->setDescription(Zend_Registry::get('Zend_Translate')->_("Are you sure to delete this item?"))
			->setAttrib('class', 'global_form_popup');
		
		$this->addElement('Button', 'submit', array(
			'type' => 'submit',
			'label' => Zend_Registry::get('Zend_Translate')->_("Delete Item"),
			'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
			'label' =>Zend_Registry::get('Zend_Translate')->_("Cancel"),
			'link' => 'true',
			'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array('ViewHelper')
		));

		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}