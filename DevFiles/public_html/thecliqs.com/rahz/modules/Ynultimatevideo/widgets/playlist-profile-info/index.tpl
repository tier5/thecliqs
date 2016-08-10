<?php 
	$playlist = $this -> playlist;
	$id = $playlist -> getIdentity();
?>


<div class="ynultimatevideo_playlist_profile_info">
	
	<div class="ynultimatevideo_detail_title">
		<?php echo $playlist->getTitle() ?>
	</div>


	<div class="ynultimatevideo_detail_block_info">
		<div class="ynultimatevideo_detail_categories_ratings_owner clearfix">

		 	<div class="ynultimatevideo_detail_owner">
                <?php 
                    $poster = $this->playlist->getOwner();
                    echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array('class' => 'ynultimatevideo_img_owner clearfix')) 
                ?>
			 	<div class="ynultimatevideo_detail_categories">
					<span>
						<?php echo $this->translate('Category')?>:
						<?php echo $playlist->getCategory() ?>
					</span>
			 	</div>
                <div class="ynultimatevideo_detail_owner_info">
                    <?php echo $this->translate('Posted by') ?>

                    <?php
                        $poster = $this->playlist->getOwner();
                        if ($poster) {
                            echo $this->htmlLink($poster, $poster->getTitle());
                        }
                    ?>
                </div>
              	<div class="ynultimatevideo_detail_owner_info">
                    <?php echo $this->timestamp($this->playlist->creation_date) ?>
                </div>
		 	</div>
		</div>
		
		<div class="ynultimatevideo_detail_button_count clearfix">
			<div class="ynultimatevideo_detail_count">


				<div class="ynultimatevideo_detail_count_items">
					<div class="ynultimatevideo_detail_count_item">
						<?php
							$viewCount = $playlist->view_count;
							echo '<span>'.$this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$viewCount)).'</span>',
							$this->translate(array(' view', ' views', $viewCount));
						?>
					</div>					
					<div class="ynultimatevideo_detail_count_item">
						<?php
							$likeCount = $playlist->like_count;
							echo '<span>'.$this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$likeCount)).'</span>',
							$this->translate(array(' like', ' likes', $likeCount));
						?>
					</div>					
					<div class="ynultimatevideo_detail_count_item">
						<?php
							$commentCount = $playlist->comment_count;
							echo '<span>'.$this->partial('_number.tpl', 'ynultimatevideo', array('number'=>$commentCount)).'</span>',
							$this->translate(array(' comment', ' comments', $commentCount));
						?>
					</div>
				</div>
			</div>
		</div>

	</div>

    <div class="ynultimatevideo_detail_block_info_2">
        <div class="ynultimatevideo_detail_block_info_2_header clearfix">
            <div class="ynultimatevideo_addthis">
                <div class="addthis_sharing_toolbox"></div>
            </div>

			<div class="ynultimatevideo_detail_block_button">
			<?php if (Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
				<?php echo $this->htmlLink(array(
					'module'=>'activity',
					'controller'=>'index',
					'action'=>'share',
					'route'=>'default',
					'type'=>'ynultimatevideo_playlist',
					'id' => $playlist->getIdentity(),
					'format' => 'smoothbox'
				), '<i class="fa fa-share-alt"></i>'.$this->translate("Share"), array('class' => 'ynultimatevideo_share_button smoothbox')); ?>

				<?php $isLiked = $playlist->likes()->isLike($this->viewer()) ? 1 : 0; ?>
				<a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="onlike('<?php echo $playlist->getType() ?>', '<?php echo $playlist->getIdentity() ?>', <?php echo $isLiked ?>);">
					<?php if( $isLiked ): ?>
					<?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Liked");?>
					<?php else: ?>
					<?php echo '<i class="fa fa-thumbs-up"></i>'.$this -> translate("Like");?>
					<?php endif; ?>
				</a>
			<?php endif; ?>
				<?php if ($playlist->isEditable() || $playlist->isDeletable()) :?>
				<div class="ynultimatevideo_options_block">
					<span class="ynultimatevideo_options_btn"><i class="fa fa-pencil"></i></span>

					<div class="ynultimatevideo_options" style="display:none">
						<?php if ($playlist->isEditable()) :?>
						<?php $url = $this->url(array('action' => 'edit', 'playlist_id' => $playlist -> getIdentity()), 'ynultimatevideo_playlist', true);?>
						<a class="icon_ynultimatevideo_edit" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Edit')?></a>
						<?php endif;?>

						<?php if ($playlist->isDeletable()) :?>
						<?php $url = $this->url(array('action' => 'delete', 'playlist_id' => $playlist -> getIdentity()), 'ynultimatevideo_playlist', true);?>
						<a class="smoothbox icon_ynultimatevideo_delete" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>"><i class="fa fa-trash"></i><?php echo $this->translate('Delete')?></a>
						<?php endif;?>
					</div>
				</div>
				<?php endif;?>
			</div>
        </div>

		<?php if ($playlist->description): ?>
		<div class="ynultimatevideo_detail_block_info_2_content">
			<!--description-->
			<div class="ynultimatevideo_playlist_desc">
				<p><?php echo $playlist->description ?></p>
			</div>
		</div>
		<?php endif; ?>
    </div>
</div>

<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynultimatevideo.addthis.pubid', 'younet');?>" async="async"></script>
<script type="text/javascript">
    $$('.ynultimatevideo_options_btn').addEvent('click',function(){
        this.getParent('.ynultimatevideo_options_block').getElement('.ynultimatevideo_options').toggle();
    });

	function onlike(itemType, itemId, isLiked) {
		if (isLiked) {
			unlike(itemType, itemId);
		} else {
			like(itemType, itemId);
		}
	}

	function like(itemType, itemId)
	{
		new Request.JSON({
			url: en4.core.baseUrl + 'core/comment/like',
			method: 'post',
			data : {
				format: 'json',
				type : itemType,
				id : itemId,
				comment_id : 0
			},
			onSuccess: function(responseJSON, responseText) {
				if (responseJSON.status == true)
				{
					var html = '<a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="unlike(\'<?php echo $playlist->getType()?>\', \'<?php echo $playlist->getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Liked'); ?></a>';
					$("ynultimatevideo_like_button").outerHTML = html;
				}
			},
			onComplete: function(responseJSON, responseText) {
			}
		}).send();
	}

	function unlike(itemType, itemId)
	{
		new Request.JSON({
			url: en4.core.baseUrl + 'core/comment/unlike',
			method: 'post',
			data : {
				format: 'json',
				type : itemType,
				id : itemId,
				comment_id : 0
			},
			onSuccess: function(responseJSON, responseText) {
				if (responseJSON.status == true)
				{
					var html = '<a id="ynultimatevideo_like_button" class="ynultimatevideo_like_button" href="javascript:void(0);" onclick="like(\'<?php echo $playlist->getType()?>\', \'<?php echo $playlist->getIdentity() ?>\')"><i class="fa fa-thumbs-up"></i><?php echo $this -> translate('Like'); ?></a>';
					$("ynultimatevideo_like_button").outerHTML = html;
				}
			}
		}).send();
	}
</script>