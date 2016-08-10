<!-- Base MasterSlider style sheet -->
<link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider.css" />

<!-- Master Slider Skin -->
<link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

<!-- jQuery -->
<script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery-1.10.2.min.js"></script>
<script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.easing.min.js"></script>
 
<!-- Master Slider -->
<script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/masterslider.min.js"></script>
<div class="ynlisting-featured-slider-master">
    <div class="master-slider ms-skin-default round-skin" id="featured-masterslider"> 
    <?php foreach($this->listings as $listing) :?>
		<?php
		      
			$listing_photo = "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";
			if ( $listing->getPhotoUrl('thumb.main') ) {
				$listing_photo = $listing->getPhotoUrl('thumb.main');
			}
		?>
		<div class="ms-slide">
            <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $listing_photo; ?>" alt="<?php echo $listing->title;?>"/> 
            <img class="ms-thumb" src="<?php echo $listing_photo; ?>" alt="thumb" />
            <div class="ms-info"><a href="<?php echo $listing->getHref(); ?>"><?php echo $listing->title; ?></a></div>
        </div>
	<?php endforeach; ?>        
    </div>
</div>
<!-- end of template -->

<script type="text/javascript">      
    jQuery.noConflict();

    var slider = new MasterSlider();
    slider.setup('featured-masterslider' , {
        width: 898,
        height: 360,
        loop: true,
        autoplay: true,
        speed: 10,
        view: 'basic'        
    });
    slider.control('arrows');  
    slider.control('thumblist' , {autohide:false ,dir:'h'});     
</script>