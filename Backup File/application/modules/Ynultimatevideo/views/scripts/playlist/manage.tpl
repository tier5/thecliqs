<div class="ynultimatevideo_count_videos">
	<div class="ynultimatevideo_count_videos">
		<i class="fa fa-file-text"></i>
		<?php echo $this->paginator->getTotalItemCount()?>
		<?php echo $this->translate('Playlists') ?>
	</div>
</div>

<div class="ynultimatevideo-listing-content ynultimatevideo_list-view">
	<?php echo $this->partial('_playlist-listing.tpl', 'ynultimatevideo', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
</div>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('.ynultimatevideo_main_manage').getParent().addClass('active');
	});
	
    $$('.ynultimatevideo_options_btn').addEvent('click',function(){
        this.getParent('.ynultimatevideo_options_block').getElement('.ynultimatevideo_options').toggle();
    })
</script>