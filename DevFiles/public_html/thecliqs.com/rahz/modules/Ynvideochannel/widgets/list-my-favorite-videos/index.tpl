<?php
    $totalVideos = $this->paginator->getTotalItemCount();
?>
<div class="ynvideochannel_count_videos">
    <i class="fa fa-video-camera"></i>
    <?php echo $this -> translate(array("%s video", "%s videos", $totalVideos), $totalVideos)?>
</div>
<?php if ($totalVideos > 0) : ?>
    <ul class="ynvideochannel_video_manage_items ynvideochannel_video_listing_items">
        <?php foreach ($this->paginator as $video) : ?>
            <?php echo $this->partial('_video_item.tpl', array('item' => $video, 'showAddto' => true, 'unfavorite' => true));?>
        <?php endforeach; ?>
    </ul>
    <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues
        ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No videos found.'); ?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    window.addEvent('domready', function () {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>