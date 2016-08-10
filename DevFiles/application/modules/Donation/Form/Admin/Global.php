<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 22.08.12
 * Time: 16:48
 * To change this template use File | Settings | File Templates.
 */
class Donation_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('DONATION_FORM_ADMIN_GLOBAL_TITLE')
      ->setDescription('DONATION_FORM_ADMIN_GLOBAL_DESCRIPTION');

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $this->addElement('Radio', 'donation_enable_charities', array(
      'label' => 'DONATION_Enable Charities?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => $settings->getSetting('donation.enable.charities',1),
    ));

    $this->addElement('Radio', 'donation_enable_projects', array(
      'label' => 'DONATION_Enable Projects?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => $settings->getSetting('donation.enable.projects',1),
    ));

    $this->addElement('Radio', 'donation_enable_fundraising', array(
      'label' => 'DONATION_Enable Fundraising?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
      'value' => $settings->getSetting('donation.enable.fundraising',1),
    ));

    $this->addElement('Checkbox', 'donation_auto_approve', array(
      'label' => 'DONATION_New Donations Approval',
      'description' => 'DONATION_SETTING_APPROVAL',
      'value' => $settings->getSetting('donation.auto.approve',1),
    ));

    $this->addElement('Text', 'donation_browse_count', array(
      'label' => 'DONATION_ITEMS_PER_PAGE_LABEL',
      'description' => 'DONATION_ITEMS_PER_PAGE_DESCRIPTION',
      'value' => $settings->getSetting('donation_browse_count',10),
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(1),
      ),
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Text', 'donation_donors_count', array(
      'label' => 'DONATION_TOP_DONORS_COUNT_LABEL',
      'description' => 'DONATION_TOP_DONORS_COUNT_DESCRIPTION',
      'value' => $settings->getSetting('donation.donors.count',4),
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(1),
      ),
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Text', 'donation_supporters_count', array(
      'label' => 'DONATION_SUPPORTERS_COUNT_LABEL',
      'description' => 'DONATION_SUPPORTERS_COUNT_DESCRIPTION',
      'value' => $settings->getSetting('donation.supporters.count',9),
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(1),
      ),
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
