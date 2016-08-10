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

class Store_Form_Options_Search extends Engine_Form
{
	public function init()
  {
    $this
      ->setAttrib('class', 'store_filter_form inner')
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag'   => 'div',
                                      'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag'   => 'div',
                                       'class' => 'clear'))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Text', 'name', array(
      'label' => 'Title'
    ));

    // Init submit
    $this->addElement('Button', 'search', array(
      'label' => 'Search',
      'type' => 'submit',
      'style' => 'padding:2px'
    ));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));
  }
}