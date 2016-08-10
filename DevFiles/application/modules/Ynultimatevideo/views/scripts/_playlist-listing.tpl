<?php if ($this->paginator->getTotalItemCount() > 0) :?>
	<div id="ynultimatevideo-playlist-listing">
		<ul class="ynultimatevideo_list_most_items clearfix">
			<?php foreach ($this->paginator as $item) :?>
				<li class="ynultimatevideo_list_most_item">
					<?php echo $this->partial('_playlist_view.tpl', 'ynultimatevideo', array('item' => $item));?>
				</li>
			<?php endforeach;?>
		</ul>

		<!-- paginator -->
		<?php if ($this->paging): ?>
			<div>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues
			)); ?>
			</div>
		<?php endif; ?>
	</div>
<?php else:?>
	<?php if ($this->paging):?>
	<div class="tip">
		<span><?php echo $this->translate('No playlists found.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>
