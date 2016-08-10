<link href="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/styles/flexslider.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/scripts/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Ynjobposting/externals/scripts/jquery.flexslider.js"></script>

<div class="ynjobposting-sponsored-companies flexslider ynjobposting-clearfix">
    <ul class="slides">
	<?php foreach ($this->companies as $company):?>
		<li class="ynjobposting-sponsored-companies-item">
			<?php echo Engine_Api::_()->ynjobposting()->getPhotoSpan($company); ?>
			<div class="ynjobposting-sponsored-companies-item-title">
				<?php echo $this->htmlLink($company->getHref(), $company->name);?>
			</div>
		</li>
	<?php endforeach;?>
	</ul>
</div>

<script type="text/javascript">
    jQuery.noConflict(); 
	jQuery(window).load(function() {
		var flex_width = jQuery('.ynjobposting-sponsored-companies').width();

		flex_width = Math.floor(flex_width / (Math.floor(flex_width/200)));
		
		jQuery('.ynjobposting-sponsored-companies').flexslider({
			animation: "slide",
			animationLoop: false,
			itemWidth: flex_width,
			controlNav: false, 
		});
	});
</script>