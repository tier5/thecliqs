<?php
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery.flexslider.js');
	$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/flexslider.css');
?>

<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile):?>
	<div class="ynbusinesspages-featured-businesspages">
		<div class="ynbusinesspages-featured-list flexslider">
		<?php if ( count($this->businesses) ):?>
			<?php $i_count = 1; $total_count = $this->businesses_count; ?>
			<ul class="slides">
				<?php foreach ( $this->businesses as $business ) :?>
				<?php 
					if ($i_count %4 == 1) {
						if ( $total_count - $i_count < 4 ) {
							echo '<li><div class="slides-item slides-item-'.($total_count - $i_count + 1).'">'; 
						} else {
							echo '<li><div class="slides-item">'; 							
						}										
					}
				?>
				<div class="ynbusinesspages-featured-item">
					<div class="ynbusinesspages-featured-item-content">
						<?php echo Engine_Api::_()->ynbusinesspages()->getFeaturedSpan($business); ?>
						<div class="ynbusinesspages-featured-item-content-bottom">
							<?php if ($business -> getIdentity() > 0):?>
							<div class="ynbusinesspages-featured-item-content-right">
								<div class="ynbusinesspages-featured-item-rating">
									<?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false); ?>
								</div>
								<div class="ynbusinesspages-featured-item-review">
									<?php echo $this -> translate(array("%s review", "%s reviews", $business->review_count), $business->review_count) ?>
								</div>		
							</div>					
							<?php endif;?>

							<div class="ynbusinesspages-featured-item-content-left">
								<div class="ynbusinesspages-featured-item-title">
									<?php echo $this->htmlLink($business->getHref(), $business->name);?>
								</div>
					
								<?php if ($business -> getIdentity() > 0):?>
								<div class="ynbusinesspages-featured-item-info">
									<span class="ynbusinesspages-featured-item-location">
										<i class="fa fa-map-marker"></i> <?php echo $business -> getMainLocation();?>
									</span>
								
									<?php $category = $business -> getMainCategory()?>
									<?php if ($category):?>
										<span class="ynbusinesspages-featured-item-categories">
											<i class="fa fa-folder-open-o"></i> <?php echo $this -> translate($category -> title); ?>	
										</span>
									<?php endif;?>
								</div>
								<?php endif;?>
							</div>
						</div>				
					</div>
				</div>			
				<?php $i_count++; ?>			
				<?php if ($i_count %4 == 1) echo '</div></li>'; ?>
			<?php endforeach;?>

			<?php if ($i_count %4 != 1) echo '</div></li>'; ?>
			</ul>
		<?php endif;?>
		</div>
	</div>
	<script type="text/javascript">
		// Can also be used with $(document).ready()
		jQuery.noConflict();
		jQuery(window).load(function() {
		  jQuery('.ynbusinesspages-featured-list').flexslider({
		    animation: "slide",
		    controlNav: true,
		    prevText: "",
			nextText: "",  
		  });
		});
	</script>
<?php else: ?>
	<div class="ynbusinesspages-featured-businesspages">
		<div class="ynbusinesspages-featured-list flexslider">
		<?php if ( count($this->businesses) ):?>
			<ul class="slides">
				<?php foreach ( $this->businesses as $business ) :?>
				<li><div class="slides-item slides-item-1">
					<div class="ynbusinesspages-featured-item">
						<div class="ynbusinesspages-featured-item-content">
							<?php echo Engine_Api::_()->ynbusinesspages()->getFeaturedSpan($business); ?>
							<div class="ynbusinesspages-featured-item-content-bottom">
								<?php if ($business -> getIdentity() > 0):?>
								<div class="ynbusinesspages-featured-item-content-right">
									<div class="ynbusinesspages-featured-item-rating">
										<?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false); ?>
									</div>
									<div class="ynbusinesspages-featured-item-review">
										<?php echo $this -> translate(array("%s review", "%s reviews", $business->review_count), $business->review_count) ?>
									</div>		
								</div>					
								<?php endif;?>

								<div class="ynbusinesspages-featured-item-content-left">
									<div class="ynbusinesspages-featured-item-title">
										<?php echo $this->htmlLink($business->getHref(), $business->name);?>
									</div>
						
									<?php if ($business -> getIdentity() > 0):?>
									<div class="ynbusinesspages-featured-item-info">
										<span class="ynbusinesspages-featured-item-location">
											<i class="fa fa-map-marker"></i> <?php echo $business -> getMainLocation();?>
										</span>
									
										<?php $category = $business -> getMainCategory()?>
										<?php if ($category):?>
											<span class="ynbusinesspages-featured-item-categories">
												<i class="fa fa-folder-open-o"></i> <?php echo $this -> translate($category -> title); ?>	
											</span>
										<?php endif;?>
									</div>
									<?php endif;?>
								</div>
							</div>				
						</div>
					</div>
				</div></li>
			<?php endforeach;?>
			</ul>
		<?php endif;?>
		</div>
	</div>
	<script type="text/javascript">
		// Can also be used with $(document).ready()
		jQuery.noConflict();
		jQuery(window).load(function() {
		  jQuery('.ynbusinesspages-featured-list').flexslider({
		    animation: "slide",
		    controlNav: true,
		    prevText: "",
			nextText: "",  
		  });
		});
	</script>
<?php endif; ?>