<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_SlideshowConfigure extends Engine_Form
{
	public function init()
	{
		$this->addElement('text', 'slideTop', array('label' => 'Top (px)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
		$this->addElement('text', 'slideLeft', array('label' => 'Left (px)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
		$this->addElement('text', 'slideWidth', array('label' => 'Width (px)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
		$this->addElement('text', 'slideHeight', array('label' => 'Height (px)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
		$this->addElement('text', 'slideDistance', array('label' => 'Distance to the body (px)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
		$this->addElement('text', 'slideInterval', array('label' => 'Interval (ms)', 'onkeypress' => 'return ynps2.isNumericKey(event)'));
	}
}