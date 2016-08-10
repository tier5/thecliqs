<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
	<?php if(isset($this->label)):?>
		<label for="location" class="optional"><?php echo $this->translate($this->label);?></label>
	<?php endif;?>
	</div>
	<div id="location-element" class="form-element">
		<input type="text" name="location" id="location" value="<?php if($this->location) echo $this->location;?>">
		<a class='ynbusinesspages_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this,'location', 'location_address', 'lat', 'long');" >
			<img src="application/modules/Ynbusinesspages/externals/images/icon-search-advform.png">
		</a>			
	</div>
</div>

