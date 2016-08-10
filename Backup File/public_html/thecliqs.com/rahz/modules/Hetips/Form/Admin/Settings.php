<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2012-03-31 14:00 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Form_Admin_Settings extends Engine_Form
{
  protected  $_type;

  public function __construct($type)
  {
    $this->_type = $type;
    parent::__construct();
  }

  public function init()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'hetips')->getSettings($this->_type);
    $translate = Zend_Registry::get('Zend_Translate');


    $this->setTitle('Settings Tips');

    $this->addElement('Radio', $this->_type.'_how_display', array(
      'label' => $translate->translate('HETIPS_ADMIN_HOW_DISPLAY_TIPS'),
      'multiOptions' => array(
        'hetips_line' => 'Comma separated',
        'hetips_own_row' => 'Each field on own row width ...'
      ),
      'value' => $settings[$this->_type.'_how_display']
    ));

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      $this->addElement('Checkbox', $this->_type.'_likes_count', array(
        'description' => $translate->translate('HETIPS_ADMIN_LIKES_COUNTER'),
        'value' => $settings[$this->_type.'_likes_count']
      ));
    } else {
      Engine_Api::_()->getDbTable('settings', 'hetips')->setSettings(array($this->_type.'_likes_count' => 0));
    }

    $this->addElement('Checkbox', $this->_type.'_show_labels', array(
      'description' => $translate->translate('HETIPS_ADMIN_SHOW_LABELS'),
      'value' => $settings[$this->_type.'_show_labels']
    ));

    if ($this->_type == 'user') {
      $this->addElement('Checkbox', 'user_display_friends', array(
        'description' => $translate->translate('HETIPS_ADMIN_DISPLAY_FRIENDS'),
        'value' => $settings['user_display_friends']
      ));
    }

    if ($this->_type == 'page') {
      $this->addElement('Checkbox', 'page_members_like', array(
        'description' => $translate->translate('HETIPS_ADMIN_MEMBERS_LIKE_THIS'),
        'value' => $settings['page_members_like']
      ));
    }

    if ($this->_type == 'group') {

      $this->addElement('Checkbox', 'group_display_friends', array(
        'description' => $translate->translate('HETIPS_ADMIN_DISPLAY_FRIENDS'),
        'value' => $settings['group_display_friends']
      ));

      $this->addElement('Checkbox', 'group_members_count', array(
        'description' => $translate->translate('HETIPS_ADMIN_MEMBERS_COUNT'),
        'value' => $settings['group_members_count']
      ));
    }

    $this->addElement('Button', 'saveSettings', array(
      'label' => 'Save',
      'type' => 'submit',
      'order' => 999
    ));

  }
}