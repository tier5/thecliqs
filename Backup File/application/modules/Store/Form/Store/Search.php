<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 07.07.11 14:29 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Form_Store_Search extends Fields_Form_Search
{
	protected $_fieldType = 'page';

	public function init()
  {
    parent::init();

    $this->addAttribs(array('id' => 'filter_form', 'class' => 'global_form_box'));
  	$this->loadDefaultDecorators();

  	$this->addElement('Text', 'keyword', array(
    	'order' => -3,
  		'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->addElement('Button', 'submit', array(
    	'label' => 'Search',
    	'order' => 10000,
    	'type' => 'submit',
    	'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->getPageTypeElement();
  }

	public function getPageTypeElement()
  {
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;

    $options = $profileTypeFields['profile_type']->getOptions();
    if( count($options) <= 1 ) {
      return;
    }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('Select', 'profile_type', array(
      'label' => 'Page Type',
      'order' => 1,
      'class'=>'field_toggle' .' '. 'parent_0 option_0 field_'.$profileTypeFields['profile_type']->field_id,
      'onchange'=>'changeFields($(this));',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'multiOptions' => $multiOptions,
    ));

    return $this->profile_type;
  }
}