<?php if (count($this->songs)) :?>
<ul class="song-items music-items">
<?php $count = 1;?>

<?php foreach($this->songs as $song) :?>

	<?php if (!$song->isViewable()) continue;?>
	<li class="song-item music-item" song_id="<?php echo $song->getIdentity()?>" id="player-song-<?php echo $song->getGuid()?>" rel="<?php echo $song->getFilePath()?>">

		<div style="display: none;">
			<?php echo $this->itemPhoto($song, 'thumb.icon', array('class'=>'song-photo'))?>
		</div>

		<div class="play-btn-<?php echo $song->getGuid()?> player-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i></a></div>

		<div class="count"><?php echo $count;?>.</div>

		<div class="title"><?php echo $song?></div>

		<?php $artists = $song->getArtists();?>
		<?php if (!empty($artists)):?>
		<div class="seperate"> - </div>

		<div class="artist"><?php echo implode(', ', $artists)?></div>
		<?php endif;?>

		<div class="action">
			<?php if ($song->isDownloadable()) :?>
			<span><a class="action-link download" download="<?php echo $song->getTitle().'.mp3'?>" href="<?php echo $song->getFilePath()?>" rel="<?php echo $song->getIdentity()?>"><i class="fa fa-download"></i></a></span>
			<?php endif;?>
			<span><a class="action-link remove" href="javascript:void(0)"><i><i class="fa fa-times"></i></i></a></span>
			
			<span class="duration-time"><?php echo date('i:s', $song->duration)?></span>
		</div>


		<?php if ($this->playImg) :?>
		<input type="hidden" class="play-img" value="<?php echo $song->getPlayImage()?>" />
		<input type="hidden" class="noplay-img" value="<?php echo $song->getNoPlayImage()?>" />
		<?php endif;?>
		<input type="hidden" class="duration" value="<?php echo $song->duration?>" />
	</li>
	
	<?php $count++;?>

<?php endforeach; ?>

</ul>
<?php endif;?>