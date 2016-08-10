<div class="slider-full" id="ynsliderfull">
	<div class="slide-height-content" style="height: <?php echo $this->height;?>px"></div>
	<div class="slide-wide-content" style="height: <?php echo $this->height;?>px; margin-top: -<?php echo $this->height;?>px">
		<?php echo $this -> html_content;?>
	</div>
</div>
<style>
    
	#ynsliderfull .slide-wide-content {
		position: absolute; 
		left: 0; 
		width: 100%; 
	}
	
    #ynsliderfull ul.pxs_slider li .pxs_slider_content {
        max-width: 90%;
    }
    
	#ynsliderfull ul.pxs_slider li img {
		max-height: <?php echo round($this->image_height*$this->height)/100 ;?>px;
        max-width: 100%;
	}

	@media screen and (max-width: 680px) {
		#ynsliderfull .pxs_thumbnails {
			display: none;
		}
	}	
</style>