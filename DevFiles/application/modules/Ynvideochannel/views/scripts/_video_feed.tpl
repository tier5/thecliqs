<?php
    $item = $this -> item;
    $photoUrl = $item ->getPhotoUrl('thumb.profile');
    if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png';
?>

<div class="ynvideochannel_feed-item">
    <a href="<?php echo $item->getHref(); ?>" class="ynvideochannel_feed-bg" style="background-image: url('<?php echo $photoUrl; ?>')">
        <span><i class="fa fa-play"></i></span>
    </a>

    <div class="ynvideochannel_feed-info">
        <div class="ynvideochannel_feed-title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => '', 'title' => $item->getTitle())); ?>
        </div>

        <div class="ynvideochannel_feed-category">
            <?php if ($item->category_id): ?>
                <span><?php echo $this->translate('Category:'); ?></span>
                <?php echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item)); ?>
            <?php endif; ?>
        </div>
        
        <?php if($item->description): ?>
        <div class="ynvideochannel_feed-description">
            <?php echo $this -> string() -> truncate(strip_tags($item->description), 500); ?>
        </div>
        <?php endif; ?>
    </div>
</div>