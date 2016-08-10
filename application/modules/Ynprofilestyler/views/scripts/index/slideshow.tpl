<?php if (isset($this->slides) && $this->slides->count() > 0) : ?>
	<form name="ynps-slides">
		<div class="ynprofilestyler_slides_container">
			<div class="prev-slide controls"></div>
			<div class="next-slide controls"></div>
			<div class="ynprofilestyler_slides">
				<ul>			
					<?php foreach ($this->slides as $slide) : ?>
					<li>
						<div>
							<img src="<?php echo $this->baseUrl() . '/' . $slide->url?>" />
						</div>
		
						<div>
							<input type="checkbox" value="<?php echo $slide->slide_id?>" name="slideId" />
						</div> 
						
						<?php if ($slide->published) :?>
							<div class="published">
								<?php echo $this->translate('Published');?>
							</div> 
						<?php endif;?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="buttons">
				<ul>
					<li><a href="javascript:void(0)"
						onclick="ynps2.openWnd('<?php echo $this->url(array('module' => 'ynprofilestyler', 'action' => 'upload-slide', 'controller' => 'index'))?>')">
							<?php echo $this->translate('Upload Image')?>
					</a>
					</li>
					<li>
						<a href="#" onclick="ynps2.deleteSlides()">
							<?php echo $this->translate('Delete Selected')?>
						</a>
					</li>
					<li>
						<a href="#" onclick="ynps2.publishSlides(1)"><?php echo $this->translate('Publish Selected')?></a>
					</li>
					<li>
						<a href="#" onclick="ynps2.publishSlides(0)">
							<?php echo $this->translate('Unpublish Selected')?>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</form>
	
	<?php echo $this->form->render()?>

	<script language="javascript" type="text/javascript">
    	var visible = <?php echo $this->slides->count();?>;
    	<?php if ($this->slides->count() > 5) : ?>
    			visible = 5;
    		<?php endif;?>
	    
		$(".ynprofilestyler_slides").jCarouselLite({
	        btnNext: ".prev-slide",
	        btnPrev: ".next-slide",
	        visible : visible,
	        circular : true,
	        width : 182,
	        height : 148    	        
	    });
    </script>
<?php else : ?>
	<?php echo $this->translate('You do not have any slides')?>
	<div class="buttons">
		<ul>
			<li><a href="javascript:void(0)"
				onclick="ynps2.openWnd('<?php echo $this->url(array('module' => 'ynprofilestyler', 'action' => 'upload-slide', 'controller' => 'index'))?>')">
					<?php echo $this->translate('Upload Image')?>
			</a>
			</li>
		</ul>
	</div>
<?php endif;?>