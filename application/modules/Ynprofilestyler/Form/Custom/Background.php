<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Background extends Engine_Form
{
	public function init()
	{
		$this->addElement('hidden', 'background_color', array(
			'required'                    => false,
			'ignore'                      => true,
			'autoInsertNotEmptyValidator' => false,
			'decorators'                  => array(
				array(
					array('data' => new Ynprofilestyler_Form_Decorator_Div(
						array('tag' => 'div', 'class' => 'background-color'))),
				),
				array('HtmlTag', array(
					'tag' => 'dd',
				)),
				array('Label', array('tag' => 'dt')),
				array('ViewHelper'),
				array('HtmlTag2', array('class' => 'element-wrapper'))				
			),
			'label'                       => 'Select Background Color'
		));

		$this->addElement('text', 'background_image', array(
			'label'       => 'Background Image',
			'description' => 'Here you can enter URL for a background image',
			'attribs' => array('size' => '50px')
		));

		$this->addElement('file', 'background_file', array('label' => 'or Browse from your computer'));
		$this->getElement('background_file')->addValidator('Size', false, '2MB');

		$this->addElement('select', 'background_repeat', array(
			'label'        => 'Repeat',
		));

		$this->addElement('select', 'background_position', array(
			'label'        => 'Position',
		));

		$this->addElement('select', 'background_attachment', array(
			'label'        => 'Movement',
		));
		
		$this->addElement('hidden', 'background');

		$this->addDisplayGroup(array(
			'background_color'
		), 'color');

		$this->addDisplayGroup(array(
			'background_image',
			'background_file',
			'background_repeat',
			'background_position',
		    'background_attachment'
		), 'options');

		$this->getDisplayGroup('color')->setDecorators(array(
			'FormElements',
			new Zend_Form_Decorator_Fieldset(array('class' => 'col1'))
		));

		$this->getDisplayGroup('options')->setDecorators(array(
			'FormElements',
			new Zend_Form_Decorator_Fieldset(array('class' => 'col2', 'legend' => 'Or Choose'))
		));

		Engine_Api::_()->ynprofilestyler()->bindRuleIds($this);
	}
}