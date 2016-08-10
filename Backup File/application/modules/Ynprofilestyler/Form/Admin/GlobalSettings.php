<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Admin_GlobalSettings extends Engine_Form 
{
	public function init() 
	{
		parent::init();

		$this->setTitle('Global Settings')
			->setDescription('YNPROFILESTYLER_FORM_ADMIN_GLOBAL_SETTINGS_DESCRIPTION');

		$this->addElement('text', 'slideTop', array(
			'label' => 'Top (px)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The top position of the slideshow'
		));
		
		$this->addElement('text', 'slideLeft', array(
			'label' => 'Left (px)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The left position of the slideshow. If this value is not inputted, then the slideshow will be aligned center'
		));
		
		$this->addElement('text', 'slideWidth', array(
			'label' => 'Width (px)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The width of the slideshow',
		));
		
		$this->addElement('text', 'slideHeight', array(
			'label' => 'Height (px)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The height of the slideshow',
		));
		
		$this->addElement('text', 'slideDistance', array(
			'label' => 'Distance to the body (px)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The distance between the top of the slideshow to the body content bellow',
		));
		
		$this->addElement('text', 'slideInterval', array(
			'label' => 'Interval (ms)', 
			'onkeypress' => 'return ynps_isNumericKey(event)',
			'description' => 'The interval to swith between slides in the slideshow'
		));
		
		 // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save changes',
            'type' => 'submit',
            'ignore' => true
        ));
		
		$this->getView()->headScript()->appendScript('function ynps_isNumeric(event) {}');
	}
}