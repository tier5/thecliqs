<?php $item = $this -> item;?>

<div class="ynmusic-song-feed music-item" id="<?php echo $item->getGuid()?>">

	<?php if (!$item->isViewable()) :?>
		<div class="disabled"></div>
	<?php endif;?>
	
	<?php $photo_url = ($item->getPhotoUrl('thumb.profile')) ? $item->getPhotoUrl('thumb.profile') : "application/modules/Ynmusic/externals/images/nophoto-feed.png";?>
	
	<div class="song-photo music-photo">
		<a href="<?php echo $item->getHref(); ?>" style="background-image:url(<?php echo $photo_url; ?>)"></a>
		<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn parent"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>
	</div>
	<div class="title" style="width: calc(100% - 60px)"><?php echo $item?></div>
</div>