<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Edit.php 11.04.12 16:59 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Taxes_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Tax')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('post');
    ;

    $this->addElement('Text', 'title', array(
      'label' => 'Name',
      'required' => true,
      'allowEmpty' => false
    ));

    $this->addElement('Text', 'percent', array(
      'label' => 'Percent',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0)
      )
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Tax',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }
}
