<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdvSearch.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Page_Form_AdvSearch extends Engine_Form
{
  protected $_fieldType = 'page';

  public function init()
  {

    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'advanced_search'))

    ;

    $this
      ->setAttribs(array(
      'id' => 'advanced_search_form',
      'class' => 'global_form_box',
    ))
    ;

    $keyword = new Engine_Form_Element_Text('adv_search_keyword');
    $keyword
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('What')
      ->setDescription('(type in keywords, or page name)')
      ->addDecorator('Label')
      ->addDecorator('Description', array('tag' => 'div', 'placement' => 'APPPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_keyword'))
    ;

    $where = new Engine_Form_Element_Text('adv_search_where');
    $where
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Where')
      ->setDescription('(address, city, state, or country)')
      ->addDecorator('Label')
      ->addDecorator('Description', array('tag' => 'div', 'placement' => 'APPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_where'))
    ;

    $unit = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.advsearch.unit', 'Miles');
    $within = new Engine_Form_Element_Select('adv_search_within');
    $within->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Within')
      ->addDecorator('Label')
      ->setDescription('(within)')
      ->addDecorator('Description', array('tag' => 'div', 'placement' => 'APPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_within'))
      ->setMultiOptions(array(
      '20' => '20 ' . $unit,
      '50' => '50 ' . $unit,
      '100' => '100 ' . $unit,
      '250' => '250 ' . $unit,
      '500' => '500 ' . $unit,
      '750' => '750 ' . $unit,
      '1000' => '1000 ' . $unit,
    ))
      ;


    $submit = new Engine_Form_Element_Submit('adv_submit');
    $submit
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Search')
      ->setDescription('More options')
      ->addDecorator('Description', array('tag' => 'a', 'placement' => 'APPPEND', 'href' => 'javascript:void(0);', 'id' => 'page_advanced_search_option'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_submit'))
    ;


    $city = new Engine_Form_Element_Text('adv_search_city');
    $city
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('City')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_city'))
      ;

    $state = new Engine_Form_Element_Text('adv_search_state');
    $state
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('State')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_state'))
    ;

    $country = new Engine_Form_Element_Text('adv_search_country');
    $country
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Country')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_country'))
    ;


    $street = new Engine_Form_Element_Text('adv_search_street');
    $street
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Street')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_street'))
    ;

    $category = $this->getPageTypeElement();

    $featured = new Engine_Form_Element_Checkbox('adv_search_featured');
    $featured
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Featured')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_featured'))
      ;

    $sponsored = new Engine_Form_Element_Checkbox('adv_search_sponsored');
    $sponsored->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Sponsored')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_sponsored'))
      ;

    $approved = new Engine_Form_Element_Checkbox('adv_search_approved');
    $approved->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Approved')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_approved'))
    ;

    $this->addElements(array(
      $keyword,
      $where,
      $within,
      $submit,
      $street,
      $city,
      $state,
      $country,
      $category,
      $featured,
      $sponsored,
      $approved
    ));

    $this->addDisplayGroup(array(
      'adv_search_street',
      'adv_search_city',
      'adv_search_state',
      'adv_search_country',

    ),
      'adv_search_location', array('legend' => ''));

    $this->addDisplayGroup(array(
        'adv_search_category',
        'adv_search_featured',
        'adv_search_sponsored',
        'adv_search_approved',
    ),
      'adv_search_pagetype', array('legend' => ''));

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

    $category = new Engine_Form_Element_Select('adv_search_category');
    $category->clearDecorators()
      ->addDecorator('ViewHelper')
      ->setLabel('Category')
      ->addDecorator('Label')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search_category'))
      ->setMultiOptions($multiOptions)
    ;


    return $category;
  }
}