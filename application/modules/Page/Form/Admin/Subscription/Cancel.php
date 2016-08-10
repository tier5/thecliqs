<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Cancel.php 11.08.11 16:52 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Subscription_Cancel extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Cancel Subscription')
      ->setDescription('Note: this will attempt to cancel the ' .
          'recurring payment regardless of current subscription status.')
      ->setAttrib('class', 'global_form_popup')
      ;

    // Token
    $this->addElement('Hash', 'token');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Cancel Subscription',
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
    $button_group = $this->getDisplayGroup('buttons');
  }
}