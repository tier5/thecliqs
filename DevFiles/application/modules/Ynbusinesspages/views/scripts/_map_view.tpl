<?php if($this->datas != '[]'):?>
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
	var datas = <?php echo $this->datas?>;
	var contents = <?php echo $this->contents?>;
    var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
    var neighborhoods = [];
    var markers = [];
	var iterator = 0;
	var latlng;
    for(i=0 ; i< datas.length ; i++)
    {
    	neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
    }
    latlng = neighborhoods;
    
	function initialize() {
		var mapOptions = {
	    	zoom: 11,
	    	center: center
  	  	};
  	  	
	  	map = new google.maps.Map(document.getElementById('layout_ynbusinesspages_business_listing-map-canvas'),mapOptions);
      	for (var i = 0; i < neighborhoods.length; i++) {
      		addMarker(i);
  		}
      	 
      	var latlngbounds = new google.maps.LatLngBounds();
		for (var i = 0; i < latlng.length; i++) {
			latlngbounds.extend(latlng[i]);
		}
		map.fitBounds(latlngbounds);
	}
	function addMarker(i) {
  		marker = new google.maps.Marker({
	    	position: neighborhoods[iterator],
	    	map: map,
	    	draggable: false,
	    	animation: google.maps.Animation.DROP,
	    	icon: datas[i]['icon']
  		})  		
  		markers.push(marker);
  		iterator++;  		
  		infowindow = new google.maps.InfoWindow({});  		
  		google.maps.event.addListener(marker, 'mouseover', function() {    		
    		infowindow.close();
    		infowindow.setContent(contents[i])
    		infowindow.open(map,markers[i]);
  		});
	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
 
<div id="layout_ynbusinesspages_business_listing-map-canvas" style="height: 450px;"></div>
<?php else:?>
	<div class="tip">
        <span>
            <?php echo $this->translate("There are no businesses view on this map") ?>
        </span>
    </div>
<?php endif;?>