<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Widget extends Zend_Form
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
					'id'  => 'background_color-element'
				)),
				array('Label', array('tag' => 'dt')),
			),
			'label'                       => 'Select Background Color'
		));

		$this->addElement('text', 'background_image', array('label' => 'Image'));

		$this->addElement('text', 'border_color', array('label' => 'Border Color'));

		$this->addElement('select', 'border_style', array(
			'label'        => 'Border Style',
		));

		$this->addElement('text', 'border_width', array('label' => 'Border Width'));

		Engine_Api::_()->ynprofilestyler()->bindRuleIds($this);
	}
}