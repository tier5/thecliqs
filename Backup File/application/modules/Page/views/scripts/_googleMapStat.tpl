<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _googleMapStat.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>
<?php
$prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
?>
<?php $this->headScript()->appendFile($prefix.'www.google.com/jsapi'); ?>

<script type="text/javascript">
  google.load('visualization', '1', {'packages': ['geomap']});
  google.setOnLoadCallback(drawMap);
  function drawMap() {
    var data = new google.visualization.DataTable();
    data.addRows(<?php echo (int)$this->map_items->getTotalItemCount(); ?>);

    data.addColumn('string', 'Country');
    data.addColumn('number', 'Popularity');

    <?php
    if ($this->map_items) {
      foreach ($this->map_items as $key => $item) {
        echo "data.setValue({$key}, 0, '{$item['country']}');";
        echo "data.setValue({$key}, 1, {$item['count']});";
      }
    }
    ?>
    var options = {};
    options['dataMode'] = 'regions';
    options['width'] = 740;
    options['height'] = 462;

    var container = document.getElementById('map_canvas');
    var geomap = new google.visualization.GeoMap(container);
    console.log(<?php echo (int)$this->map_items->getTotalItemCount(); ?>);
    geomap.draw(data, options);
  };
</script>

<div class="map_canvas_stat"><div id='map_canvas'></div></div>
<div class="clr"></div>