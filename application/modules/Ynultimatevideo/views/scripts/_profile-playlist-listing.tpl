<?php if ($this->paginator->getTotalItemCount() > 0) :?>
	<div id="ynultimatevideo-playlist-listing">
		<?php if (!$this->paging && !$this->ajaxPaging):?>
			<div class="playlist-count">
				<span class="label"><?php echo $this->translate(array('ynultimatevideo_playlist_count', 'Playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?></span>
				<span class="value count"><?php echo $this->translate('(%s)', $this->paginator->getTotalItemCount())?></span>
			</div>
		<?php endif;?>

		<ul class="ynultimatevideo_list_most_items clearfix">
			<?php foreach ($this->paginator as $item) :?>
				<li class="ynultimatevideo_list_most_item">
					<?php echo $this->partial('_playlist_view.tpl', 'ynultimatevideo', array('item' => $item));?>
				</li>
			<?php endforeach;?>
		</ul>
	</div>
<?php else:?>
	<?php if ($this->paging):?>
	<div class="tip">
		<span><?php echo $this->translate('No playlists found.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>
