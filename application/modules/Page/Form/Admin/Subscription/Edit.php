<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 11.08.11 16:52 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Admin_Subscription_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Subscription')
      ;

    $this->setAttrib('class', 'global_form_popup');

    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'description' => 'Note: this is provided for adjustment. Changing this ' .
          'will not have any effect on existing transactions. For example, ' .
          'changing this to "cancelled" will not refund any transactions or ' .
          'cancel any recurring payment profiles, however if it was ' .
          'previously "active," the member will have to create a new ' .
          'subscription. Please use the details link on ' .
          'Manage Subscriptions page to perform these actions.',
      'multiOptions' => array(
        'initial' => 'Initializing',
        'trial' => 'Trial',
        'pending' => 'Payment Pending',
        'active' => 'Active',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired',
        'overdue' => 'Overdue',
        'refunded' => 'Refunded',
      ),
    ));

    $this->addElement('Select', 'active', array(
      'label' => 'Active',
      'description' => 'Is this the current, most relevant subscription for ' .
          'this member? Non-active subscriptions have no effect and ' .
          'are stored for record-keeping purposes.',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
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