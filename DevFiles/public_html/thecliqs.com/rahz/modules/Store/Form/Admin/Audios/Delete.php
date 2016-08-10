<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Delete.php 09.09.11 14:57 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Audios_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Audio')
      ->setDescription('Are you sure you want to delete this audio?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('post');
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Audio',
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