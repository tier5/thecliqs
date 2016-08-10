<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.ynvideochannel_main_manage').getParent().addClass('active');
    });
</script>

<div class="ynvideochannel_count_videos">
    <i class="fa fa-bookmark"></i>
    <?php $totalItems = $this->paginator->getTotalItemCount();?>
    <?php echo $this -> translate(array("%s playlist", "%s playlists", $totalItems), $totalItems)?>
</div>

<?php if ($totalItems > 0):?>
    <ul class="ynvideochannel_playlist_list">
        <?php foreach ($this->paginator as $item) {
            echo $this->partial('_playlist_item.tpl', array('item' => $item));
        } ?>
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
            <?php echo $this->translate('No playlists found.');?>
        </span>
    </div>
<?php endif; ?>

<script type="text/javascript">
    ynvideochannelPlaylistOptions();
</script>