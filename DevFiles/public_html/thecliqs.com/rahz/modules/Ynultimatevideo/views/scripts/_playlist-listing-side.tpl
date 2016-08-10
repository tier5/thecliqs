<?php if ($this->paginator->getTotalItemCount() > 0) :?>
	<ul >
		<?php foreach ($this->paginator as $item) :?>
			<li >
				<?php echo $this->partial('_playlist_view_side.tpl', 'ynultimatevideo', array('item' => $item));?>
			</li>
		<?php endforeach;?>
	</ul>
<?php else:?>
	<?php if ($this->paging):?>
	<div class="tip">
		<span><?php echo $this->translate('No more playlists.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>
