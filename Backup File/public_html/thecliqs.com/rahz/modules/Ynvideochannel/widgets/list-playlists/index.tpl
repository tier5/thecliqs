<div class="ynvideochannel_count_videos">
    <i class="fa fa-bookmark"></i>
    <?php $totalItems = $this->paginator->getTotalItemCount();?>
    <?php echo $this -> translate(array("%s playlist", "%s playlists", $totalItems), $totalItems)?>
</div>
<?php if ($totalItems > 0):?>
    <?php echo $this->partial('_playlists_grid.tpl', array('playlists' => $this->paginator)); ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues
        ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No playlists found.');?>
        </span>
    </div>
<?php endif; ?>