<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: large-map.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->markers): ?>
	<h4 class="large_map_head"><?php echo $this->page->getTitle(); ?></h4>
	<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Page/externals/scripts/core.js'); ?>
	<?php echo Engine_Api::_()->getApi('gmap', 'page')->getMapJS(); ?>
	<script type="text/javascript">
    en4.core.runonce.add(function(){
      pages_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
    });
	</script>
	<div id="map_canvas" class="large_map"></div>
<?php endif; ?>