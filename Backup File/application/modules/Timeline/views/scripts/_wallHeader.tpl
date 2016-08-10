<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _wallHeader.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

// Wall Scripts
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Timeline/externals/scripts/wall_core.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/webcam/webcam.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hecore/externals/scripts/core.js');


// Support Other Plugins
$modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();


// Translates
$translate_list = array(
  'WALL_CONFIRM_ACTION_REMOVE_TITLE',
  'WALL_CONFIRM_ACTION_REMOVE_DESCRIPTION',
  'WALL_CONFIRM_COMMENT_REMOVE_TITLE',
  'WALL_CONFIRM_COMMENT_REMOVE_DESCRIPTION',
  'WALL_CONFIRM_LIST_REMOVE_TITLE',
  'WALL_CONFIRM_LIST_REMOVE_DESCRIPTION',
  'WALL_LIKE',
  'WALL_UNLIKE',
  'Save',
  'Cancel',
  'delete',
  'Show %1s',
  'Earlier in %1s',
  'WALL_LOADING',
  'WALL_STREAM_EMPTY_VIEWALL',
  'WALL_EMPTY_FEED',
  'WALL_CAMERA_FREEZE',
  'WALL_CAMERA_CANCEL',
  'WALL_CAMERA_UPLOAD',
  'WALL_COMPOSE_CAMERA',

  'WALL_TWITTER_RETWEETED',
  'WALL_CONFIRM_TWITTER_DELETE_TITLE',
  'WALL_CONFIRM_TWITTER_DELETE_DESCRIPTION',
  'WALL_SENDING',
  'WALL_Share',
  'WALL_Who are you with?',
  'WALL_with %1$s',
  'WALL_with %1$s and %2$s',
  'WALL_%1$s others',
  'WALL_Link to this post',
  'WALL_Copy this link to send a copy of this post to others:',
  'WALL_GO',
  'WALL_No longer seeing this post.',
  'WALL_Undo mute',
  'WALL_CONFIRM_REMOVE_TAG_TITLE',
  'WALL_CONFIRM_REMOVE_TAG_DESCRIPTION',

  'WALL_PRIVACY_USER_EVERYONE',
  'WALL_PRIVACY_USER_NETWORKS',
  'WALL_PRIVACY_USER_MEMBERS',
  'WALL_PRIVACY_USER_OWNER',
  'WALL_PRIVACY_PAGE_EVERYONE',
  'WALL_PRIVACY_PAGE_REGISTERED',
  'WALL_PRIVACY_PAGE_PAGE',

  'WALL_USER_NETWORKS_TAGGED',
  'WALL_USER_MEMBERS_TAGGED',
  'WALL_USER_OWNER_TAGGED',
  'WALL_PAGE_PAGE_TAGGED',

  'TIMELINE_Edit Photo',
  'WALL_CHOOSE_MY_PAGE',
  'WALL_FBPAGE_NO'

);


$services = Engine_Api::_()->wall()->getManifestType('wall_service', true);

foreach ($services as $service) {
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '';
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '_ACTIVE';
  $translate_list[] = 'WALL_STREAM_' . strtoupper($service) . '_LOGIN';
}

$this->headTranslate($translate_list);




?>

<script type="text/javascript">

  Wall.liketips_enabled = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.liketips', true)?>;
  Wall.rolldownload = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.rolldownload', true)?>;
  Wall.dialogConfirm = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.dialogconfirm', true)?>;

  <?php
  if ($this->viewer()->getIdentity()) {

    $services_request = array();
    foreach ($services as $service) {
      $class = Engine_Api::_()->wall()->getServiceClass($service);
      if (!$class) {
        continue;
      }
      $config = array(
        'provider' => $service,
        'enabled' => false,
      );
      $tokenRow = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($this->viewer(), $service);
      if ($tokenRow) {
        $config = array_merge($config, $tokenRow->publicArray());
        $services_request[$service] = true;
      }

      $setting_key = 'share_' . $service . '_enabled';
      $setting = Engine_Api::_()->wall()->getUserSetting($this->viewer());

      if (isset($setting->{$setting_key}) && $setting->{$setting_key}) {
        $config['share_enabled'] = true;
      }

      echo 'Wall.runonce.add(function (){ Wall.services.add("' . $service . '", new Wall.Service.' . ucfirst($service) . '(' . $this->jsonInline($config) . ')); });';

    }
    if (count($services_request)) {
      echo "Wall._servicesRequest = new Wall.ServicesRequest(" . $this->jsonInline($services_request) . "); Wall.runonce.add(function (){ Wall._servicesRequest.send(); });";
    }


  }
  ?>

</script>
