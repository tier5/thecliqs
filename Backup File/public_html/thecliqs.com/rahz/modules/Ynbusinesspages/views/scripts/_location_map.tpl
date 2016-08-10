<?php if (!$this->item->longitude || !$this->item->latitude) : ?>
<div class="tip" style="margin: 10px">
    <span><?php echo $this->translate('Can not found the location.')?></span>
</div>
<?php else: ?>
<?php 
$this->headTranslate(array(
		"Missing location from",
		"Missing location to",
		"Browser doesn't support Geolocation",
		"Current location..."
));
?>
<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
<style>
      .controls {
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        padding: 0 11px 0 13px;
        width: 320px;
        font-size: 15px;
        font-weight: 300;
        text-overflow: ellipsis;
      }

      #pac-input:focus {
        border-color: #4d90fe;
        margin-left: -1px;
        padding-left: 14px;  /* Regular padding-left + 1. */
        width: 321px;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
}
</style>
<div id="ynbusinesspages_direction" class="ynbusinesspage-location-maps">
  <div id="<?php echo $this -> map_canvas_id;?>" style="height: 200px;"></div>
  <?php echo $this->htmlLink(
        array('route' => 'ynbusinesspages_general', 'action' => 'direction', 'id' => $this->item->getIdentity()), 
        $this->translate('<i class="fa fa-location-arrow"></i> Get Direction'),
        array('class' => 'smoothbox')) ?>
</div>
<script>
var fromPoint;
var endPoint = new  google.maps.LatLng(<?php echo $this->item->latitude;?>, <?php echo $this->item->longitude;?>);
var directionsService = new google.maps.DirectionsService();
var directionsDisplay;
var map;
var marker;

function initialize() {
  var center =  new google.maps.LatLng(<?php echo $this->item->latitude;?>, <?php echo $this->item->longitude;?>);
  var mapOptions = {
    center: center,
    zoom: 13
  };

  directionsDisplay = new google.maps.DirectionsRenderer();
  map = new google.maps.Map(document.getElementById('<?php echo $this -> map_canvas_id;?>'), mapOptions);
  directionsDisplay.setMap(map);
  

  var infowindow = new google.maps.InfoWindow();

  marker = new google.maps.Marker({
  	map:map,
  	draggable:true,
  	animation: google.maps.Animation.DROP,
  	position: center
  });
	
  google.maps.event.addListener(marker, 'dragend', toggleBounce);

  function toggleBounce() {
		if (marker.getAnimation() != null) 
		{
  			marker.setAnimation(null);
		} 
		else 
		{
  			marker.setAnimation(google.maps.Animation.BOUNCE);
		}
		var point = marker.getPosition();
		fromPoint = new google.maps.LatLng(point.lat(), point.lng());
  }
}


google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif; ?>

