<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _checkin_header.tpl 2011-12-09 17:53 taalay $
 * @author     Taalay
 */


// Wall Scripts 
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/webcam/webcam.js');

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
  'WALL_LOADING',
  'WALL_STREAM_EMPTY_VIEWALL',
  'WALL_EMPTY_FEED',
  'WALL_CAMERA_FREEZE',
  'WALL_CAMERA_CANCEL',
  'WALL_CAMERA_UPLOAD',
  'WALL_COMPOSE_CAMERA'
);

$this->headTranslate($translate_list);
?>

<script type="text/javascript">
  Wall.liketips_enabled = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.liketips', true)?>;
  Wall.rolldownload = 0;
  Wall.dialogConfirm = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.dialogconfirm', true)?>;
</script>
