<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Change.php 2010-08-31 16:05 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Team_Change extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Change Page Team Type')
      ->setMethod('post')
      ->setDescription('Are you sure you want to change page team type?')
      ->setAttrib('class', 'global_form_popup');


    $this->addElement('Button', 'submit', array(
      'label' => 'Change',
      'type' => 'submit',
      'ignore' => true,
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements'
      )
    ));
  }
}