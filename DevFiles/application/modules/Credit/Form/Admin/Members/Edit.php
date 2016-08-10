<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 05.01.12 18:40 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_Members_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit member\'s credit')
      ->setDescription('CREDIT_ADMIN_EDIT_MEMBER_CREDITS_DESC');

    $this->addElement('Text', 'current_credit', array(
      'label' => 'Current Credits',
      'description' => 'Current'
    ));

    $this->addElement('Text', 'earned_credit', array(
      'label' => 'Earned Credits',
      'description' => 'Earned'
    ));

    $this->addElement('Text', 'spent_credit', array(
      'label' => 'Spent Credits',
      'description' => 'Spent'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}