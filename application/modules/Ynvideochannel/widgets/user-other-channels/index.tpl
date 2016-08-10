<ul class="ynvideochannel_widget_list-items">
    <?php foreach($this -> channels as $item):?>
    <li class="ynvideochannel_widget_list-item clearfix">
        <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_channel_thumb_normal.png'; ?>
        <div class="ynvideochannel_widget_list-bg" style="background-image: url(<?php echo $photo_url?>); "></div>
        <div class="ynvideochannel_widget_list-info">
            <a class="ynvideochannel_widget_list-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>
            <div class="ynvideochannel_widget_list-countsubscribe"><?php echo $this -> translate(array("%s subscriber", "%s subscribers", $item -> subscriber_count), $item -> subscriber_count)?></div>
            <div class="ynvideochannel_widget_list-countvideos"><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></div>
        </div>
    </li>
    <?php endforeach;?>
</ul>
