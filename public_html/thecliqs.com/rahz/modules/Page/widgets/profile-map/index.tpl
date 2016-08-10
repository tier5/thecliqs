<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->markers): ?>
  <?php echo Engine_Api::_()->getApi('gmap', 'page')->getMapJS(); ?>
  <script type="text/javascript">
  window.addEvent('domready', function(){
    pages_map.construct( null, <?php echo $this->markers; ?>, 2, <?php echo $this->bounds; ?> );
  });
  </script>
  <div id="map_canvas" class="page_map"></div>
  <a class="smoothbox" href="<?php echo $this->url(array('page_id' => $this->subject->getIdentity()), 'page_map'); ?>">
    <?php echo $this->translate("View Larger Map"); ?>
  </a>
<?php endif; ?>