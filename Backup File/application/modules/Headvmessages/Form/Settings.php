<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Settings.php 20.02.12 10:58 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvmessages_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    //$settings = Engine_Api::_()->getApi('settings', 'core');
    /*$this->setTitle('Global Settings')
      ->setDescription('HEADVMESSAGES_Admin general settings description');*/

    /*$this->addElement('Checkbox', 'enabled_adv_messages', array(
      'label' => 'HEADVMESSAGES_Enable messages',
      'description' => '',
      'value' => $settings->getSetting('headvmessages.enabled', 0)
    ));

    $this->addElement('Checkbox', 'enabled_enter', array(
      'label' => 'HEADVMESSAGES_Enable messages enter send',
      'description' => '',
      'value' => $settings->getSetting('headvmessages.enter.send.enabled', 0)
    ));*/

    // Add submit button
    /*$this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));*/
  }
}
