<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_TabBar extends Engine_Form
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
				array('HtmlTag2', array('class' => 'form-wrapper'))
			),
			'label'                       => 'Background Color'
		));

		$this->addElement('select', 'background_image', array(
			'label'        => 'Use Images',
            'attribs' => array('id' => 'tabbar_background_image')
		));
		
		$this->addElement('hidden', 'background');

		$this->addElement('select', 'font_family', array(
			'label'        => 'Font Family',
		));

		$this->addElement('select', 'font_size', array(
			'label'        => 'Font Size',
		));

		$this->addElement('select', 'font_weight', array(
			'label'        => 'Font Weight',
		));

		$this->addElement('select', 'font_style', array(
			'label'        => 'Font Style',
		));

		$this->addElement('select', 'text_decoration', array(
			'label'        => 'Text Decoration',
		));

		$this->addElement('hidden', 'color', array(
			'required'                    => false,
			'ignore'                      => true,
			'autoInsertNotEmptyValidator' => false,
			'decorators'                  => array(
				array(
					array('data' => new Zend_Form_Decorator_HtmlTag(
						array('tag' => 'div', 'class' => 'text-color color'))),
				),
				array('HtmlTag', array(
					'tag' => 'dd',
				)),
				array('Label', array('tag' => 'dt')),
				array('ViewHelper'),
				array('HtmlTag2', array('class' => 'form-wrapper'))
			),
			'label'                       => 'Text Color'
		));

		Engine_Api::_()->ynprofilestyler()->bindRuleIds($this);
	}
}