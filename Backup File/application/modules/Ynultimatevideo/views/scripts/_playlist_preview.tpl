<div class="form-wrapper">
	<div class="form-label">
		<?php echo $this->translate('Viewing Mode')?>
	</div>
	<div class="form-element">
		<div>
			<label for="playlist_preview_0">
				<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynultimatevideo/externals/images/playlist_edit_preview_listing.png" />
			</label>
			<input id='playlist_preview_0' <?php if ($this->view_mode == 0) echo "checked='true'" ?> type='radio' name='view_mode' value ='0'>
		</div>
		<div>
			<label for="playlist_preview_1">
				<img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynultimatevideo/externals/images/playlist_edit_preview_slideshow.png" />
			</label>
			<input id='playlist_preview_1' <?php if ($this->view_mode == 1) echo "checked='true'" ?> type='radio' name='view_mode' value ='1'>
		</div>
	</div>
</div>