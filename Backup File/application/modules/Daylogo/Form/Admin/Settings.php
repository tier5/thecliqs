<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2012-08-16 16:33 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('DAYLOGO_SETTINGS_TITLE')
      ->setDescription('DAYLOGO_SETTINGS_DESCRIPTION')
      ->setAttrib('id', 'daylogo_settings_form');

    // Element: max width
    $this->addElement('Text', 'maxwidth', array(
      'label' => 'DAYLOGO_MAXWIDTH_TITLE',
      'required' => true,
      'allowEmpty' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('daylogo.maxwidth'),
    ));

    // Element: max height
    $this->addElement('Text', 'maxheight', array(
      'label' => 'DAYLOGO_MAXHEIGHT_TITLE',
      'required' => true,
      'allowEmpty' => false,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('daylogo.maxheight'),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'DAYLOGO_SAVE_CHANGES',
      'type' => 'submit',
    ));
  }
}