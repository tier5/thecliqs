<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminGlobalController.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_AdminGlobalController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('suggest_admin_main', array(), 'suggest_admin_main_global');

    $this->view->form = $form = new Suggest_Form_Admin_Global();

    // if demoadmin
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 1250) {
      $form->getElement('suggest_facebook_app_id')->setValue('******************');
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $suggestApi = Engine_Api::_()->getApi('core', 'suggest');

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $values = $this->getRequest()->getPost();
    if (!$form->isValid($values)) {
      return ;
    }

    $facebookAppId = $values['suggest_facebook_app_id'];
    $settings->setSetting('suggest.facebook.app.id', $facebookAppId);

    $widgetCount = $values['suggest_widget_item_count'];
    $settings->setSetting('suggest.widget.item.count', $widgetCount);

    $profilePhoto = $values['suggest_profile_photo'];
    $settings->setSetting('suggest.profile.photo', $profilePhoto);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('quiz')) {

      $profilePhoto = $values['suggest_popup_take_quiz'];
      $settings->setSetting('suggest.popup.take.quiz', $profilePhoto);

    }

    $itemTypes = $suggestApi->getItemTypes();
    if (isset($itemTypes['album_photo'])) {
      $itemTypes['photo'] = $itemTypes['album_photo'];
      unset($itemTypes['album_photo']);
    }
    $itemTypes = array_keys($itemTypes);

    $suggest_popup_create = (isset($values['suggest_popup_create']) && is_array($values['suggest_popup_create']) ? $values['suggest_popup_create'] : array());
    $suggest_link = (isset($values['suggest_link']) && is_array($values['suggest_link']) ? $values['suggest_link'] : array());
    $suggest_mix = (isset($values['suggest_mix']) && is_array($values['suggest_mix']) ? $values['suggest_mix'] : array());

    $suggestPopupCreate = array_values($suggest_popup_create);
    $suggestLink = array_values($suggest_link);
    $suggestMix = array_values($suggest_mix);

    $suggestFriendAdd = $values['suggest_friend_add'];
    $suggestFriendConfirm = $values['suggest_friend_confirm'];

    $popupCreateValues = array();
    $linkValues = array();
    $mixValues = array();

    foreach ($itemTypes as $type) {
      if ($type == 'photo') {
        continue ;
      }
      if (in_array($type, $suggestPopupCreate)) {
        $popupCreateValues[$type] = 1;
      } else {
        $popupCreateValues[$type] = 0;
      }
    }

    $itemTypes[] = 'user';
    foreach ($itemTypes as $type) {
      if (in_array($type, $suggestLink)) {
        $linkValues[$type] = 1;
      } else {
        $linkValues[$type] = 0;
      }
    }
    foreach ($itemTypes as $type) {
      if ($type == 'photo') {
        continue ;
      }
      if (in_array($type, $suggestMix)) {
        $mixValues[$type] = 1;
      } else {
        $mixValues[$type] = 0;
      }
    }

    $settings->setSetting('suggest.friend.add', $suggestFriendAdd);
    $settings->setSetting('suggest.friend.confirm', $suggestFriendConfirm);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('group')) {

      $suggestGroupJoin = $values['suggest_group_join'];
      $suggestGroupAccept = $values['suggest_group_accept'];

      $settings->setSetting('suggest.group.join', $suggestGroupJoin);
      $settings->setSetting('suggest.group.accept', $suggestGroupAccept);

    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event')) {

      $suggestEventJoin = $values['suggest_event_join'];
      $suggestEventAccept = $values['suggest_event_accept'];

      $settings->setSetting('suggest.event.join', $suggestEventJoin);
      $settings->setSetting('suggest.event.accept', $suggestEventAccept);

    }

    $settings->setSetting('suggest.popup.create', $popupCreateValues);
    $settings->setSetting('suggest.link', $linkValues);
    $settings->setSetting('suggest.mix', $mixValues);
  }
}