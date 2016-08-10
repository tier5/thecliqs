<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl) {
        $photoUrl = $item->getLastVideoPhoto();
    }
    if (!$photoUrl) {
        $photoUrl = $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png';
    }
    $count = $item->getVideoCount();
?>
<div class="ynvideochannel_feed-item ynvideochannel_feed-playlist-channel">
    <a href="<?php echo $item->getHref(); ?>" class="ynvideochannel_feed-bg" style="background-image: url('<?php echo $photoUrl; ?>')"></a>

    <div class="ynvideochannel_feed-info">
        <div class="ynvideochannel_feed-title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => '', 'title' => $item->getTitle())); ?>
        </div>

        <div class="ynvideochannel_feed-category">
            <?php echo $this->translate(array('<b>%1$s</b> video','<b>%1$s</b> videos', $count), $this->locale()->toNumber($count)); ?>
        </div>
        
        <?php if($item->description): ?>
        <div class="ynvideochannel_feed-description">
            <?php echo $this -> string() -> truncate(strip_tags($item->description), 500); ?>
        </div>
        <?php endif; ?>
    </div>
</div>  