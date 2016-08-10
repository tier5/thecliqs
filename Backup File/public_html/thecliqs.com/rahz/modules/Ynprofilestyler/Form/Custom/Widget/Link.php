<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Widget_Link extends Engine_Form
{
	public function init()
	{
		$this->addElement('hidden', 'color', array(
			'required'                    => false,
			'ignore'                      => true,
			'autoInsertNotEmptyValidator' => false,
			'decorators'                  => array(
				array(
					array('data' => new Zend_Form_Decorator_HtmlTag(
						array('tag' => 'div', 'class' => 'link-color color'))),
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

		Engine_Api::_()->ynprofilestyler()->bindRuleIds($this, 'widget-link');
	}
}