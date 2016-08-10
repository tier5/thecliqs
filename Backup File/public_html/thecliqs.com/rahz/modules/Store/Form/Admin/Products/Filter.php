<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Products_Filter extends Engine_Form
{
	public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    $title = new Zend_Form_Element_Text('title');
    $title
      ->setLabel('Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('store_product');
    //$metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta('page');
    $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions('store_product');

    $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
  	$topLevelFields = array();
    foreach( $topLevelMaps as $map ) {
      $field = $map->getChild();
      $topLevelFields[$field->field_id] = $field;
    }

    $topLevelField = array_shift($topLevelFields);
  	$topLevelOptions = array( 0 => '', -1 => 'Uncategorized');
    foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
      $topLevelOptions[$option->option_id] = $option->label;
    }

		$category = new Zend_Form_Element_Select('category');
    $category
      ->setLabel('STORE_Category')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($topLevelOptions);

		if ( Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') )
		{
			$mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('page');
			$optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions('page');

			$topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
			$topLevelFields = array();
			foreach( $topLevelMaps as $map ) {
				$field = $map->getChild();
				$topLevelFields[$field->field_id] = $field;
			}

			$topLevelField = array_shift($topLevelFields);
			$topLevelOptions = array( 0 => '', -1 => 'Uncategorized');
			foreach( $optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option ) {
				$topLevelOptions[$option->option_id] = $option->label;
			}

			$profile_type = new Zend_Form_Element_Select('profile_type');
			$profile_type
				->setLabel('Store Type')
				->clearDecorators()
				->addDecorator('ViewHelper')
				->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
				->addDecorator('HtmlTag', array('tag' => 'div'))
				->setMultiOptions($topLevelOptions)
				->setValue('-1');

				$this->addElement($profile_type);
		}

    $featured = new Zend_Form_Element_Select('featured');
    $featured
      ->setLabel('Featured')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '-1' => '',
        '0' => 'Not Featured',
        '1' => 'STORE_Featured',
      ))
      ->setValue('-1');

    $sponsored = new Zend_Form_Element_Select('sponsored');
    $sponsored
      ->setLabel('Sponsored')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '-1' => '',
        '0' => 'Not Sponsored',
        '1' => 'STORE_Sponsored',
      ))
      ->setValue('-1');

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    $this->addElements(array(
      $title,
      $category,
      $featured,
      $sponsored,
      $submit,
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}