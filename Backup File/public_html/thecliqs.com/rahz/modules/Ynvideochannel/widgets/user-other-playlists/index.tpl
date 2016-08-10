<ul class="ynvideochannel_widget_list-items">
    <?php foreach($this -> paginator as $item):?>
    <li class="ynvideochannel_widget_list-item clearfix">
        <?php $photo_url = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png'; ?>
        <div class="ynvideochannel_widget_list-bg" style="background-image: url(<?php echo $photo_url?>); "></div>
        <div class="ynvideochannel_widget_list-info">
            <a class="ynvideochannel_widget_list-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>
            <div class="ynvideochannel_widget_list-countsubscribe"><?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count)?></div>
            <div class="ynvideochannel_widget_list-countvideos"><?php echo $this -> translate(array("%s video", "%s videos", $item -> video_count), $item -> video_count)?></div>
        </div>
    </li>
    <?php endforeach;?>
</ul>
