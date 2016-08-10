<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl) {
        $photoUrl = $item->getLastVideoPhoto();
    }
    if (!$photoUrl) {
        $photoUrl = $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynultimatevideo/externals/images/nophoto_playlist_thumb_icon.png';
    }
    $count = $item->getVideoCount();
?>

<div class="ynultimatevideo_feed_item">
    <div class="ynultimatevideo_img">
        <img src="<?php echo $photoUrl; ?>" alt="">

        <div class="ynultimatevideo_duration_btn">
            <span class="ynultimatevideo_feed_count_video"><?php echo $this->translate(array('<b>%1$s</b> video','<b>%1$s</b> videos', $count), $this->locale()->toNumber($count)); ?></span> &nbsp;&nbsp;
            <a href="<?php echo $item->getHref(); ?>" class="ynultimatevideo_btn_play_feed" ><i class="fa fa-play"></i></a>
        </div>
    </div>

    <div class="ynultimatevideo_info">
        <div class="ynultimatevideo_title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => '', 'title' => $item->getTitle())); ?>
        </div>
        
        <div class="ynultimatevideo_description">
            <?php echo $item->description; ?>
        </div>
    </div>
</div>