<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: tag.tpl 2011-11-17 17:53 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Checkin/externals/scripts/composer_checkin.js');

  $this->headTranslate(array('CHECKIN_Share location', 'CHECKIN_Where are you?', 'CHECKIN_%s were here'));
?>

<script type="text/javascript">

  Wall.runonce.add(function (){
    var defaultLocation = <?php echo ($this->subject()) ? $this->checkinDefaultLocation($this->subject()) : $this->checkinDefaultLocation($this->viewer()) ?>;
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var checkin = new Wall.Composer.Plugin.Checkin({}, defaultLocation);
    feed.compose.addPlugin(checkin);
    checkin.suggestUrl = <?php echo $this->jsonInline($this->url(array(''), 'default')); ?>;
  });

</script>

<div class="display_none">
  <div class="checkinWallShareLocation">
    <a class="checkinShareLoc wall_liketips" href="javascript://" rev="checkin"></a>
    <a class="checkinLocationInfo display_none" href="javascript://"></a>
    <input type="text" name="location_info" class="checkinEditLocation display_none"/>
    <span class="checkinLoader display_none"><?php echo $this->translate('CHECKIN_Getting location...'); ?></span>
    <div class="clr"></div>
  </div>

  <div class="checkin_choice_cont_tpl" style="opacity: 0; display: none;">
    <div class="checkin-autosuggest-list">
      <ul class="checkin-autosuggest"></ul>
    </div>
    <div class="checkin-scroller-box">
      <div class="checkin-scroller"></div>
    </div>
    <div class="clr"></div>
    <div class="checkin-autosuggest-map display_none"></div>
    <div style="height: 0px; display: block;"></div>
  </div>

  <ul>
    <li class="checkin_choice_tpl">
      <div class="autocompleter-choice">
        <img src="" class="checkin_choice_icon"/>
        <div class="checkin_choice_label"></div>
        <div class="checkin_choice_info"></div>
        <div class="clr"></div>
      </div>
    </li>
  </ul>

  <div class="checkin_checkmap_tpl checkin_custom_place">
    <?php echo $this->translate('CHECKIN_There are no places found by your keywords'); ?>
    <a class="checkin_show_map" href="javascript://"><?php echo $this->translate('CHECKIN_Mark on the map'); ?></a>
  </div>
</div>