<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Products_Search extends Engine_Form
{
  public function init()
  {
    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form');

    $this
      ->setAttribs(array(
      'id' => 'search_form',
      'class' => 'global_form_box',
    ));

    $name = new Zend_Form_Element_Text('name');
    $name
      ->setLabel('Title')
      ->setOrder(1)
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $store_title = new Zend_Form_Element_Text('store_title');
    $store_title
      ->setLabel('Store')
      ->setOrder(2)
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->setOrder(5)
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002
    ));

    $elements = array($name);
    if ($isPageEnabled) {
      $elements = array_merge($elements, array($store_title));
    }
    $elements = array_merge($elements, array($submit));

    $this->addElements($elements);


    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}