<?php

class Socialgames_Form_Admin_Games_Manage extends Engine_Form
{
	public function init()
	{
		$this->setTitle(Zend_Registry::get('Zend_Translate')->_("Edit Game"))
			->setMethod('post')
			->setAttrib('class', 'global_form_popup');

		$this->addElement('Text', 'title', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Title"),
			'required' => true,
			'filter' => array('StringTrim'),
			'attribs' => array(
				'style' => 'width: 280px;'
			)
		));

		$this->addElement("Textarea", 'description', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Description"),
			'required' => true,
			'filter' => array('StringTrim'),
			'attribs' => array(
				'style' => 'width: 330px;'
			)
		));
		
		$this->addElement("Textarea", 'instruction', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Instruction"),
			'required' => false,
			'filter' => array('StringTrim'),
			'attribs' => array(
				'style' => 'width: 330px;'
			)
		));

		$this->addElement('Text', 'image', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Image"),
			'attribs' => array(
				'style' => 'width: 330px;'
			)
		));

		$this->addElement('Text', 'flash', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Flash file"),
			'attribs' => array(
				'style' => 'width: 330px;'
			)
		));

		$this->addElement('Checkbox', 'is_active', array(
			'label' => 'Enabled?',
			'checkedValue' => '1',
			'uncheckedValue' => '0',
			'value' => '1',
		));
		
		$this->addElement('Checkbox', 'is_featured', array(
			'label' => 'Feature?',
			'checkedValue' => '1',
			'uncheckedValue' => '0',
			'value' => '1',
		));

		$this->addElement('Button', 'submit', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Save"),
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
			'label' => Zend_Registry::get('Zend_Translate')->_("Cancel"),
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onClick' => 'javascript:parent.Smoothbox.close();',
			'decorators' => array(
				'ViewHelper'
			)
		));

		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}