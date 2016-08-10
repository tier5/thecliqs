<ul id="ynmusic-album-profile-more">
	<?php foreach($this -> albums as $album) :?>
		<li class="album-item music-item">
			<?php $photo_url = ($album->getPhotoUrl('thumb.profile')) ? $album->getPhotoUrl('thumb.profile') : "application/modules/Ynmusic/externals/images/nophoto_album_thumb_icon.png";?>
			<div class="album-photo music-photo" style="background-image: url(<?php echo $photo_url; ?>)">
				<div class="play-btn-<?php echo $album->getGuid()?> music-play-btn">
					<a href="javascript:void(0)">
						<i rel="<?php echo $album->getGuid()?>" class="fa fa-play"></i>
					</a>
				</div>
				<div class="icon-playing">
					<img src="application/modules/Ynmusic/externals/images/playing.gif" alt="">
				</div>
			</div>

			<div class="album-info music-info">
				<div class="album-title music-title">
					<?php echo $album;?>
				</div>

				<div class="song-count"><i class="fa fa-music"></i><?php echo $this->translate(array('ynmusic_song_count_num', '%s songs', $album->getCountSongs()), $album->getCountSongs())?></div>

				<div class="play-count"><i class="fa fa-headphones"></i><?php echo number_format($album -> play_count)?></div>
			</div>


		</li>
	<?php endforeach;?>
</ul>
