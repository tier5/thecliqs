<?php echo Engine_Api::_()->getApi('gmap', 'page')->getMapJS(); ?>

<script type="text/javascript">
  var current_marker = {};
  current_marker.markers = <?php echo $this->markers; ?>;
  current_marker.bounds = <?php echo $this->bounds; ?>;
</script>

<a href="javascript://" class="page_edit_form_map_show" onclick='pages_map.showEditMap(null, current_marker.markers, 2, current_marker.bounds, true);'><?php echo $this->translate('PAGE_Show map'); ?></a>
<a href="javascript://" class="page_edit_form_map_hide display_none" onclick="pages_map.hideEditMap();"><?php echo $this->translate('PAGE_Hide map'); ?></a>
<br/>
<div id="map_canvas" class="page_map display_none" style="width: 500px; height: 300px"></div>