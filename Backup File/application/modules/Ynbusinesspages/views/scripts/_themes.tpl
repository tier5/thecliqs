<div class="form-wrapper form-ynbusinesspages-choose-theme">
	<div class="form-label">
		<?php echo $this->translate('Select Themes')?>
	</div>
	<div class="form-element">
		<div class="item-form-theme-choose">
			<input id='category_theme1' type='checkbox' name='themes[]' value ='theme1'>
			<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynbusinesspages/externals/images/theme1.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='category_theme2' type='checkbox' name='themes[]' value ='theme2'>
			<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynbusinesspages/externals/images/theme2.png" />
		</div>
		<div class="item-form-theme-choose">
			<input id='category_theme3' type='checkbox' name='themes[]' value ='theme3'>
			<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynbusinesspages/externals/images/theme3.png" />
		</div>
	</div>
</div>
<script type='text/javascript'>
	<?php if($this->package):?>
		<?php foreach($this->package->themes as $item) :?>
		    var id = 'category_' + '<?php echo $item;?>';
			$(id).setProperty('checked', 'true');
		<?php endforeach ;?>
	<?php endif;?>
</script>
