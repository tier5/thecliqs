<?php $item = $this -> item;?>
<?php if (!$item->isViewable()) :?>
<div class="disabled"></div>
<?php endif;?>
<?php $photo_url = ($item->getPhotoUrl('thumb.profile')) ? $item->getPhotoUrl('thumb.profile') : "application/modules/Ynmusic/externals/images/nophoto_song_thumb_profile.png";?>
<!-- MODE GRID VIEW -->
<div class="song-grid-view">
	<div class="song-photo music-photo">
		<a href="<?php echo $item->getHref(); ?>" style="background-image: url(<?php echo $photo_url; ?>)"></a>

		<?php if ($this->history) :?>
		<div class="remove-history">
			<input type="checkbox" class="remove-history-checkbox" value="<?php echo $this->history?>"/>
			<?php echo $this->htmlLink(array('action'=>'remove','id'=>$this->history,'route'=>'ynmusic_history'), $this->translate('Remove'), array('class'=>'smoothbox'));?>
		</div>
		<?php endif; ?>
		
		<?php if ($this->viewer()->getIdentity()):?>
		<div class="show-hide-action">
			<a class="action-link show-hide-btn" href="javascript:void(0)"><i class="fa fa-plus"></i></a>

			<div class="action-pop-up" style="display: none">
				<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist()) :?>
					<div class='song-action-add-playlist dropdow-action-add-playlist'>
					<span><?php echo $this-> translate('add to') ?></span>
					<?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$item->getGuid()),'ynmusic_playlist', true)?>
					<div rel="<?php echo $url;?>" class="music-loading add-to-playlist-loading" style="display: none;text-align: center">
						<span class="ajax-loading">
					    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
						</span>
					</div>
					<div class="add-to-playlist-notices"></div>
					<div class="box-checkbox">
						<?php echo $this->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $item)); ?>
					</div>
			    </div>
			    <?php endif;?>
				<div class="song-action-dropdown music-action-dropdown">
					<?php if (Engine_Api::_()->ynmusic()->canCreatePlaylist()) :?>
					<a href="javascript:void(0);" onclick="addNewPlaylist(this, '<?php echo $item->getGuid()?>');" class="action-link add-to-playlist" data="<?php echo $item->getGuid()?>"><i class="fa fa-plus"></i><span class="label"><?php echo $this->translate('Add to new playlist')?></span></a>
					<span class="play_list_span"></span>
					<?php endif;?>
					
					<?php if ($item->isDownloadable()) :?>
					<a class="action-link download" download="<?php echo $item->getTitle().'.mp3'?>" href="<?php echo $item->getFilePath()?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-download"></i><span class="label"><?php echo $this->translate('Download')?></span></a>
					<?php endif;?>
					
					<?php if ($item->isCommentable()) :?>
					<?php if( $item->likes()->isLike($this->viewer()) ): ?>
					<a class="action-link like liked" href="javascript:void(0);" onclick="ynmusicUnlike(this, '<?php echo $item->getType()?>', '<?php echo $item->getIdentity() ?>')" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-thumbs-up"></i><span class="label"><?php echo $this->translate('Unlike')?></span></a>
					<?php else: ?>
					<a class="action-link like" href="javascript:void(0);" onclick="ynmusicLike(this, '<?php echo $item->getType()?>', '<?php echo $item->getIdentity() ?>')" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-thumbs-up"></i><span class="label"><?php echo $this->translate('Like')?></span></a>
					<?php endif;?>
					<?php endif;?>
					
					<?php if ($this->viewer()->getIdentity()):?>
					<?php $url = $this -> url(array(
				        'module' => 'activity',
				        'controller' => 'index',
				        'action' => 'share',
				        'type' => 'ynmusic_song',
				        'id' => $item->getIdentity(),
				        'format' => 'smoothbox'),'default', true) ?>
					<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-share-alt"></i><span class="label"><?php echo $this->translate('Share')?></span></a>
					<?php endif;?>
					
					<?php if ($item->isEditable()) :?>
					<?php if (!empty($this->business_id) && Engine_Api::_()->hasModuleBootstrap('ynbusinesspages')) :?>
					<?php $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->business_id);?>
					<?php endif;?>
					<?php if (!empty($this->group_id) && Engine_Api::_()->hasModuleBootstrap('advgroup')) :?>
					<?php $group = Engine_Api::_()->getItem('group', $this->group_id);?>
					<?php endif;?>
					<?php if (!empty($business)) :?>
					<?php $url = $this->url(array('action' => 'edit', 'song_id' => $item -> getIdentity(), 'business_id' => $this->business_id, 'parent_type' => 'ynbusinesspages_business'), 'ynmusic_song', true);?>
					<?php elseif(!empty($group)): ?>
					<?php $url = $this->url(array('action' => 'edit', 'song_id' => $item -> getIdentity(), 'subject_id' => $this->group_id, 'parent_type' => 'group'), 'ynmusic_song', true);?>
					<?php else: ?>
					<?php $url = $this->url(array('action' => 'edit', 'song_id' => $item -> getIdentity()), 'ynmusic_song', true);?>	
					<?php endif;?>
					<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-pencil-square-o"></i><span class="label"><?php echo $this->translate('Edit')?></span></a>
					<?php endif;?>
					
					<?php if ($item->isDeletable()) :?>
					<?php $url = $this->url(array('action' => 'delete', 'id' => $item -> getIdentity()), 'ynmusic_song', true);?>
					<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-trash"></i><span class="label"><?php echo $this->translate('Delete')?></span></a>
					<?php endif;?>
					
					<?php if ($this->viewer()->getIdentity() && !$item->isOwner($this->viewer())):?>
					<?php $url = $this->url(array(
				        'module' => 'core',
				        'controller' => 'report',
				        'action' => 'create',
				        'subject' => $item->getGuid(),
				        'format' => 'smoothbox'),'default', true)?>
				    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-ban"></i><span class="label"><?php echo $this->translate('Report')?></span></a>
					<?php endif;?>
					
					<?php if ($business && $business->isAllowed('music_delete', null, $item)) :?>
					<?php echo $this->htmlLink(array(
	                    'route' => 'ynbusinesspages_specific',
	                    'action' => 'remove-item',
	                    'item_id' => $item->getIdentity(),
	                    'item_type' => 'ynmusic_song',
	                    'item_label' => 'Song',
	                    'remove_action' => 'music_delete',
	                    'business_id' => $business->getIdentity(),
	                ),
	                '<i class="fa fa-times"></i><span class="label">'.$this->translate('Delete Song To Business').'</span>',
	                array('class'=>'action-link smoothbox'))
	                ?>	
					<?php endif;?>
					
					<?php if ($group && $item->isOwner($this->viewer())) :?>
					<?php echo $this->htmlLink(array(
	                    'route' => 'group_extended',
	                    'module' => 'advgroup',
	                    'controller' => 'social-music',
	                    'action' => 'delete',
	                    'item_id' => $item->getIdentity(),
	                    'type' => 'ynmusic_song',
	                    'group_id' => $group->getIdentity(),
	                ),
	                '<i class="fa fa-times"></i><span class="label">'.$this->translate('Remove Song To Group').'</span>',
	                array('class'=>'action-link smoothbox'))
	                ?>	
					<?php endif;?>
					<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
				</div>
				
			</div>
		</div>
		<?php endif;?>
	</div>
	
	<div class="song-title music-title">
		<span class="value"><?php echo $item ?></span>
	</div>

	<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>

	<?php $genre = $item->getFirstGenre();?>
	<?php if (!empty($genre)) :?>
	<div class="song-genre music-genre">
		<?php echo $genre;?>
	</div>
	<?php endif;?>
	<div class="song-artist-owner">
		<?php $artists = $item->getArtists();?>
		<?php if (!empty($artists)) :?>
		<span class="artist label"><?php echo $this->translate('By ')?></span>
		<span class="artist value"><?php echo implode(', ', $artists)?></span>
		 
		 <?php endif;?>
	</div>
	<div class="song-creation_time music-creation_time">
		<?php echo $this->timestamp(strtotime($item->creation_date))?>
	</div>

	<div class="song-statistic music-statistic">
		<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
		<span class="like-count"><i class="fa fa-thumbs-up"></i><span><?php echo $item -> like_count;?></span></span>
		<span class="comment-count"><i class="fa fa-comments-o"></i><?php echo $item -> comment_count;?></span>
	</div>
