<ul id="ynmusic-more-playlist">
	<?php foreach ($this->playlists as $playlist):?>
	<li class="playlist-item music-item">

	<?php $photo_url = ($playlist->getPhotoUrl('thumb.profile')) ? $playlist->getPhotoUrl('thumb.profile') : "application/modules/Ynmusic/externals/images/nophoto_album_thumb_icon.png";?>
	
	<div class="album-photo music-photo" style="background-image: url(<?php echo $photo_url; ?>)">
		<div class="play-btn-<?php echo $playlist->getGuid()?> music-play-btn">
			<a href="javascript:void(0)">
				<i rel="<?php echo $playlist->getGuid()?>" class="fa fa-play"></i>
			</a>
		</div>
		<div class="icon-playing">
			<img src="application/modules/Ynmusic/externals/images/playing.gif" alt="">
		</div>
	</div>

	<div class="album-info music-info">
		<div class="album-title music-title">
			<?php echo $playlist;?>
		</div>

		<div class="song-count"><i class="fa fa-music"></i><?php echo $this->translate(array('ynmusic_song_count_num', '%s songs', $playlist->getCountSongs()), $playlist->getCountSongs())?></div>

		<div class="play-count"><i class="fa fa-headphones"></i><?php echo number_format($playlist -> play_count)?></div>
	</div>
	</li>
	<?php endforeach;?>	
</ul>
