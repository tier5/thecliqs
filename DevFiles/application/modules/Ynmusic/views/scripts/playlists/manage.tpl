<div class="ynmusic-block-count-mode-view not-mode-view">
	<div id="ynmusic-total-item-count">
		<?php echo $this->translate(array('ynmusic_playlist_count_num_ucf', '%s Playlists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?>
	</div>
</div>
	
<div id="ynmusic-manage-playlist" class="ynmusic-listing-content manage-music">
	<?php echo $this->partial('_playlist-listing.tpl', 'ynmusic', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
</div>

<script type="text/javascript">
en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});		
</script>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('.ynmusic_main_manage_albums').getParent().addClass('active');
	});
</script>