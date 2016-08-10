<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate(' &#187; Videos');
			?>
		</h2>
	</div>
</div>

<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="search">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'ynlistings_general', 'action' => 'view', 'id' => $this->listing->getIdentity()), $this->translate('Back to Listing'), array(
				'class' => 'buttonlink icon_back'
			)) ?>
			<?php if( $this->canCreate ): ?>
				<?php echo $this->htmlLink(array(
					'route' => 'video_general',
					'action' => 'create',
					'type_parent' =>'ynlistings_listing',
					'id_subject' =>  $this->listing->getIdentity(),
				  ), $this->translate('Add New Video'), array(
					'class' => 'buttonlink icon_listings_add_videos'
				)) ?>	
			<?php endif; ?>
		</div>
<!-- Content -->
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynlistings_videos_manage videos_manage">
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="ynlistings_thumb_wrapper video_thumb_wrapper">
                    <?php if ($item->duration): ?>
                        <?php echo $this->partial('_video_duration.tpl','ynlistings', array('video' => $item)) ?>
                    <?php endif; ?>
                    <?php
	                    if ($item->photo_id) {
	                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
	                    } else {
	                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynlistings/externals/images/video.png">';
	                    }
                    ?>
                </div>
                <?php if($this->listing->user_id == $this->viewer->getIdentity() || $item->isOwner($this->viewer)) :?>
                <div class='video_options'>
                    <?php
                    $ynvideo_enable = Engine_Api::_() -> ynlistings() ->checkYouNetPlugin('ynvideo');
                    if($ynvideo_enable)
                    {
					    echo $this->htmlLink(array(
		                    'route' => 'video_general',
		                    'action' => 'edit',
		                    'video_id' => $item->video_id,
		                    'listing_id' => $item->parent_id,
		                    'type_parent' =>'ynlistings_listing',
							'id_subject' =>  $this->listing->getIdentity(),
		                    ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_listings_edit'));
					}
					else
					{
						echo $this->htmlLink(array(
		                    'route' => 'default',
				            'module' => 'video',
				            'controller' => 'index',
				            'action' => 'edit',
		                    'video_id' => $item->video_id,
		                    'listing_id' => $item->parent_id,
		                    'type_parent' =>'ynlistings_listing',
							'id_subject' =>  $this->listing->getIdentity(),
		                    ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_listings_edit'));
					}
				    ?>
                    <?php
                    if ($item->status != 2) {
                    	if($ynvideo_enable)
                    	{
	                        echo $this->htmlLink(array(
	                            'route' => 'video_general',
	                            'action' => 'delete',
	                            'video_id' => $item->video_id,
	                            'listing_id' => $item->parent_id,
	                            'type_parent' =>'ynlistings_listing',
								'id_subject' =>  $this->listing->getIdentity(),
	                            'case' => 'video',
	                            'format' => 'smoothbox'
	                            ), $this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_listings_delete'));
                         }
						else 
						{
							echo $this->htmlLink(array(
	                            'route' => 'default', 
		                     	'module' => 'video', 
		                     	'controller' => 'index', 
		                     	'action' => 'delete', 
	                            'video_id' => $item->video_id,
	                            'listing_id' => $item->parent_id,
	                            'type_parent' =>'ynlistings_listing',
								'id_subject' =>  $this->listing->getIdentity(),
	                            'case' => 'video',
	                            'format' => 'smoothbox'
	                            ), $this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_listings_delete'));
						}
					}
                    ?>
                     <?php
		  		?>
                </div>
                <?php endif;?>
                <div class="video_info video_info_in_list">
                    <div class="ynlistings_title">
                        <?php 
	                         $title = Engine_Api::_()->ynlistings()->subPhrase($item->getTitle(),70);
				             if($title == '') $title = "Untitle Video";
	                         echo $this->htmlLink($item->getHref(), htmlspecialchars($title));
                         ?>
                    </div>
                    <div class="video_desc ynlistings_block">
                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
                     <p class="thumbs_info">
			          <?php echo $this->translate('By');?>
			          <?php if($item->owner_id != 0 ){
			              $name = Engine_Api::_()->ynlistings()->subPhrase($item->getOwner()->getTitle(),18);
			              echo $this->htmlLink($item->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
			            }
			             else{
			              $name = Engine_Api::_()->ynlistings()->subPhrase($listing->getOwner()->getTitle(),18);
			              echo $this->htmlLink($listing->getOwner()->getHref(), $name, array('class' => 'thumbs_author'));
			             }
			          ?>
			          <br />
			          <?php echo $this->timestamp($item->creation_date) ?>
			          <?php echo $this->partial('_video_rating_big.tpl', array('video' => $item)) ?>
       				</p>
                    <?php if ($item->status == 0): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.') ?>
						</span>
					</div>
					<?php elseif ($item->status == 2): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.') ?>
						</span>
					</div>
					<?php elseif ($item->status == 3): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
						</span>
					</div>
					<?php elseif ($item->status == 4): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
						</span>
					</div>
					<?php elseif ($item->status == 5): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
						</span>
					</div>
					<?php elseif ($item->status == 7): ?>
					<div class="tip">
						<span>
							<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
						</span>
					</div>
					<?php endif; ?>
				</div>
			</li>
            <?php endforeach; ?>
		</ul>
		<br/>
		<div class="ynlistings_pages">
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>

	<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You do not have any videos.'); ?>
			</span>
		</div>
	<?php endif; ?> 		
		
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('title'))
	    {
	      new OverText($('title'), 
	      {
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