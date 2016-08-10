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

class Store_Form_Product_Search extends Fields_Form_Search
{
  protected $_fieldType = 'store_product';

	public function init()
  {
  	parent::init();

    $translate = Zend_Registry::get('Zend_Translate');

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box store_products_browse_filters field_search_criteria',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'products'), 'store_general', true))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browsestore_products_criteria store_products_browse_filters');


  	$this->addElement('Text', 'search', array(
      'label' => 'Search',
    	'order' => 1,
      'class' => 'search',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->addElement('Text', 'min_price', array(
    	'order' => 2,
      'value' => $translate->_('STORE_min'),
      'class' => 'price_input',
      'style' => 'color: #999999;',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Text', 'max_price', array(
    	'order' => 3,
      'value' => $translate->_('STORE_max'),
      'class' => 'price_input',
      'style' => 'color: #999999;',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array(
      'min_price',
      'max_price',
    ), 'price',
      array('class' => 'price', 'legend' => 'Price')
    );

    $price_group = $this->getDisplayGroup("price");
    $price_group->setOrder(4);

    $this->addElement('Button', 'submit', array(
    	'label' => 'Search',
    	'order' => 10000,
    	'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));

    $this->getProductTypeElement();
  }

  public function getProductTypeElement()
  {
    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if( count($profileTypeFields) !== 1 && !isset($profileTypeFields['profile_type']) ) return;

    $options = $profileTypeFields['profile_type']->getOptions();

    if( count($options) <= 1 ) {
      if( count($options) == 1 ) {
        $this->_topLevelId = $profileTypeFields['profile_type']->field_id;
        $this->_topLevelValue = $options[0]->option_id;
      }
      return 0;
    }


    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('Select', 'profile_type', array(
      'label' => 'STORE_Product Type',
      'order' => 5,
      'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_'  . $profileTypeFields['profile_type']->field_id  . ' ',
      'onchange' => 'changeFields($(this));',
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