<?php if(count($this->videos) > 0):?>
    <ul class="ynultimatevideo_list_most_items clearfix">
    <?php foreach( $this->videos as $video ): ?>
        <?php echo $this->partial('_video_item.tpl', 'ynultimatevideo', array('video' => $video)); ?>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("No videos found.") ?>
        </span>
    </div>
<?php endif; ?>