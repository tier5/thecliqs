<?php if ($this->paginator->getTotalItemCount() > 0) :?>
<div id="ynmusic-album-listing">
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="album-count music-count">
		<span class="label"><?php echo $this->translate(array('ynmusic_album_count', 'Albums', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?></span>
		<span class="value count"><?php echo $this->translate('(%s)', $this->paginator->getTotalItemCount())?></span>
	</div>
	<?php endif; ?>
	
	<ul class="album-items music-items clearfix">

		<?php foreach ($this->paginator as $item) :?>
		<?php $business_id = (!empty($this->business_id)) ? $this->business_id : 0;?>
		<?php $group_id = (!empty($this->group_id)) ? $this->group_id : 0;?>
		<li id="<?php echo $item->getGuid()?>" class="album-item music-item <?php if (!$item->getCountSongs() || !$item->isViewable()) echo 'clearfix'?>">
			<?php echo $this->partial('_album_view.tpl', 'ynmusic', array('item' => $item, 'business_id' => $business_id, 'group_id' => $group_id));?>
		</li>
		<?php endforeach;?>
	</ul>
	
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="view-all">
		<a href="<?php echo $this->url(array(),'ynmusic_album', true)?>?<?php echo http_build_query($this->formValues)?>"><?php echo $this->translate(array('View %s Album', 'View %s Albums', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></a>
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
		<span><?php echo $this->translate('There are no albums.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>