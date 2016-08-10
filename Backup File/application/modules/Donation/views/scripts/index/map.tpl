<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       09.08.12
 * @time       16:44
 */?>

<?php if ($this->markers): ?>
<h4 class="large_map_head"><?php echo $this->donation->getTitle(); ?></h4>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Donation/externals/scripts/core.js'); ?>
<?php echo Engine_Api::_()->getApi('gmap', 'donation')->getMapJS(); ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    donations_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
  });
</script>
<div id="map_canvas" class="large_map"></div>
<?php endif; ?>