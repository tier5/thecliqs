<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Admin_Upload extends Engine_Form
{
	public function init()
	{
		$this->setAttribs(array(
			'enctype' => 'multipart/form-data',
			'class'   => 'global_form_popup'
		));

		$this->setTitle('Add Background Image');

		$this->addElement('text', 'url', array(
			'label' => 'Enter URL',
			'filters' => array('StringTrim', 'StripTags'),
// 			'validators' => array(
// 				array(
// 					'Callback',
// 					true,
// 					array ('callback' => function($value) {
// 						return Zend_Uri::check($value);
// 					}),
// 					'messages' => array(
// 						'Please enter a valid URL',
// 					),
// 				),
// 			)
		));

		$this->addElement('file', 'image', array(
			'prependText' => 'or',
			'label'       => 'Browse Image',
			'accept' => 'image/*',		
		));
		$this->getElement('image')->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this->getElement('image')->addValidator('Size', false, '2MB');
		
		// Add submit button
		$this->addElement('Button', 'submit', array(
			'label' => 'Add',
			'type'  => 'submit',
		));
	}
}