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

class Page_Form_Admin_Manage_Filter extends Engine_Form
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
    
		$category = new Zend_Form_Element_Select('category');
    $category
      ->setLabel('Category')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($topLevelOptions);


    $owner = new Engine_Form_Element_Text('owner');
    $owner->setLabel('Owner')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $this->addElements(array(
      $title,
      $owner,
      $category,
    ));


    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0)) {
      $packagesRows = Engine_Api::_()->getItemTable('page_package')->getEnabledPackages();
      $packages = array(-1=>'', 0=>'Unpackaged');
      foreach ($packagesRows as $row) {
        $packages[$row->getIdentity()] = $row->getTitle();
      }

      $package = new Zend_Form_Element_Select('package');
      $package
        ->setLabel('Package')
        ->clearDecorators()
        ->addDecorator('ViewHelper')
        ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
        ->addDecorator('HtmlTag', array('tag' => 'div'))
        ->setMultiOptions($packages);

      $this->addElement($package);
    }

    $approved = new Zend_Form_Element_Select('approved');
    $approved
      ->setLabel('Approved')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '-1' => '',
        '0' => 'Not Approved',
        '1' => 'Approved',
      ))
      ->setValue('-1');
      
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
        '1' => 'Featured',
      ))
      ->setValue('-1');

    $ipp = new Zend_Form_Element_Select('ipp');
    $ipp
      ->setLabel('Items Per Page')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
      '20' => '20',
      '50' => '50',
      '100' => '100',
      '200' => '200',
      '500' => '500',
      '1000' => '1000',
    ))
      ->setValue('2');

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
      $approved,
      $featured,
      $ipp,
      $submit,
    ));

    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}