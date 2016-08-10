<ul class="ynvideochannel_channel_of_day">
    <?php foreach($this -> paginator as $item):?>
    <li class="ynvideochannel_channel_of_day-item">

        <?php if($item -> is_featured):?>
            <div class="ynvideochannel_videos_channel-featured"><?php echo $this -> translate("Featured")?></div>
        <?php endif;?>
        <?php $photo_url = $item->getPhotoUrl('thumb.normal'); ?>
        <div class="ynvideochannel_channel_of_day-bg" style="background-image: url('<?php echo $photo_url?>')">
            <div class="ynvideochannel_channel_of_day-info">
                <a class="ynvideochannel_channel_of_day-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>

                <span class="ynvideochannel_channel_of_day-count-subscribe">
                    <?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?>
                </span>

                <div class="ynvideochannel_channel_of_day-count-video">
                    <i class="fa fa-video-camera" aria-hidden="true"></i> <?php echo $item -> video_count?>
                </div>
            </div>
        </div>
    </li>
    <?php endforeach;?>
</ul>
