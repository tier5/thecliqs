<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'page';

  private $set = array();

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

//    $customFields = new Page_Form_Custom_Fields();
//    $customFields->removeElement('submit');
//    $customFields->setIsCreation(true);
//    $this->addSubForms(array(
//      'fields' => $customFields
//    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'order' => 10000,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $setInfo = Engine_Api::_()->page()->getSetInfo();
    $set = array();
    $setOptions = array(0=>'Select');
    foreach ($setInfo as $cSet) {
      if (!isset($set[$cSet['set_id']])) {
        $set[$cSet['set_id']] = array(
          'info' => array('id'=>$cSet['set_id'], 'caption' =>$cSet['caption']),
          'items' => array()
        );
        $setOptions[$cSet['set_id']] = $cSet['caption'];
      }
      $set[$cSet['set_id']]['items'][$cSet['cat_id']] = array('id'=>$cSet['cat_id'], 'caption'=>$cSet['cat_caption']);
    }
    $this->set = $set;
    $this->addElement('select', 'setId',
      array('id' => 'set', 'label'=>'Set',
        'decorators' => array(
          'ViewHelper',
          array('Label', array('tag' => 'span')),
          array('HtmlTag', array('tag' => 'li'))
        ),
      )
    );
    $this->getElement('setId')->setMultiOptions($setOptions);
    $this->getPageTypeElement();
  }

  public function getPageTypeElement()
  {
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');

    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) )
      return;

    if(count($this->getSetInfo()) == 1) {
    $options = $profileTypeFields['profile_type']->getOptions();

    if( count($options) <= 1 ) {
      return;
    }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }
    }

    $htmlTagOptions = array('tag' => 'li', 'id'=>'category-container');

    $this->addElement('Select', 'profile_type', array(
      'label' => 'Category',
      'order' => 1,
      'class'=>'field_toggle' .' '. 'parent_0 option_0 field_'.$profileTypeFields['profile_type']->field_id,
      'onchange'=>'changeFields($(this));',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', $htmlTagOptions)
      ),
      'multiOptions' => $multiOptions,
    ));

    return $this->profile_type;
  }

  /**
   * @return array
   */
  public function getSetInfo(){
    return $this->set;
  }
}