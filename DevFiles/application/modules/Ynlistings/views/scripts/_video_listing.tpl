<div class="video_thumb_wrapper">
    <?php if ($this->video->duration): ?>
        <?php echo $this->partial('_video_duration.tpl', 'ynlistings', array('video' => $this->video)) ?>
    <?php endif ?>
    <?php
    if ($this->video->photo_id) {
        echo $this->htmlLink($this->video->getHref(), $this->itemPhoto($this->video, 'thumb.normal'));
    } else {
        echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynlistings/externals/images/video.png">';
    }
    ?>
</div>

