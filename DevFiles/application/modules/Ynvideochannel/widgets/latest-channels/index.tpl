<?php echo $this->partial('_channels_grid.tpl', 'ynvideochannel', array('channels' => $this->paginator)); ?>
<?php if(!$this->paginator->getTotalItemCount()): ?>
<div class="tip">
        <span>
            <?php echo $this->translate("No channels found.") ?>
        </span>
</div>
<?php endif; ?>