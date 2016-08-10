<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 01.06.12 17:48 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Credits_Settings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('STORE_CREDIT_SETTINGS_DESCRIPTION');

    // Elements
    $this->addElement('Radio', 'credits_on_store', array(
      'label' => 'STORE_Credits on Store',
      'description' => 'STORE_Do you want to give store owners a choice of a credit system on product creation?',
      'multiOptions' => array(
        '1' => 'Yes',
        '0' => 'No',
      ),
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
