<div id="location_alert-wrapper" class="form-wrapper">
	<div id="location_alert-label" class="form-label">
	<?php if(isset($this->label)):?>
		<label for="location_alert" class="optional"><?php echo $this->translate($this->label);?></label>
	<?php endif;?>
	<?php if(empty($this -> alert)) :?>
		<label for="location_alert_name" class="optional">
			<?php echo $this->translate('Full Address');?>
		</label>
	<?php endif;?>
	</div>
	<div id="location_alert-element" class="form-element">
		<input style="width: 400px;" type="text" name="location_alert" id="location_alert" value="<?php if($this->location) echo $this->location;?>">
		<a class='ynjobposting_location_icon' href="javascript:void()" onclick="return getAlertCurrentLocation(this);" >
			<img src="application/modules/Ynjobposting/externals/images/icon-search-advform.png">
		</a>			
	</div>
</div>

