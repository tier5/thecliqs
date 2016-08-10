<?php $item = $this->item; ?>
<li class="ynvideochannel_video_listing_item ynvideochannel_videos_duration_hover clearfix">    


    <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
    <div class="ynvideochannel_video_listing_item-bg" style="background-image: url('<?php echo $photo_url?>')">
        <a href="<?php echo $item -> getHref()?>" style="background-image: url('<?php echo $photo_url?> "></a>
        <?php if($item -> is_featured):?>
            <div class="ynvideochannel_videos_channel-featured"><?php echo $this -> translate("Featured")?></div>
        <?php endif;?>

        <div class="ynvideochannel_video_punch-hole"></div>

        <div class="ynvideochannel_videos_duration ynvideochannel_videos_duration-bgwhite">
            <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $item)); ?>
            <a href="<?php echo $item -> getHref()?>">
                <i class="fa fa-play" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <div class="ynvideochannel_video_listing_item-info">
        <a class="ynvideochannel_video_listing_item-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>
        
        <div class="ynvideochannel_video_listing_item-owner-date">
            <span class="ynvideochannel_video_listing_item-owner">
                <span><?php echo $this->translate('Posted by') ?></span>
                <?php echo $item -> getOwner() ?>
                <span>&nbsp;.&nbsp;</span>
            </span>
            <span class="ynvideochannel_video_listing_item-date"><?php echo $this->timestamp(strtotime($item->creation_date)) ?></span>
        </div>
        
        <div class="ynvideochannel_video_listing_item-description"><?php echo $this -> string() -> truncate(strip_tags($item -> description), 200) ?></div>

        <div class="ynvideochannel_video_listing_item-count-rating clearfix">
            <div class="ynvideochannel_video_listing_item-count">
                <span><?php echo $this -> translate(array("<span>%s</span> view", "<span>%s</span> views", $item -> view_count), $item -> view_count)?></span>
                <span><?php echo $this -> translate(array("<span>%s</span> like", "<span>%s</span> likes", $item -> like_count), $item -> like_count)?></span>
                <span><?php echo $this -> translate(array("<span>%s</span> comment", "<span>%s</span> comments", $item -> comment_count), $item -> comment_count)?></span>
                <span><?php echo $this -> translate(array("<span>%s</span> favorite", "<span>%s</span> favorites", $item -> favorite_count), $item -> favorite_count)?></span>
            </div>

            <div class="ynvideochannel_video_listing_item-rating ynvideochannel_videos_rating">
                <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $item->rating)); ?>
            </div>
        </div>
    </div>



    <div class="ynvideochannel_video_listing_item-options">
        <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $item,'unfavorite' => $this -> unfavorite)); ?>
        <?php if($this -> showAddto) echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $item)); ?>
    </div>
    
</li>