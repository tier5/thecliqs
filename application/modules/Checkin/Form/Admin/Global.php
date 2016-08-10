<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('CHECKIN_Checkin Plugin Settings')
      ->setDescription('CHECKIN_ADMIN_GLOBAL_DESCRIPTION');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'checkin_google_map_key', array(
      'label' => 'CHECKIN_Google Map Key',
      'value' => $settings->getSetting('checkin.google_map_key', ''),
      'style' => 'width: 300px'
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}