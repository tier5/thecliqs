<?php if ($this->paginator->getTotalItemCount() > 0) :?>
	<div id="ynvideochannel-playlist-listing">
		<ul class="ynvideochannel_playlist_list ynvideochannel_playlist_profile clearfix">
			<?php foreach ($this->paginator as $item) :?>
				<?php echo $this->partial('_playlist_item.tpl', 'ynvideochannel', array('item' => $item));?>
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


<script type="text/javascript">
    ynvideochannelPlaylistOptions();
</script>