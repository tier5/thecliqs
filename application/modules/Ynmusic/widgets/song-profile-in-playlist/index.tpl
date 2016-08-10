<ul id="ynmusic-song-in-playlist">
<?php foreach ($this -> playlistIds as $playlist_id):?>
	<?php $item = Engine_Api::_() -> getItem('ynmusic_playlist', $playlist_id);?>
	<?php if (!$item) continue;?>
	<li>
		<?php if (!$item->isViewable()) :?>
	<div class="disabled"></div>
	<?php endif;?>
	<div class="music-title">
		<?php echo $item;?>
	</div>

	<div class="ynmusic-item-info">
		<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
	</div>
		
	<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>
	</li>
<?php endforeach;?>
</ul>