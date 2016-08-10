<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Add extends Engine_Form
{
	public function init()
	{
	    $this->setAttrib('class', 'global_form_popup');
	    
		$this->addElement('Text', 'title', array(
			'label'      => 'Title',
			'required'   => true,
			'allowEmpty' => false,
			'validators' => array(
				array('StringLength', true, array(1, 50)),
			),
		));

		// Icon
		$this->addElement('File', 'thumbnail', array(
			'label'  => 'Thumbnail',
		    'required'   => true,
			'accept' => 'image/*'));
		$this->getElement('thumbnail')->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this->getElement('thumbnail')->addValidator('Size', false, '2MB');

		// Element: submit
		$this->addElement('Button', 'submit', array(
			'label'      => 'Save',
			'type'       => 'submit',
			'ignore'     => true,
		));

		$this->addElement('Cancel', 'cancel', array(
			'label'       => 'cancel',
			'link'        => true,
			'prependText' => ' or ',
			'href'        => '',
			'onclick'     => 'parent.Smoothbox.close();',
		));
		
		 $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}
