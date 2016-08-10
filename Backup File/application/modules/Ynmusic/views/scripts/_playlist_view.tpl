<?php $item = $this -> item;?>
<?php if (!$item->isViewable()) :?>
<div class="disabled"></div>
<?php endif;?>
<?php $photo_url = ($item->getPhotoUrl()) ? $item->getPhotoUrl() : "application/modules/Ynmusic/externals/images/nophoto_playlist_thumb_profile.png";?>

<div class="playlist-photo music-photo">
	<div class="playlist-photo-bg" style="background-image: url(<?php echo $photo_url; ?>)">
		<div class="icon-playing">
			<img src="application/modules/Ynmusic/externals/images/playing.gif" alt="">
		</div>
	</div>
	<div class="playlist-photo-bgopacity"></div>
	<?php //if ($item->getCountAvailableSongs()) :?>	
	<div class="play-btn-<?php echo $item->getGuid()?> grid-view-music-play-btn music-play-btn">
		<div class="playlist-statistic music-statistic">
			<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
			<span class="like-count"><i class="fa fa-thumbs-up"></i><span><?php echo $item -> like_count;?></span></span>
			<span class="comment-count"><i class="fa fa-comments-o"></i><?php echo $item -> comment_count;?></span>
		</div> 

		<a href="javascript:void(0)">
			<i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i>
		</a>
	</div>
	<?php //endif;?>
	
	<?php if ($this->viewer()->getIdentity()):?>
	<div class="grid-view-playlist-action-add-playlist show-hide-action">
		<a class="action-link show-hide-btn" href="javascript:void(0)"><i class="fa fa-plus"></i></a>
		<div class="action-pop-up" style="display: none">
			<div class="playlist-action-dropdown music-action-dropdown">
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
			        'type' => 'ynmusic_playlist',
			        'id' => $item->getIdentity(),
			        'format' => 'smoothbox'),'default', true) ?>
				<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-share-alt"></i><span class="label"><?php echo $this->translate('Share')?></span></a>
				<?php endif;?>
				
				<?php if ($item->isEditable()) :?>
				<?php $url = $this->url(array('action' => 'edit', 'id' => $item -> getIdentity()), 'ynmusic_playlist', true);?>
				<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-pencil-square-o"></i><span class="label"><?php echo $this->translate('Edit')?></span></a>
				<?php endif;?>
				
				<?php if ($item->isDeletable()) :?>
				<?php $url = $this->url(array('action' => 'delete', 'id' => $item -> getIdentity()), 'ynmusic_playlist', true);?>
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
				
				<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if ($this->history) :?>
	<div class="remove-history">
		<input type="checkbox" class="remove-history-checkbox" value="<?php echo $this->history?>"/>
		<?php echo $this->htmlLink(array('action'=>'remove','id'=>$this->history,'route'=>'ynmusic_history'), $this->translate('Remove'), array('class'=>'smoothbox'));?>
	</div>
	<?php endif; ?>
</div>

<div class="list-view-playlist-info list-view-info">
	<div class="list-view-playlist-info-top list-view-info-top">
		<?php //if ($item->getCountAvailableSongs()) :?>
		<div class="play-btn-<?php echo $item->getGuid()?> list-view-music-play-btn music-play-btn">
			<a href="javascript:void(0)">
				<i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i>
			</a>
		</div>
		<?php //endif;?>
		
		<div class="box-float">
			<div class="playlist-title music-title">
				<span class="label"><?php echo $this -> translate('Playlist:');?></span>
				<span class="value"><?php echo $item ?></span>
			</div>
			<?php $genre = $item->getFirstGenre();?>
			<?php if (!empty($genre)) :?>
			<div class="playlist-genre music-genre">
				<?php echo $genre;?>
			</div>
			<?php endif;?>
		</div>
			
		<div class="box-float">
			<div class="playlist-artist-owner list-view-artist-owner">
				<div class="playlist-owner music-owner">
					<span class="owner label"><?php echo $this->translate('Posted:')?></span>
					<span class="owner value"><?php echo $item->getOwner() ?></span>
				</div>
			</div>
			<div class="playlist-creation_time music-creation_time">
				<?php echo $this->timestamp(strtotime($item->creation_date))?>
			</div>
		</div>
	</div>

	<?php $firstSong = $item->getFirstSong();?>
	<?php if ($firstSong) : ?>
	<?php 
		$noPlayImg = $firstSong->getNoPlayImage();
		$playImg = $firstSong->getPlayImage();
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
		<div class="duration-time"><?php echo date('i:s', $firstSong->duration)?></div>
	</div>
	
	<?php endif;?>

	<div class="playlist-statistic_action statistic_action">
		<div class="list-view-playlist-statistic music-statistic">
			<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
			<span class="like-count"><i class="fa fa-thumbs-up"></i><span><?php echo $item -> like_count;?></span></span>
			<span class="comment-count"><i class="fa fa-comments-o"></i><?php echo $item -> comment_count;?></span>
		</div>    

		<div class="list-view-playlist-action music-action">
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
		        'type' => 'ynmusic_playlist',
		        'id' => $item->getIdentity(),
		        'format' => 'smoothbox'),'default', true) ?>
			<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Share')?>"><i class="fa fa-share-alt"></i></a>
			<?php endif;?>
			
			<?php if ($item->isEditable()) :?>
			<?php $url = $this->url(array('action' => 'edit', 'id' => $item -> getIdentity()), 'ynmusic_playlist', true);?>
			<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Edit')?>"><i class="fa fa-pencil-square-o"></i></a>
			<?php endif;?>
			
			<?php if ($item->isDeletable()) :?>
			<?php $url = $this->url(array('action' => 'delete', 'id' => $item -> getIdentity()), 'ynmusic_playlist', true);?>
			<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>" title="<?php echo $this->translate('Delete')?>"><i class="fa fa-trash"></i></a>
			<?php endif;?>
			
			<?php if ($this->viewer()->getIdentity() && !$item->isOwner($this->viewer())):?>
			<?php $url = $this->url(array(
		        'module' => 'core',
		        'controller' => 'report',
		        'action' => 'create',
		        'subject' => $item->getGuid(),
		        'format' => 'smoothbox'),'default', true)?>
		    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"  title="<?php echo $this->translate('Report')?>"><i class="fa fa-ban"></i></a>
			<?php endif;?>
		</div>
	</div>

	<!-- get songs -->
	<?php $songs = $item -> getSongs();?>
	<?php if (count($songs)) :?>
	<div class="playlist-songs music-songs">
		<?php echo $this->partial('_song-list.tpl', 'ynmusic', array('songs' => $songs, 'parent' => $item));?>
	</div>
	<?php endif;?>	
</div>
