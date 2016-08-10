<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  09.12.11 11:33 TeaJay $
 * @author     Taalay
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Checkin/externals/scripts/core.js')
    ->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');

  $this->headTranslate(array('CHECKIN_There are no locations'));
?>

<?php
  $id = uniqid('checkin_event_map_');
  $owner_mode = ($this->subject->user_id == $this->viewer->getIdentity());
?>

<div id="<?php echo $id; ?>">
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      var eventMarker = <?php echo $this->jsonInline($this->placeInfo); ?>;
      checkin_map.event_owner_mode = <?php echo $this->jsonInline($owner_mode); ?>;
      checkin_map.get_event_loc_url = "<?php echo $this->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'get-event-location', 'format' => 'json', 'event_id' => $this->subject->event_id), 'default'); ?>";
      checkin_map.set_event_loc_url = "<?php echo $this->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'set-event-location', 'format' => 'json', 'event_id' => $this->subject->event_id), 'default'); ?>";
      checkin_map.initEventMap('<?php echo $id; ?>', eventMarker);
    });
  </script>

  <div class="checkin_event_location">
    <input type="text" name="event_location" class="edit_location" value="<?php echo $this->subject->location ?>"/>
    <div class="get_location" title="<?php echo $this->translate('CHECKIN_Get location'); ?>"></div>
    <div class="clr"></div>

    <div class="event_locations display_none"></div>
  </div>

    <div class="checkin_event_info display_none">
    <a class="checkin_label display_block" href="javascript://" title="<?php echo ($owner_mode) ? $this->translate('CHECKIN_Edit location'): $this->placeInfo['vicinity']; ?>"></a>
  </div>

  <div style="height: 5px;"></div>

  <div class="checkin_event_map" style="height: 200px;"></div>
</div>