<?php if($this->contest->location):?>
	<h4>
		<span><i class="fa fa-building" aria-hidden="true"></i><?php echo $this->translate("Location")?></span>
			<span rel="group_desc" id="desc_more_icon_id" onmousedown="toggleDescloca('yncontest_hide','desc_more_icon_id'); return false;">
				<i class="fa fa-chevron-circle-down" id="yncontest_toggle-loca" aria-hidden="true"></i>
			</span>
	</h4>
	<div id="yncontest_hide">
		<strong><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo $this->contest->location;?></strong>
		<?php if (!$this->contest->longitude || !$this->contest->latitude) : ?>
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
			<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
			<div id="yncontest_direction">
				<div id="map-canvas" style="height: 335px;"></div>
				<br/>
				<div class="yncontest_detail-get-descretion">
					<i class="fa fa-location-arrow" aria-hidden="true"></i>
						<span>
							<?php echo $this->htmlLink(
							array('route' => 'yncontest_general', 'action' => 'direction', 'id' => $this->contest->getIdentity()),
								$this->translate('Get Direction'),
								array('class' => 'smoothbox')) ?>
						</span>
				</div>
			</div>
			<script type="text/javascript">
				function initialize() {
					var center =  new google.maps.LatLng(<?php echo $this->contest->latitude;?>, <?php echo $this->contest->longitude;?>);
				var mapOptions = {
					center: center,
					zoom: 13
				};

				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				marker = new google.maps.Marker({
					map:map,
					draggable: false,
					animation: google.maps.Animation.DROP,
					position: center
				});
				}
				google.maps.event.addDomListener(window, 'load', initialize);
				en4.core.runonce.add(function()
				{
					$$('li.tab_<?php echo $this -> identity?> a').addEvent('click', function(){
						initialize();
					});
				});

				function toggleDescloca(block_id,img_id){

					if(document.getElementById(block_id).style.display == 'none'){
						document.getElementById(block_id).style.display = 'block';
						document.getElementById('yncontest_toggle-loca').className="fa fa-chevron-circle-down";
					}else{
						document.getElementById(block_id).style.display = 'none';
						document.getElementById('yncontest_toggle-loca').className="fa fa-chevron-circle-up";
					}
				}
			</script>
		<?php endif;?>
	</div>
<?php endif;?>
<div class="yncontest_detail-detaildesc">
	<?php echo $this->contest->description?>
</div>