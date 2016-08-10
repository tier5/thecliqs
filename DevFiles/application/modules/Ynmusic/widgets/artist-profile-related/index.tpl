<ul id="artist-profile-related" class="artist-items clearfix">
<?php foreach($this -> artists as $artist ):?>
	<li class="artist-item">
		<?php 
		$photoUrl = $artist->getPhotoUrl();
		if (!$photoUrl)	 $photoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/nophoto_artist_thumb_profile.png';
		?>
		<div class="artist-photo music-photo" style="background-image: url('<?php echo $photoUrl?>')">
		</div>
		<div class="artist-title music-title">
			<?php echo $artist;?>
		</div>
		<div class="song-count">
			<span class="value"><i class="fa fa-music"></i> <?php echo $this -> translate(array("ynmusic_song_count_num", "%s songs" ,$artist -> getCountItems('ynmusic_song')), $artist -> getCountItems('ynmusic_song'));?></span>
		</div>
		<div class="album-count">
			<span class="value"><i class="fa fa-th-large"></i> <?php echo $this -> translate(array("ynmusic_album_count_num", "%s albums" ,$artist -> getCountItems('ynmusic_album')), $artist -> getCountItems('ynmusic_album'));?></span>
		</div>
	</li>
<?php endforeach;?>
</ul>
