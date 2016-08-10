<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php echo $this->form->render($this);?>

<script>
window.addEvent('domready', function() {
	if ($('ynultimatevideo_playlist_create')) {
		$('ynultimatevideo_playlist_create').addEvent('submit', function() {
			var btn = this.getElement('#submit-wrapper');
			if (btn) btn.hide();
			return true;
		});
	}
});

var previewMode = function() {
	var ele = $('view_mode');
	var photoUrl = "<?php echo $this->escape($this->layout()->staticBaseUrl); ?>";
	// preview slideshow
	if (ele.value == '0') {
		photoUrl += 'application/modules/Ynultimatevideo/externals/images/playlist_edit_preview_listing.png';
	} else {
		photoUrl += 'application/modules/Ynultimatevideo/externals/images/playlist_edit_preview_slideshow.png';
	}
	var playlistPreview = new Element('div', {html:
	'<div class="preview-overlay">'
	+'<img src="'
	+ photoUrl
	+ '">'
	+'</div>'
	});

	playlistPreview.addClass('preview-popup');
	$$('.preview-popup').dispose();
	playlistPreview.inject( $$('body')[0] );

	$$('.preview-popup').addEvent('click', function(){
		$$('.preview-popup').dispose();
	});
}
</script>