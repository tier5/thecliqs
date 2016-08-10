<?php if(count($this->listings) > 0):?>
	<ul class="generic_list_widget listing_browse listing_browse_view_content clearfix">
		<?php foreach( $this->listings as $listing ): ?>
			<li>
				<div class="list-view ynlisting-list-item">  
					<div class="listing_photo">
					    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
						<div class="listing_photo_main" style="background-image: url(<?php echo $photo_url; ?>);">

							<?php if ($listing->isNew()) : ?>
								<div class="newListing"></div>
							<?php endif; ?>

							<?php if ($listing->featured) : ?>
								<div class="featureListing"></div>
							<?php endif; ?>
							<?php if(!(($listing -> longitude == 0) && ($listing -> latitude == 0))): ?>
								<?php echo $this->htmlLink(array('route' => 'ynlistings_specific', 'action' => 'direction', 'id' => $listing->getIdentity()), $this->translate('Get Direction'), array('class' => 'smoothbox get_direction')); ?>
							<?php endif;?>
							<div class="listing_photo_hover">
								<div class="listing_view_more"> 
									<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
								</div>
							</div>
						</div>						
					</div>

					<div class="listing_info">
						<div class="listing_title">
							<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
						</div>						

						<div class="listing_price">
							<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
						</div>

						<div class="short_description">
							<?php echo strip_tags($listing->short_description)?>
						</div>

						<div class="listing_info_footer">
							<div class="author-avatar">
								<?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?>
							</div>	
							<div class="listing_info_footer_main">
								<div>
									<div class="listing_creation">							
										<span class=""><?php echo $this->translate('by ')?></span>
										<span><?php echo $listing->getOwner()?></span>
									</div>

									<div class="category">										
									<?php $category = $listing->getCategory()?>
									<?php if ($category) : ?>   
										<span class="fa fa-folder-open-o"></span>
										<span><?php echo ' '.$this->htmlLink($category->getHref(), $category->title)?></span>
									<?php endif; ?>
									</div>
								</div>

								<div>
									<div class="listing_rating">
										<span><?php 
											echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing));
											?>
										</span>
										<span class="review">
											<?php echo $listing->ratingCount().' '.$this->translate('review(s)')?>
										</span>
									</div>

									<div class="listing_location">
									<?php if ($listing->location): ?>										
										<span class="fa fa-map-marker"></span>
										<?php echo $listing->location;?>
									<?php endif; ?>									
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="grid-view">
					<div class="ynlisting-grid-item">
						<div class="ynlisting-grid-item-content">
						    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
                            <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">

								<?php if ($listing->featured) : ?>
									<div class="featureListing"></div>
								<?php endif; ?>

								<?php if ($listing->isNew()) : ?>
									<div class="newListing"></div>
								<?php endif; ?>

								<div class="ynlisting-item-rating">
									<?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing)); ?>
								</div>
							</div>
							<div class="item-front-info">
								<div class="listing_title">
									<?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
								</div>    

								<div class="listing_price">
									<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
								</div>
							</div>
						</div>
						<div class="ynlisting-grid-item-hover">
							<div class="ynlisting-grid-item-hover-background">
								<div class="listing_view_more"> 
									<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
								</div>

								<div class="short_description">
									<?php echo strip_tags($listing->short_description)?>
								</div>

								<div class="listing_creation">
									<span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
									<span><?php echo $this->translate('by ')?></span>
									<span class="author-title"><?php echo $listing->getOwner()?></span>
								</div>
							</div>
						</div>
					</div>            
				</div>				
			</li>
		<?php endforeach; ?>
	</ul>
	
	<ul class="generic_list_widget listing_browse listing_pin_view_content clearfix">
		<?php foreach( $this->listings as $listing ): ?>
			<li>				
				<div class="pin-view">
					<div class="highlight_listing">
						<div class="listing_title">
							<?php echo $this->htmlLink($listing->getHref(), $listing->title); ?>
						</div>

						<div class="listing_photo">
							<?php echo $this->htmlLink($listing->getHref(), $this->itemPhoto($listing, 'thumb.profile')); ?>

							<div class="prices">
								<?php echo $this -> locale()->toCurrency($listing->price, $listing->currency); ?>
							</div>

							<div class="listing_owner">
								<span><?php echo $this->translate('by').' '?></span>
								<span><?php echo $listing->getOwner()?></span>
							</div>

							<?php if ($listing->isNew()) : ?>
								<div class="newListing"></div>
							<?php endif; ?>

							<?php if ($listing->featured) : ?>
								<div class="featureListing"></div>
							<?php endif; ?>

							<div class="listing_photo_hover">
								<div class="listing_category">            
									<span class="fa fa-folder-open-o"></span>
									<?php 
									$category = $listing->getCategory();
									if ($category) {
										echo $this->htmlLink($category->getHref(), $category->title);
									}
									?>
								</div>  

								<div class="listing_view_more"> 
									<?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
								</div>
							</div>
						</div>

						<div class="listing_owner_avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></div>

						<div class="listing_rating">
							<?php 
							echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing));
							?>            
						</div>

						<?php if ($listing->location): ?>
							<div class="listing_location">
								<span class="fa fa-map-marker"></span>
								<?php echo $listing->location;?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate("There are no listings.") ?>
		</span>
	</div>
<?php endif;?>