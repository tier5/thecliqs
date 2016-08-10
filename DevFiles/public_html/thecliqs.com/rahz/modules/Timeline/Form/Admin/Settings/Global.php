<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Global Settings')
      ->setDescription('TIMELINE_GLOBAL_SETTINGS_DESCRIPTION');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Select', 'usage', array(
      'label' => 'TIMELINE_Timeline Usage',
      'description' => 'TIMELINE_USAGE_ELEMENT_DESCRIPTION',
      'multiOptions' => array(
        'choice' => 'TIMELINE_Let members to change their default profile to Timeline',
        'force' => 'TIMELINE_Force members to use Timeline instead of the system\'s default profile page',
      ),
      'value' => $settings->__get('timeline.usage')
    ));
    // @TODO langs
    $this->addElement('Select', 'usage_on_page', array(
      'label' => 'Page Timeline Usage',
      'description' => 'Page Timeline Usage',
      'multiOptions' => array(
        'choice' => 'Let page owners to change default profile on their\'s pages to Timeline',
        'force' => 'Force page owners to use Timeline instead of the default profiles on their\'s pages',
      ),
      'value' => $settings->__get('timeline.usageonpage')
    ));

    $this->addElement('Text', 'menuitems', array(
      'label' => 'TIMELINE_Timeline Widgets',
      'description' => 'TIMELINE_WIDGETS_DESCRIPTION',
      'value' => $settings->__get('timeline.menuitems', 20)
    ));

    $this->usage->getDecorator('Description')->setOptions(array('placement' => 'append'));


    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}
