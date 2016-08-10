<?php if(count($this->paginator) >0 ):?>
<ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynbusinesspages_newest_videos" style="padding-bottom:0px;">
    <?php foreach ($this->paginator as $item): ?>
        <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
            <?php
            echo $this->partial('_video_listing.tpl', 'ynbusinesspages', array(
                'video' => $item,
                'recentCol' => $this->recentCol
            ));
            ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php endif;?>