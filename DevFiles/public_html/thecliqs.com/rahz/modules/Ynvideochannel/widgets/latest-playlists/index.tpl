<?php echo $this->partial('_playlists_grid.tpl', 'ynvideochannel', array('playlists' => $this->paginator)); ?>
<?php if(!$this->paginator->getTotalItemCount()): ?>
<div class="tip">
        <span>
            <?php echo $this->translate("No playlists found.") ?>
        </span>
</div>
<?php endif; ?>