<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view-map.tpl  01.12.11 16:00 TeaJay $
 * @author     Taalay
 */
?>

<?php if ($this->markers): ?>
	<?php
	  $this->headScript()
      ->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false')
      ->appendFile($this->baseUrl() . '/application/modules/Checkin/externals/scripts/core.js');
  ?>

	<script type="text/javascript">
    en4.core.runonce.add(function() {
      checkin_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
    });
	</script>
  <div style="display: inline;">
    <div id="map_canvas" style="width: 640px; height: 460px; margin: 10px 0 0 10px; float: left;"></div>
    <div class="checkin_users">
      <div class="list">
        <?php foreach($this->users as $user) : ?>
          <div class="item">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb_icon'), array('title' => $user->getTitle()))?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>