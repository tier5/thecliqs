<?php echo $this->partial('_videos_grid.tpl', 'ynvideochannel', array('videos' => $this->paginator)); ?>
<?php if(!$this->paginator->getTotalItemCount()): ?>
<div class="tip">
        <span>
            <?php echo $this->translate("No videos found.") ?>
        </span>
</div>
<?php endif; ?>