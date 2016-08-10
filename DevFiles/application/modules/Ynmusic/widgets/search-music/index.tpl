<?php echo $this->form->render($this) ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$$('.layout_ynmusic_search_music #type').addEvent('change', function() {
			var value = this.value;
			if (value == 'artist') {
				$$('.layout_ynmusic_search_music #owner-wrapper').hide();
				$$('.layout_ynmusic_search_music #created_from-wrapper').hide();
				$$('.layout_ynmusic_search_music #created_to-wrapper').hide();
				
				$$('.layout_ynmusic_search_music #browse_by option').each(function(el) {
					if (el.value != 'a_z' && el.value != 'z_a') {
						el.hide();
					}
				});
				
				var browseBy = $$('.layout_ynmusic_search_music').getElement('#browse_by');
				if (browseBy && browseBy.get('value') != 'a_z' && browseBy.get('value') != 'z_a') {
					browseBy.set('value', 'a_z');
				}
				
			}
			else {
				$$('.layout_ynmusic_search_music #owner-wrapper').show();
				$$('.layout_ynmusic_search_music #created_from-wrapper').show();
				$$('.layout_ynmusic_search_music #created_to-wrapper').show();
				$$('.layout_ynmusic_search_music #browse_by option').show();
			}
		});
		
		$$('.layout_ynmusic_search_music #type').fireEvent('change');
	});
</script>
