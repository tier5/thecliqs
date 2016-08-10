<link rel="stylesheet" href="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/styles/owl.carousel.css" />
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery-1.10.2.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/owl.carousel.min.js"></script>


<div class="ynvideochannel_channels_slideshow owl-carousel owl-theme" id="ynvideochannel_featured_channel">
    <?php foreach($this -> paginator as $item):?>
    <div class="item ynvideochannel_channels_slideshow-item">
        <div class="ynvideochannel_channels_slideshow-options">
            <?php echo $this->partial('_channel_options.tpl', 'ynvideochannel', array('channel' => $item, 'showEditDel' => true)); ?>
        </div>

        <?php $cover_url = ($item->getCoverUrl('thumb.main')) ? $item->getCoverUrl('thumb.main') : 'application/modules/Ynvideochannel/externals/images/noimg_cover.jpg'; ?>
        <div class="ynvideochannel_channels_slideshow-bg" style="background-image: url('<?php echo $cover_url?>')">
            <div class="ynvideochannel_channels_slideshow-bgopacity"></div>
            <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/noimg_channel.jpg'; ?>
            <div class="ynvideochannel_channels_slideshow-thumb" style="background-image: url('<?php echo $photo_url?>')"></div>

            <div class='ynvideochannel_channels_slideshow-title'><a href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a></div>
            
            <div class="ynvideochannel_channels_slideshow-category-date-owner">
                <span class="ynvideochannel_channels_slideshow-category">
                    <?php if ($item->category_id)
                        echo $this->partial('_category_breadcrumb.tpl', 'ynvideochannel', array('item' => $item));
                    ?>
                </span>
                &nbsp;.&nbsp;
                <span class="ynvideochannel_channels_slideshow-date-owner">
                    <?php echo $this -> translate("%1s by %2s", $this->timestamp(strtotime($item->creation_date)), $item -> getOwner());?>
                </span>
            </div>
    
            <div class="ynvideochannel_channels_slideshow-count">
                <span id="ynvideochannel_subscriber_count_<?php echo $item -> channel_id ?>"><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> like_count), $item -> like_count)?></span>&nbsp;.&nbsp;
                <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count)?></span>
            </div>
        </div>

        <?php
        $videos = $item -> getVideos(4);
        if(count($videos)):?>
        <ul class="ynvideochannel_channels_slideshow-videos clearfix">
            <?php foreach($videos as $video):?>
                <li class="ynvideochannel_channels_slideshow-video ynvideochannel_videos_duration_hover">
                    <?php $photo_url = ($video->getPhotoUrl('thumb.normal')) ? $video->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/noimg_video.jpg'; ?>
                    <div class="ynvideochannel_channels_slideshow-video-bg" style="background-image: url('<?php echo $photo_url?>');">
                        <a href="<?php echo $video -> getHref()?>"></a>
                        <div class="ynvideochannel_channels_slideshow-video-duration ynvideochannel_videos_duration">
                            <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)); ?>
                            <a href="<?php echo $video -> getHref()?>"><i class="fa fa-play" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </li>
            <?php endforeach;?>
         <?php endif;?>
        </ul>
        
        <div class="ynvideochannel_channels_slideshow-description"><?php echo $this -> string() -> truncate(strip_tags($item -> description), 200);?></div>
    </div>
    <?php endforeach;?>
</div>


<script type="text/javascript">
ynvideochannelChannelOptions();

$$('.ynvideochannel_channels_slideshow-options').removeEvents('click').addEvent('click',function(){
    var popup_channel = this.getElement('.ynvideochannel_channel_options');
    if (popup_channel.hasClass('explained')){
        $$('.owl-next').setStyle('display','none');
    }else{
        $$('.owl-next').setStyle('display','block');
    }
});

    
jQuery(document).ready(function() {
    jQuery("#ynvideochannel_featured_channel").owlCarousel({

      navigation : true, // Show next and prev buttons
      slideSpeed : 300,
      paginationSpeed : 400,
      singleItem:true,
      navigationText:["",""],
      autoPlay: true,
    });
});

</script>