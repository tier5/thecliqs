<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Package_Filter extends Engine_Form
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

    $title = new Zend_Form_Element_Text('name');
    $title
      ->setLabel('Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
    
    $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('page');
    $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta('page');
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

    $price = new Zend_Form_Element_Text('price');
    $price
      ->setLabel('Price')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

//		$category = new Zend_Form_Element_Select('price');
//    $category
//      ->setLabel('Price')
//      ->clearDecorators()
//      ->addDecorator('ViewHelper')
//      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
//      ->addDecorator('HtmlTag', array('tag' => 'div'))
//      ->setMultiOptions($topLevelOptions);

    $enabled = new Zend_Form_Element_Select('enabled');
    $enabled
      ->setLabel('Enabled')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '-1' => '',
        '0' => 'Disabled',
        '1' => 'Enabled',
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
      $price,
      $enabled,
      $submit,
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}