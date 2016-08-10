<div class="form-wrapper form-ynbusinesspages-choose-theme">
	<div class="form-label">
		<?php echo $this->translate('Select Themes')?>
	</div>
	<div class="form-element">
		<?php if(count($this->package->themes) > 0):?>
			<?php foreach($this->package->themes as $item) :?>
				<div class="item-form-theme-choose">
					<input <?php if($this->theme == $item) echo "checked='true'"?>  id='package_<?php echo $item?>' type='radio'  name='theme' value ='<?php echo $item?>'>
					<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynbusinesspages/externals/images/<?php echo $item?>.png" >
				</div>
			<?php endforeach ;?>
		<?php endif;?>	
	</div>
</div>
