<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery.flexslider.js');
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/flexslider.css');
?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-content">
		<?php echo $this -> business -> short_description?>
	</div>
</div>

<?php if($this -> business -> theme == "theme3") :?>
	<?php if (count($this->covers)):?>	
		<div class="ynbusinesspages-profile-fields">	
			<div id="ynbusinesspages-profile-slider" class="flexslider">
				<ul class="slides">
				<?php foreach ($this->covers as $photo):?>
					<li>
						<span class="ynbusinesspages-slider-flex-thumb" style="background-image: url(<?php echo $photo->getPhotoUrl();?>);"></span>
					</li>
				<?php endforeach;?>
				</ul>
			</div>
		</div>
	<?php endif;?>
<?php endif;?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-folder-open"></i><?php echo $this->translate('Category');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul>
			<?php foreach($this -> categoryMaps  as $categoryMap) :?>
				<?php 
					$table = Engine_Api::_() -> getDbTable('categories', 'ynbusinesspages');
					$category = $table -> getNode($categoryMap -> category_id);
					$i = 0;
				?>
				<?php if($category) :?>
					<li>
						<?php foreach($category->getBreadCrumNode() as $node): ?>
							<?php if($node -> category_id != 1) :?>
							<?php if($i != 0) :?>
								&raquo;	
							<?php endif;?>
			        			<?php $i++; echo $this->translate($node->shortTitle()) ?>
			        		<?php endif; ?>
				     	 <?php endforeach; ?>
				     	 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
									&raquo;	
						 <?php endif;?>
				     	 <?php echo $category->getTitle(); ?>
			     	 </li>
		     	 <?php endif;?>
			<?php endforeach;?>
		</ul>
	</div>
</div>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-users"></i><?php echo $this->translate('Business Size');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<?php echo $this -> business -> size?>
	</div>
</div>

<?php if(count($this -> founders)):?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-rocket"></i> <?php echo $this->translate('Founders');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul class="ynbusinesspages-overview-founder">
			<?php foreach($this -> founders  as $founder) :?>
			<li>
				<?php if(!empty($founder -> user_id)) :?>
					<?php $user = Engine_Api::_() -> getItem('user', $founder -> user_id);?>
					<?php if($user -> getIdentity() > 0):?>
						<?php echo $this -> htmlLink($user -> getHref(), $user -> getTitle(), array('target' => '_blank'));?></span>
					<?php endif;?> 
				<?php else :?>
					<span><?php echo $founder -> name ?></span>
				<?php endif;?>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php endif;?>


<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-building"></i> <?php echo $this->translate('Locations');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<ul class="ynbusinesspages-overview-listmaps">
			<?php $locationIndex = 1; foreach($this -> locations  as $location) :?>
			<li>
				<div class="ynbusinesspages-overview-maps-title"><?php echo $location -> title ?></div>
				<div class="ynbusinesspages-overview-maps-location"><i class="fa fa-map-marker"></i> <?php echo $location -> location ?></div>
				<?php if ($location -> latitude != '0' && $location -> longitude != '0'):?>
					<?php
						echo $this -> partial('_location_map.tpl', 'ynbusinesspages', array(
							'item' => $location,
							'map_canvas_id' => 'map-canvas-'.$locationIndex,
						));
						$locationIndex++;
					?>
	            <?php endif;?>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>

<?php if($this -> package -> getIdentity() && $this -> package -> allow_owner_add_customfield && count($this -> businessInfos)) :?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-book"></i><?php echo $this->translate('Additional Information');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
	<ul>
		<?php foreach($this -> businessInfos  as $businessInfo) :?>
		<li>
			<span class="ynbusinesspages-overview-custom-header"><?php echo $businessInfo -> header ;?></span>
			<span><?php echo $businessInfo -> content ;?></span>
		</li>
		<?php endforeach;?>
	</ul>
	</div>
</div>
<?php endif;?>

<?php if($this -> business -> user_id != 0) :?>
<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> business); ?>
<?php if($this -> fieldValueLoop($this -> business, $fieldStructure)):?>
<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-building"></i> <?php echo $this->translate('Business Specifications');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
	   <?php echo $this -> fieldValueLoop($this -> business, $fieldStructure); ?>
	</div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="ynbusinesspages-profile-fields">
	<div class="ynbusinesspages-overview-title ynbusinesspages-overview-line">
		<span class="ynbusinesspages-overview-toggle-button"><i class="fa fa-chevron-down"></i></span>
		<span class="ynbusinesspages-overview-title-content"><i class="fa fa-align-justify"></i><?php echo $this->translate('Description');?></span>
	</div>
	<div class="ynbusinesspages-overview-content">
		<div class="ynbusinesspages-description rich_content_body">
			<?php echo $this -> business -> description?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$$('.ynbusinesspages-overview-toggle-button').addEvent('click', function(){
		this.toggleClass('ynbusinesspages-overview-content-closed');
		this.getParent('.ynbusinesspages-profile-fields').getElement('.ynbusinesspages-overview-content').toggle();
	});

	<?php if($this -> business -> theme == "theme3") :?>
		// Can also be used with $(document).ready()
		jQuery.noConflict();
		jQuery(window).load(function() {
			if ( jQuery('#ynbusinesspages-profile-slider').length > 0 ) {
				jQuery('#ynbusinesspages-profile-slider').flexslider({
				    animation: "slide",
				    controlNav: false, 
				    useCSS: false,
				    prevText: "",
					nextText: "", 
				});	
			}			
		});
	<?php endif;?>

</script>

<script>
	
	window.addEvent('domready', function(){
		$$('.tab_layout_ynbusinesspages_business_profile_overview').addEvent('click', function(){		

			<?php if($this -> business -> theme == "theme3") :?>
				jQuery.noConflict();
				if ( jQuery('#ynbusinesspages-profile-slider').length > 0 ) {
					jQuery('#ynbusinesspages-profile-slider').flexslider().resize();
				}
			<?php endif;?>			
			
			<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places"); ?>
			
			<?php $locationIndex = 1; foreach($this -> locations  as $location) :?>
				<?php if ($location -> latitude != '0' && $location -> longitude != '0'):?>
					var fromPoint;
					var endPoint = new  google.maps.LatLng(<?php echo $location->latitude;?>, <?php echo $location->longitude;?>);
					var directionsService = new google.maps.DirectionsService();
					var directionsDisplay;
					var map;
					var marker;
					
					  var center =  new google.maps.LatLng(<?php echo $location->latitude;?>, <?php echo $location->longitude;?>);
					  var mapOptions = {
					    center: center,
					    zoom: 13
					  };
					
					  directionsDisplay = new google.maps.DirectionsRenderer();
					  map = new google.maps.Map(document.getElementById('map-canvas-<?php echo $locationIndex;?>'), mapOptions);
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
					
				    <?php $locationIndex++;?>
				<?php endif;?>
		    <?php endforeach;?>
		});	
	});
</script>