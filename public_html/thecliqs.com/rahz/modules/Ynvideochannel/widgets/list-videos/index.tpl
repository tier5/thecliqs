<div class="ynvideochannel_count_videos">
    <i class="fa fa-video-camera" aria-hidden="true"></i>
    <?php $totalVideos = $this->paginator->getTotalItemCount();?>
    <?php echo $this -> translate(array("%s video", "%s videos", $totalVideos), $totalVideos)?>
</div>
<?php if ($totalVideos > 0):?>
    <ul class="ynvideochannel_browse_video_items ynvideochannel_video_listing_items">
        <?php foreach ($this->paginator as $video):
        echo $this->partial('_video_item.tpl', array('item' => $video, 'showAddto' => true));
        endforeach;?>
    </ul>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues
        ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No videos found.');?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    ynvideochannelVideoOptions();
</script>