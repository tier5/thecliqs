<?php
    $item = $this -> item;
	$photo_url = $item ->getPhotoUrl('thumb.profile');
	if (!$photo_url) {
		$photo_url = $item->getLastVideoPhoto();
	}
	if (!$photo_url) {
		$photo_url = $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynultimatevideo/externals/images/nophoto_playlist_thumb_icon.png';
	}
	$count = $item->getVideoCount();
?>

<!-- thumbnail -->
<div class="ynultimatevideo_wrapper_block">
<div class="ynultimatevideo_wrapper" style="background-image: url(<?php echo $photo_url ?>)">
	<div class="ynultimatevideo_background_opacity"></div>
	<div class="ynultimatevideo_playlist_play video-play-btn">
        <a href="<?php echo $item->getHref(); ?>">
            <i class="fa fa-play"></i>
        </a>
	</div> 
</div>
</div>

<div class="ynultimatevideo_playlist_info clearfix">
	<div class="ynultimatevideo_playlist_title" style="width: calc(100% - 62px)">
		<?php echo $item ?>
	</div>
	<div class="ynultimatevideo_playlist_count">
		<span><?php echo $count ?></span>
		<span><?php echo $this->translate('videos') ?></span>
	</div>
</div>
