<div id="location-wrapper" class="form-wrapper">
	<div class="ynbusinesspages-clearfix ynbusinesspages-location">
		<div id="location-label" class="form-label">
			<label for="location_name">
				<?php echo $this->translate('Location Title');?>
			</label>
		</div>
		<div id="location-element" class="form-element">
			<input class="ynbusinesspages-location-title" style="width : 300px" type="text" name="location_title" id="location_title">
		</div>
	</div>
	<div class="ynbusinesspages-clearfix ynbusinesspages-location" style="display: inline-block">
		<div id="location-label" class="form-label">
			<label for="location_name">
				<?php echo $this->translate('Full Address');?>
			</label>
		</div>
		<div id="location-element" class="form-element">
			<input class="ynbusinesspages-location-maps" style="width : 400px; display: inline-block; vertical-align: middle;" type="text" name="location" id="location" value="<?php if($this->location) echo $this->location;?>">
			<a style="display: inline-block; vertical-align: middle;" class='ynbusinesspages_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this,'location', 'location_address', 'lat', 'long');" >
				<img src="application/modules/Ynbusinesspages/externals/images/icon-search-advform.png">
			</a>			
		</div>
	</div>
</div>

