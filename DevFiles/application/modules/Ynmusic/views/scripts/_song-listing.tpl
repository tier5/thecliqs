<?php if ($this->paginator->getTotalItemCount() > 0) :?>
<div id="ynmusic-song-listing">
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="song-count music-count">
		<span class="label"><?php echo $this->translate(array('ynmusic_song_count', 'Songs', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?></span>
		<span class="value count"><?php echo $this->translate('(%s)', $this->paginator->getTotalItemCount())?></span>
	</div>
	<?php endif;?>
	<ul class="song-items music-items clearfix">
	<?php foreach ($this->paginator as $item) :?>
	<?php $business_id = (!empty($this->business_id)) ? $this->business_id : 0;?>
	<?php $group_id = (!empty($this->group_id)) ? $this->group_id : 0;?>
	<li id="<?php echo $item->getGuid()?>" class="song-item music-item">
		<?php echo $this->partial('_song_view.tpl', 'ynmusic', array('item' => $item, 'business_id' => $business_id, 'group_id' => $group_id));?>
	</li>
	<?php endforeach;?>
	</ul>
	
	<?php if (!$this->paging && !$this->ajaxPaging):?>
	<div class="view-all">
		<a href="<?php echo $this->url(array(),'ynmusic_song', true)?>?<?php echo http_build_query($this->formValues)?>"><?php echo $this->translate(array('View %s Song', 'View %s Songs', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></a>
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
		<span><?php echo $this->translate('There are no songs.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>