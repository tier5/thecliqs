<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
?>

<div class="ynultimatevideo_feed_item">
    <div class="ynultimatevideo_img">
        <img src="<?php echo $photoUrl; ?>" alt="">

        <div class="ynultimatevideo_duration_btn">
            <?php echo $this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $item)) ?>
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