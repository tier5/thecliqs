<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
	<?php if(isset($this->label)):?>
		<label for="location" class="optional"><?php echo $this->translate($this->label);?></label>
	<?php endif;?>
	</div>
	<div id="location-element" class="form-element">
		<input type="text" name="location" id="location" value="<?php if($this->location) echo $this->location;?>">
		<a class='ynresume_location_icon' href="javascript:void()" onclick="return getCurrentLocation(this);" >
			<img src="application/modules/Ynresume/externals/images/icon-search-advform.png">
		</a>			
	</div>
</div>

