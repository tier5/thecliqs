<?php if ($this->paginator->getTotalItemCount() > 0) :?>
<div id="ynmusic-playlist-listing">
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="playlist-count music-count">
		<span class="label"><?php echo $this->translate(array('ynmusic_playlist_count', 'Playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?></span>
		<span class="value count"><?php echo $this->translate('(%s)', $this->paginator->getTotalItemCount())?></span>
	</div>
	<?php endif;?>
	<ul class="playlist-items music-items clearfix">
	<?php foreach ($this->paginator as $item) :?>
	<li id="<?php echo $item->getGuid()?>" class="playlist-item music-item <?php if (!$item->getCountSongs() || !$item->isViewable()) echo 'clearfix'?>">
		<?php echo $this->partial('_playlist_view.tpl', 'ynmusic', array('item' => $item));?>
	</li>
	<?php endforeach;?>
	</ul>
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="view-all">
		<a href="<?php echo $this->url(array(),'ynmusic_playlist', true)?>?<?php echo http_build_query($this->formValues)?>"><?php echo $this->translate(array('View %s Playlist', 'View %s Playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></a>
	</div>
	<?php elseif ($this->paging):?>
	<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
	</div>
	<?php endif;?>
</div>
<?php else:?>
	<?php if ($this->paging):?>
	<div class="tip">
		<span><?php echo $this->translate('There are no playlists.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>
