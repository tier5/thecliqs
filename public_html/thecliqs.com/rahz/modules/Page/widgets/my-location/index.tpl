<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>
<?php
$prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
?>
<script src='<?php echo $prefix ?>maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>
<script type="text/javascript">
  window.addEvent('domready', function(){
    url = "<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>";
    my_location.init(url);
    $('my_location_loading').addClass('hidden');
  });
</script>

<?php if( $this->paginator) : ?>
<ul class="my_location_page_list">
  <?php foreach($this->paginator as $page) : ?>
  <li>

        <div class="my_location_page_list_title"><a href="<?php echo $page->getHref(); ?>"><?php echo $page->getTitle(); ?></a></div>

        <div class="my_location_page_info">
          <?php if ($page->country || $page->city || $page->state): ?>
          <?php echo $page->displayAddress(); ?>
          </br>
          <?php endif; ?>
          <div class="distance">
            <?php echo $this->translate( "approximately %s %s", $this->distance($page->distance), $this->unit );?>
          </div>
          <?php echo $this->translate("Submitted by"); ?>
          <a href="<?php echo $page->getOwner()->getHref(); ?>"><?php echo $page->getOwner()->getTitle(); ?></a>,
          <br/><?php echo $this->translate("updated"); ?>
          <?php echo $this->timestamp($page->modified_date); ?>
        </div>

  </li>
  <?php endforeach; ?>
</ul>
<?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/mylocation.tpl","page")); ?>
  <?php endif; ?>
<script type="text/javascript">
  window.addEvent('domready', function(){
    my_location.set_my_marker("<?php echo $this->my_latitude; ?>", "<?php echo $this->my_longitude; ?>");
    my_location.show_pages(<?php echo $this->markers; ?>, <?php echo $this->bounds; ?>);
    $('my_address').value = "<?php echo $this->my_address;?>";
  });
</script>

<?php else : ?>

<script type="text/javascript">

  window.addEvent('domready', function(){
    my_location.get_geolocation();

    $('my_address_submit').addEvent('click', function(e){
      e.stop();
      my_location.set_my_location_address($('my_address').value);

    });
  });


</script>


<div class="my_location_container">
  <table width="100%">
    <thead>
    <tr><td colspan="2">
  <form action="">
    <input type="text" id="my_address">
    <button type="submit" id="my_address_submit">Submit</button>
    <div id="my_location_loading" class="hidden"><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/></div>
  </form>
    </td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td width="35%"><div id="my_location_pages"> <?php echo $this->translate('There is no pages')?> </div></td>
      <td><div id="my_map_canvas" class="my_location_map" style="width:95%; height:300px;">  </div></td>
    </tr>
  </tbody>
  </table>
</div>


<?php endif; ?>