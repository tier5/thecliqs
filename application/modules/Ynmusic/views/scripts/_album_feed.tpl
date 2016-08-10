<?php $item = $this -> item;?>

<div class="ynmusic-album-feed music-item" id="<?php echo $item->getGuid()?>">

<?php if (!$item->isViewable()) :?>
	<div class="disabled"></div>
<?php endif;?>

<?php $photo_url = ($item->getPhotoUrl()) ? $item->getPhotoUrl() : "application/modules/Ynmusic/externals/images/nophoto-feed.png";?>

<div class="album-photo music-photo">
	<a href="<?php echo $item->getHref(); ?>" style="background-image:url(<?php echo $photo_url; ?>)"></a>
	<?php if ($item->getCountAvailableSongs()) :?>
	<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn parent"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>
	<?php endif;?>
</div>

<!-- get songs -->
<?php $songs = $item -> getSongs();?>
<?php if (count($songs)) :?>
<div class="album-songs music-songs" style="width: calc(100% - 162px)">
	<?php echo $this->partial('_song-list.tpl', 'ynmusic', array('songs' => $songs, 'parent' => $item));?>
</div>
<?php endif;?>
</div>
