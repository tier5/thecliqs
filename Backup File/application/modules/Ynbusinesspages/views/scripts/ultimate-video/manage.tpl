<!-- Header -->
<div class="generic_layout_container layout_top">
  <div class="generic_layout_container layout_middle">
    <div class="headline">	
		<h2>
			<?php echo $this->business->__toString()." " ;
				echo $this->translate('&#187; Ultimate Videos');
			?>
		</h2>
	</div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="search">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<div class="generic_layout_container">
		<div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
					'class' => 'buttonlink'
				)) ?>
				
				<?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller'=>'ultimate-video','action'=>'list','subject' => $this->subject()->getGuid()), '<i class="fa fa-list"></i>'.$this->translate('Browse Videos'), array(
					'class' => 'buttonlink active'
				)) ?>

				<?php if( $this->canCreate ): ?>
					<?php echo $this->htmlLink(array(
							'route' => 'ynultimatevideo_general',
							'action' => 'create',
							'parent_type' =>'ynbusinesspages_business',
							'subject_id' =>  $this->business->business_id,
						), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Video'), array(
						'class' => 'buttonlink'
						)) ;
					?>
				<?php endif; ?>
            </div>      
        </div> 
		
		<!-- Content -->
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynvideo_videos_manage videos_manage">
        <h3>
            <?php
            $totalVideo = $this->paginator->getTotalItemCount();
            echo $this->translate(array('video_count', '%s videos', $totalVideo), $this->locale()->toNumber($totalVideo));
            ?>
        </h3>
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="ynvideo_thumb_wrapper video_thumb_wrapper">
                    <?php if ($item->duration): ?>
                        <?php echo $this->partial('_video_duration.tpl','ynbusinesspages', array('video' => $item)) ?>
                    <?php endif; ?>
                    <?php
                    if ($item->photo_id) {
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                    } else {
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/video.png">';
                    }
                    ?>
                </div> 
                <div class="ynbusinesspages-profile-module-option">
                	<?php
                	$canDeleteToBusiness = $this->business -> isAllowed('video_delete', null, $item);
					$canDelete = $item -> authorization() -> isAllowed(null, 'delete');
					$canEdit = $item -> authorization() -> isAllowed(null, 'edit');
                	if($canEdit)
					{
						echo $this->htmlLink(array(
							'route' => 'ynultimatevideo_general',
							'action' => 'edit',
							'video_id' => $item->video_id,
							'business_id' => $this->business->business_id,
							'parent_type' => 'ynbusinesspages_business',
							), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Video'), array('class' => 'buttonlink'));
					}
					?>
					
                    <?php
                    if($canDelete)
					{
	                    if ($item->status != 2) {
							echo $this->htmlLink(array(
								'route' => 'ynultimatevideo_general',
								'action' => 'delete',
								'video_id' => $item->video_id,
								'business_id' => $this->business->business_id,
								'parent_type' => 'ynbusinesspages_business',
								'case' => 'ynultimatevideo_video',
								'format' => 'smoothbox'
								), '<i class="fa fa-trash-o"></i>'.$this->translate('Delete Video'), array('class' => 'buttonlink smoothbox'));
						}
					}
					if($canDeleteToBusiness)
					{
						echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_specific',
                            'action' => 'remove-item',
                            'item_id' => $item->getIdentity(),
                            'item_type' => 'ynultimatevideo_video',
                            'item_label' => $this -> translate('video'),
                            'remove_action' => 'video_delete',
                            'business_id' => $this -> business->getIdentity(),
                        ),
                        '<i class="fa fa-times"></i>'.$this->translate('Delete Video to Business'),
                        array('class'=>'buttonlink smoothbox'));
					}
		  		?>
                </div>               
                <div class="video_info video_info_in_list">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($item->getHref(), htmlspecialchars($item->getTitle())) ?>
                         <?php if($row->highlight) :?>
                       			<strong style="color: red;"><?php echo " - " . $this->translate("highlighted"); ?></strong> 
                        <?php endif;?>
                    </div>
                    <div class="video_stats">
                        <?php echo $this->partial('_video_views_stat.tpl','ynbusinesspages', array('video' => $item)) ?>
                        <div class="ynvideo_block">
                            <?php echo $this->partial('_video_rating_big.tpl','ynbusinesspages', array('video' => $item)) ?>
                        </div>
                    </div>
                    <div class="video_desc ynvideo_block">
                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
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

		<div class="ynvideo_pages">
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