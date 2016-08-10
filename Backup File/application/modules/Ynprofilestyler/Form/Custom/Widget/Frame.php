<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Widget_Frame extends Engine_Form
{
	public function init()
	{
		$this->addElement('hidden', 'background_color', array(
			'required'                    => false,
			'ignore'                      => true,
			'autoInsertNotEmptyValidator' => false,
			'decorators'                  => array(
				array(
					array('data' => new Zend_Form_Decorator_HtmlTag(
						array('tag' => 'div', 'class' => 'background-color color'))),
				),
				array('HtmlTag', array(
					'tag' => 'dd',
				)),
				array('Label', array('tag' => 'dt')),
				array('ViewHelper'),
				array('HtmlTag2', array('class' => 'form-wrapper'))
			),
			'label'                       => 'Background Color'
		));

		$this->addElement('select', 'background_image', array(
			'label'        => 'Use Images',
			'attribs'      => array('id' => 'widget_frame_background_image')
		));
		
		$this->addElement('hidden', 'background');

		$this->addElement('select', 'border_style', array(
			'label'        => 'Border Style',
		));

		$this->addElement('select', 'border_width', array(
			'label'        => 'Weight',
		));

		$this->addElement('hidden', 'border_color', array(
			'required'                    => false,
			'ignore'                      => true,
			'autoInsertNotEmptyValidator' => false,
			'decorators'                  => array(
				array(
					array('data' => new Zend_Form_Decorator_HtmlTag(
						array('tag' => 'div', 'class' => 'border-color color'))),
				),
				array('HtmlTag', array(
					'tag' => 'dd',
				)),
				array('Label', array('tag' => 'dt')),
				array('ViewHelper'),
				array('HtmlTag2', array('class' => 'form-wrapper')),
			),
			'label'                       => 'Outline Color'
		));

		Engine_Api::_()->ynprofilestyler()->bindRuleIds($this, 'widget');
	}
}