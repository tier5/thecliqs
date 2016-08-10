<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Donation_Form_Transaction_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag'   => 'div',
                                      'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag'   => 'div',
                                       'class' => 'clear'))
      ->setAttribs(array(
        'id'    => 'search_form',
        'class' => 'donation_filter_form inner',
      ));

    // Element: query
    $this->addElement('Text', 'name', array(
      'label' => 'Name',
    ));
    $type = new Zend_Form_Element_Select('type');
    $type->setLabel('Type')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
      '0' => 'All Donations',
      'charity' => 'Charities',
      'project' => 'Projects',
      'fundraise' => 'Fundraisings'
    ));
    $this->addElement($type);

    // Element: order
    $this->addElement('Hidden', 'order', array(
      'value' => 'creation_date',
      'order' => 10004,
    ));


    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'value' => 'DESC',
      'order' => 10005,
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Search',
      'type'  => 'submit',
      'style' => 'padding: 2px'
    ));
  }
}