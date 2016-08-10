<ul id="ynmusic-songs-you-may-like">
<?php foreach( $this->songs as $song ): ?>
	<li>
	<?php if (!$song->isViewable()) :?>
	<div class="disabled"></div>
	<?php endif;?>
	<div class="song-title music-title">
		<?php echo $song;?>
	</div>
	<div class="ynmusic-song-info">
		<?php $artists = $song->getArtists();?>
		<div class="song-artist music-artist">
			<?php if (!empty($artists)) :?>
				<?php echo implode(', ', $artists)?>
			<?php endif;?>
		</div>
		<div class="play-count"><i class="fa fa-headphones"></i><?php echo $song -> play_count;?></div>
	</div>
	<div class="play-btn-<?php echo $song->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i></a></div>
	</li>
<?php endforeach;?>
</ul>
