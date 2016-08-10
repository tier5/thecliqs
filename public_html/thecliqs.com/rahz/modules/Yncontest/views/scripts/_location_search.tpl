<div id="location-wrapper" class="form-wrapper">
	<div id="location-label" class="form-label">
		<?php echo $this -> translate($this->label)?>
	</div>
	<div id="location-element" class="form-element" style="width: 100%">
		<input style="width: 85%" type="text" name="location" id="location" value="<?php echo $this -> location_address?$this -> location_address:''?>">
		<a class='yncontest_location_icon' style="display: inline-block;vertical-align: middle;" href="javascript:void()" onclick="return getCurrentLocation(this);" >
			<img src="application/modules/Ynfundraising/externals/images/icon-search.png">
		</a>			
	</div>
</div>

