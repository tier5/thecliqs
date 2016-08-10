<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Albums');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="album_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="ynlisting_listing_action">
			<?php echo $this->htmlLink(array('route' => 'ynlistings_general', 'action'=>'view', 'id' => $this->listing->getIdentity()), $this->translate('Back to Listing'), array(
			'class' => 'buttonlink icon_back'
			)) ?>
			<?php if( $this->canUpload ): ?>
			<?php echo $this->htmlLink(array(
				'route' => 'ynlistings_extended',
				'controller' => 'album',
				'action' => 'create',
				'subject' => $this->subject()->getGuid(),
			  ), $this->translate('Create Album'), array(
				'class' => 'buttonlink icon_listings_add_photos'
			)) ?>
			<?php endif; ?>
		</div>
	
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $listing = $this->listing;?>
		<div class="ynlistings_grid-view">
			<div class="ynlistings-tabs-content ynclearfix">
				<div class="tabcontent" style="display: block;">
					<ul class="generic_list_widget ynlistings_list_albums">
						<?php foreach( $this->paginator as $album ): ?>
						<li>
							<div class="grid-view">
								<div class="photo">
									<a class="thumbs" href="<?php echo $album->getHref(); ?>">
									<?php $photo = $album->getFirstCollectible();
									if($photo):?>
									<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.profile');?>)" class="image-thumb"></span>
									<?php else:?>
									<span style="background-image: url(./application/modules/Ynlistings/externals/images/nophoto_album_thumb_profile.png)" class="image-thumb"></span>
									<?php endif;?>
									</a>
								</div>
								<div class="info">
									<div class="title">
										<?php $title = Engine_Api::_()->ynlistings()->subPhrase($album->getTitle(),23);
										if($title == '') $title = "Untitle Album";
										echo $this->htmlLink($album->getAlbumHref(),"<b>".$title."</b>");?>
										<div class="time_active">
											<i class="ynicon-time" title="Time create"></i>
											<?php echo $this->timestamp($album->creation_date) ?>
										</div>
									</div>
									<div class="stats">
										<div>
											<?php echo $this->translate('By');?>
											<?php if($album->user_id != 0 ){
												$name = Engine_Api::_()->ynlistings()->subPhrase($album->getMemberOwner()->getTitle(),20);
												echo $this->htmlLink($album->getMemberOwner()->getHref(), $name , array('class' => 'thumbs_author'));
											}
											else{
												$name = Engine_Api::_()->ynlistings()->subPhrase($listing->getOwner()->getTitle(), 20);
												echo $this->htmlLink($listing->getOwner()->getHref(), $listing->getOwner()->getTitle(), array('class' => 'thumbs_author'));
											}
											?>
										</div>
									</div>
								</div>
							</div>
						</li>
					<?php endforeach;?>
					</ul>
				</div>
			</div>
		</div>
		<?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
			'pageAsQuery' => true,
			'query' => $this->formValues,
		)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No albums have been uploaded.');?>
				<?php if($this->canUpload):?>
				<?php echo $this->translate('Create a %1snew one%2s.',
					  '<a href="'.$this->url(array('controller'=>'album','action' => 'create','subject' =>$this->listing->getGuid()), 'ynlistings_extended').'">', '</a>');?>
				<?php endif;?>
			</span>
		</div>
		<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
	en4.core.runonce.add(function() {
		if($('search')) {
			new OverText($('search'),{
				poll: true,
				pollInterval: 500,
				positionOptions: {
					position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
					edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
					offset: {
						x: ( en4.orientation == 'rtl' ? -4 : 4 ),
						y: 2
					}
				}
			});
		}
	});
</script>
