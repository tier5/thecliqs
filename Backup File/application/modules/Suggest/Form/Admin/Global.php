<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $suggestApi = Engine_Api::_()->getApi('core', 'suggest');

    $this
      ->setTitle('suggest_global_settings_title')
      ->setDescription('suggest_global_settings_descr');

    $this->addElement('Text', 'suggest_facebook_app_id', array(
      'label' => 'SUGGEST_FACEBOOK_APP_ID_TITLE',
      'description' => 'SUGGEST_FACEBOOK_APP_ID_DESC',
      'value' => $settings->getSetting('suggest.facebook.app.id', ''),
      'escape' => false
    ));

    $desc_decorator = $this->getElement('suggest_facebook_app_id')->getDecorator('description');
    if ($desc_decorator) {
      $desc_decorator->setEscape(false);
    }

    $this->addElement('Text', 'suggest_widget_item_count', array(
      'label' => 'suggest_widget_item_count_title',
        'description' => 'suggest_widget_item_count_descr',
      'value' => $settings->getSetting('suggest.widget.item.count', 6)
    ));

    $this->addElement('Checkbox', 'suggest_friend_add', array(
      'label' => 'suggest_global_setting_friend_add_title',
      'description' => 'suggest_global_setting_friend_add_descr',
      'value' => $settings->getSetting('suggest.friend.add', 1)
    ));

    $this->addElement('Checkbox', 'suggest_friend_confirm', array(
      'label' => 'suggest_global_setting_friend_confirm_title',
      'description' => 'suggest_global_setting_friend_confirm_descr',
      'value' => $settings->getSetting('suggest.friend.confirm', 1)
    ));

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('group')) {

      $this->addElement('Checkbox', 'suggest_group_join', array(
        'label' => 'suggest_global_setting_group_join_title',
        'description' => 'suggest_global_setting_group_join_descr',
        'value' => $settings->getSetting('suggest.group.join', 1)
      ));

      $this->addElement('Checkbox', 'suggest_group_accept', array(
        'label' => 'suggest_global_setting_group_accept_title',
        'description' => 'suggest_global_setting_group_accept_descr',
        'value' => $settings->getSetting('suggest.group.accept', 1)
      ));

    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event')) {

      $this->addElement('Checkbox', 'suggest_event_join', array(
        'label' => 'suggest_global_setting_event_join_title',
        'description' => 'suggest_global_setting_event_join_descr',
        'value' => $settings->getSetting('suggest.event.join', 1)
      ));

      $this->addElement('Checkbox', 'suggest_event_accept', array(
        'label' => 'suggest_global_setting_event_accept_title',
        'description' => 'suggest_global_setting_event_accept_descr',
        'value' => $settings->getSetting('suggest.event.accept', 1)
      ));

    }

    $this->addElement('Checkbox', 'suggest_profile_photo', array(
      'label' => 'suggest_global_setting_suggest_profile_photo_title',
      'description' => 'suggest_global_setting_suggest_profile_photo_descr',
      'value' => $settings->getSetting('suggest.profile.photo', 1)
    ));

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('quiz')) {

      $this->addElement('Checkbox', 'suggest_popup_take_quiz', array(
        'label' => 'suggest_global_setting_suggest_popup_take_quiz_title',
        'description' => 'suggest_global_setting_suggest_popup_take_quiz_descr',
        'value' => $settings->getSetting('suggest.popup.take.quiz', 1)
      ));

    }

    $itemTypes = $suggestApi->getItemTypes();
    unset($itemTypes['album_photo']);
    $popupValues = array();
    if (!empty($itemTypes)) {
      $values = $settings->getSetting('suggest.popup.create', array());
      $values['album'] = isset($values['album']) ? (int)$values['album'] : 0;
      unset($values['photo']);
      foreach ($values as $key => $value) {
        $key = $this->maintainSettings($key, $value);
        if ($key) {
          $popupValues[] = $key;
        }
      }

      $createItemTypes = $itemTypes;
      if (isset($createItemTypes['store_product'])) {
        unset($createItemTypes['store_product']);
      }

      $this->addElement('MultiCheckbox', 'suggest_popup_create', array(
        'label' => 'suggest_global_setting_popup_create_title',
        'description' => 'suggest_global_setting_popup_create_descr',
        'value' => $popupValues,
        'multiOptions' => $createItemTypes
      ));
    }
    
    $itemTypes['user'] = 'User';

	  if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album')) {
      $itemTypes['photo'] = 'Album Photo';
    }

    $linkValues = array();
    $values = $settings->getSetting('suggest.link', array());
    foreach ($values as $key => $value) {
      $key = $this->maintainSettings($key, $value);
      if ($value) {
        $linkValues[] = $key;
      }
    }
    
    $this->addElement('MultiCheckbox', 'suggest_link', array(
      'label' => 'suggest_global_setting_link_title',
      'description' => 'suggest_global_setting_link_descr',
      'value' => $linkValues,
      'multiOptions' => $itemTypes
    ));


    unset($itemTypes['photo']);

    $mixValues = array();
    $values = $settings->getSetting('suggest.mix', array());
    foreach ($values as $key => $value) {
      $key = $this->maintainSettings($key, $value);
      if ($value) {
        $mixValues[] = $key;
      }
    }

    $this->addElement('MultiCheckbox', 'suggest_mix', array(
      'label' => 'suggest_global_setting_mix_title',
      'description' => 'suggest_global_setting_mix_descr',
      'value' => $mixValues,
      'multiOptions' => $itemTypes
    ));


    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function maintainSettings($key, &$value)
  {
    if ( is_array($value) ) {
      foreach ($value as $subkey => $subvalue) {
        return $this->maintainSettings($key . '_' . $subkey, $subvalue);
      }
    } elseif (is_numeric($value) && $value) {
      return $key;
    }

    return '';
  }

}