</div>

<!-- MODE LIST VIEW -->
<div class="song-list-view">
	<div class="song-photo music-photo">		
		<a href="<?php echo $item->getHref();?>" class="song-photo-bg" style="background:url(<?php echo $photo_url; ?>)"></a>
		<div class="song-photo-bgopacity"></div>

		<?php if ($this->history) :?>
		<div class="remove-history">
			<input type="checkbox" class="remove-history-checkbox" value="<?php echo $this->history?>"/>
			<?php echo $this->htmlLink(array('action'=>'remove','id'=>$this->history,'route'=>'ynmusic_history'), $this->translate('Remove'), array('class'=>'smoothbox'));?>
		</div>
		<?php endif; ?>
	</div>

	<div class="list-view-song-info list-view-info">
		<div class="list-view-song-info-top list-view-info-top">
			<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>

			<div class="box-float">
				<div class="song-title music-title">
					<span class="value"><?php echo $item ?></span>
				</div>
				<?php $genre = $item->getFirstGenre();?>
				<?php if (!empty($genre)) :?>
				<div class="song-genre music-genre">
					<?php echo $genre;?>
				</div>
				<?php endif;?>
			</div>

			<div class="box-float">
				<div class="song-artist-owner list-view-artist-owner">
					<?php $artists = $item->getArtists();?>
					<?php if (!empty($artists)) :?>
					<span class="artist label"><?php echo $this->translate('Artist:')?></span>
					<span class="artist value"><?php echo implode(', ', $artists)?></span>
				 	<span class="list-view-hyphen">&nbsp;-&nbsp;</span>
					 <?php endif;?>
					<span class="owner label"><?php echo $this->translate('Posted:')?></span>
					<span class="owner value"><?php echo $item->getOwner() ?></span>
				</div>
				<div class="song-creation_time music-creation_time">
					<?php echo $this->timestamp(strtotime($item->creation_date))?>
				</div>
			</div>
			
			<?php 
				$noPlayImg = $item->getNoPlayImage();
				$playImg = $item->getPlayImage();
			?>
			<?php if ($noPlayImg && $playImg) :?>
			<div class="image-song">
				<div class="no-play-div" style="background-image: url('<?php echo $noPlayImg?>');">
				</div>
				<div class="play-div" style="width: 0; overflow: hidden; background-image: url('<?php echo $playImg?>');">
				</div>
			</div>
			<?php endif;?>
			
			<div class="time-song">
				<div class="duration-time"><?php echo date('i:s', $item->duration)?></div>
			</div>
			
		</div>

		<div class="song-statistic_action statistic_action">
			<div class="song-statistic music-statistic">
				<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
				<span class="like-count"><i class="fa fa-thumbs-up"></i><span><?php echo $item -> like_count;?></span></span>
				<span class="comment-count"><i class="fa fa-comments-o"></i><?php echo $item -> comment_count;?></span>
			</div>
			
			<div class="song-action music-action">
				<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist() || Engine_Api::_()->ynmusic()->canCreatePlaylist()) :?>
				<div class="list-view-song-action-add-playlist show-hide-action">
					<a class="action-link show-hide-btn" href="javascript:void(0)"><i class="fa fa-plus" title="<?php echo $this->translate('Add to playlist')?>"></i></a>
					<div class="action-pop-up" style="display: none">
						<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist()) : ?>
						<div class='song-action-add-playlist dropdow-action-add-playlist'>
							<span><?php echo $this-> translate('add to') ?></span>
							<?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$item->getGuid()),'ynmusic_playlist', true)?>
							<div rel="<?php echo $url;?>" class="music-loading add-to-playlist-loading" style="display: none;text-align: center">
								<span class="ajax-loading">
							    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
								</span>
							</div>
							<div class="add-to-playlist-notices"></div>
							<div class="box-checkbox">
								<?php echo $this->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $item)); ?>
							</div>
					    </div>
					    <?php endif; ?>
					    <?php if (Engine_Api::_()->ynmusic()->canCreatePlaylist()) : ?>
						<div class="song-action-dropdown music-action-dropdown">
							<a href="javascript:void(0);" onclick="addNewPlaylist(this, '<?php echo $item->getGuid()?>');" class="action-link add-to-playlist" data="<?php echo $item->getGuid()?>"><i class="fa fa-plus"></i><span class="label"><?php echo $this->translate('Add to new playlist')?></span></a>
							<span class="play_list_span"></span>
							
							<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif;?>
				
				<?php if ($item->isDownloadable()) :?>
				<a class="action-link download" download="<?php echo $item->getTitle().'.mp3'?>" href="<?php echo $item->getFilePath()?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Download')?>"><i class="fa fa-download"></i></a>
				<?php endif;?>
				
				<?php if ($item->isCommentable()) :?>
				<?php if( $item->likes()->isLike($this->viewer()) ): ?>
				<a class="action-link like liked" href="javascript:void(0);" onclick="ynmusicUnlike(this, '<?php echo $item->getType()?>', '<?php echo $item->getIdentity() ?>')" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Unlike')?>"><i class="fa fa-thumbs-up"></i></a>
				<?php else: ?>
				<a class="action-link like" href="javascript:void(0);" onclick="ynmusicLike(this, '<?php echo $item->getType()?>', '<?php echo $item->getIdentity() ?>')" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Like')?>"><i class="fa fa-thumbs-up"></i></a>
				<?php endif;?>
				<?php endif;?>
				
				<?php if ($this->viewer()->getIdentity()):?>
				<?php $url = $this -> url(array(
			        'module' => 'activity',
			        'controller' => 'index',
			        'action' => 'share',
			        'type' => 'ynmusic_song',
			        'id' => $item->getIdentity(),
			        'format' => 'smoothbox'),'default', true) ?>
				<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Share')?>"><i class="fa fa-share-alt"></i></a>
				<?php endif;?>
				
				<?php if ($item->isEditable()) :?>
				<?php $url = $this->url(array('action' => 'edit', 'song_id' => $item -> getIdentity()), 'ynmusic_song', true);?>
				<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Edit')?>"><i class="fa fa-pencil-square-o"></i></a>
				<?php endif;?>
				
				<?php if ($item->isDeletable()) :?>
				<?php $url = $this->url(array('action' => 'delete', 'id' => $item -> getIdentity()), 'ynmusic_song', true);?>
				<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Delete')?>"><i class="fa fa-trash"></i></a>
				<?php endif;?>
				
				<?php if ($this->viewer()->getIdentity() && !$item->isOwner($this->viewer())):?>
				<?php $url = $this->url(array(
			        'module' => 'core',
			        'controller' => 'report',
			        'action' => 'create',
			        'subject' => $item->getGuid(),
			        'format' => 'smoothbox'),'default', true)?>
			    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Report')?>"><i class="fa fa-ban"></i></a>
				<?php endif;?>
			</div>
		</div>

	</div>

</div>

