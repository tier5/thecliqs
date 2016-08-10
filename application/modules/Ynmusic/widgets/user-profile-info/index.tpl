<div class="owner-photo">
	<?php $photo_url = ($this->owner->getPhotoUrl()) ? $this->owner->getPhotoUrl() : "application/modules/Ynmusic/externals/images/nophoto_user_thumb_profile.png";?>

	<a href="<?php echo $this->owner->getHref(); ?>" style="background-image: url(<?php echo $photo_url; ?>)"></a>
</div>

<div class="box-infomation">
	<div class="owner-title">
		<?php echo $this->translate('by %s', $this->owner)?>
	</div>

	<div class="statistics-info">
		<div class="album-count music-count">
			<span class="label"><?php echo $this->translate('Albums')?></span>
			<?php $url = $this->url(array('user_id' => $this->owner->getIdentity()), 'ynmusic_album', true)?>
			<span class="value"><a href="<?php echo $url?>"><?php echo Engine_Api::_()->ynmusic()->getUserMusicCount('album', $this->owner)?></a></span>
		</div>
		
		<div class="playlist-count music-count">
			<span class="label"><?php echo $this->translate('Playlists')?></span>
			<?php $url = $this->url(array('user_id' => $this->owner->getIdentity()), 'ynmusic_playlist', true)?>
			<span class="value"><a href="<?php echo $url?>"><?php echo Engine_Api::_()->ynmusic()->getUserMusicCount('playlist', $this->owner)?></a></span>
		</div>
		
		<div class="song-count music-count">
			<span class="label"><?php echo $this->translate('Songs')?></span>
			<?php $url = $this->url(array('user_id' => $this->owner->getIdentity()), 'ynmusic_song', true)?>
			<span class="value"><a href="<?php echo $url?>"><?php echo Engine_Api::_()->ynmusic()->getUserMusicCount('song', $this->owner)?></a></span>
		</div>
	</div>
</div>