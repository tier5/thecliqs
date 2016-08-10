<?php 
$this -> headScript() 
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/music-actions.js') 
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/render-music-player.js');
?>

<ul id="ynbusinesspages_ynmusic_newest_songs">
<?php foreach( $this->paginator as $song ): ?>
	<li class="music-item">
		<?php if (!$song->isViewable()) :?>
		<div class="disabled"></div>
		<?php endif;?>
		
		<?php $photo_url = ($song->getPhotoUrl('thumb.profile')) ? $song->getPhotoUrl('thumb.profile') : "application/modules/Ynmusic/externals/images/nophoto_song_thumb_icon.png";?>

		<div class="album-photo song-photo music-photo" style="background-image: url(<?php echo $photo_url; ?>)">
			<div class="play-btn-<?php echo $song->getGuid()?> music-play-btn">
				<a href="javascript:void(0)">
					<i rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i>
				</a>
			</div>
			<div class="icon-playing">
				<img src="application/modules/Ynmusic/externals/images/playing.gif" alt="">
			</div>
		</div>

		<div class="album-info song-info music-info">
			<div class="album-title song-title music-title">
				<?php echo $song;?>
			</div>

			<div class="play-count"><i class="fa fa-headphones"></i><?php echo $song -> play_count;?></div>
		</div>
	</li>
<?php endforeach;?>
</ul>

<script type="text/javascript">
en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});

function addNewPlaylist(ele, guid) {
	var nextEle = ele.getNext();
	if(nextEle.hasClass("ynmusic_active_add_playlist")) {
		//click to close
		nextEle.removeClass("ynmusic_active_add_playlist");
		nextEle.setStyle("display", "none");
	} else {
		//click to open
		nextEle.addClass("ynmusic_active_add_playlist");
		nextEle.setStyle("display", "block");
	}
	$$('.play_list_span').each(function(el){
		if(el === nextEle){
			//do not empty the current box
		} else {
			el.empty();
			el.setStyle("display", "none");
			el.removeClass("ynmusic_active_add_playlist");
		}
	});
	var data = guid;
	var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynmusic_playlist', true);?>';
	var request = new Request.HTML({
        url : url,
        data : {
        	subject: data,
        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var spanEle = nextEle;
            spanEle.innerHTML = responseHTML;
            eval(responseJavaScript);
            
            var popup = spanEle.getParent('.action-pop-up');
            var layout_parent = popup.getParent('.layout_middle');
	    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
	    	var y_position = popup.getPosition(layout_parent).y;
			var p_height = layout_parent.getHeight();
			var c_height = popup.getHeight();
    		if(p_height - y_position < (c_height + 1)) {
    			layout_parent.addClass('popup-padding-bottom');
    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 1 + y_position - p_height)+'px');
			}
        }
    });
    request.send();
}

function addToPlaylist(ele, playlistId, guild) {
	var checked = ele.get('checked');
	var data = guild;
	var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynmusic_playlist', true);?>';
	var request = new Request.JSON({
        url : url,
        data : {
        	subject: data,
        	playlist_id: playlistId,
        	checked: checked,
        },
        onSuccess: function(responseJSON) {
        	if (!responseJSON.status) {
        		ele.set('checked', !checked);
        	}
            var div = ele.getParent('.action-pop-up');
            var notices = div.getElement('.add-to-playlist-notices');
            var notice = new Element('div', {
            	'class' : 'add-to-playlist-notice',
            	text : responseJSON.message
            });
            notices.adopt(notice);
            notice.fade('in');
            (function() {
            	notice.fade('out').get('tween').chain(function() {
            		notice.destroy();
            	});
        	}).delay(3000, notice);
        }
    });
    request.send();
}

window.addEvent('domready', function(){
	$$('a.action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {
		
    	var parent = this.getParent('.show-hide-action');
    	var popup = parent.getElement('.action-pop-up');
    	$$('.action-pop-up').each(function(el) {
    		if (el != popup) el.hide();
    	});
    	
    	if (!popup.isDisplayed()) {
    		var loading = popup.getElement('.add-to-playlist-loading');
    		if (loading) {
	    		var url = loading.get('rel');
	    		loading.show();
	    		var checkbox = popup.getElement('.box-checkbox');
	    		checkbox.hide();
	    		var request = new Request.HTML({
		            url : url,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                elements = Elements.from(responseHTML);
		                if (elements.length > 0) {
		                    checkbox.empty();
		                    checkbox.adopt(elements);
		                    eval(responseJavaScript);
		                    loading.hide();
		                    checkbox.show();
		                    var layout_parent = popup.getParent('.layout_middle');
					    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
					    	var y_position = popup.getPosition(layout_parent).y;
							var p_height = layout_parent.getHeight();
							var c_height = popup.getHeight();
				    		if(p_height - y_position < (c_height + 1)) {
				    			layout_parent.addClass('popup-padding-bottom');
				    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
				    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 1 + y_position - p_height)+'px');
							}
		                }
		            }
		        });
		        request.send();
	       	}
    	}
    	
    	popup.toggle();
    	
    	var layout_parent = popup.getParent('.layout_middle');
    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
    	var y_position = popup.getPosition(layout_parent).y;
		var p_height = layout_parent.getHeight();
		var c_height = popup.getHeight();
    	if (popup.isDisplayed()) {
    		if(p_height - y_position < (c_height + 1)) {
    			layout_parent.addClass('popup-padding-bottom');
    			layout_parent.setStyle('padding-bottom', (c_height + 1 + y_position - p_height)+'px');
			}
			else if (layout_parent.hasClass('popup-padding-bottom')) {
    			layout_parent.setStyle('padding-bottom', '0');
    		}
    	}
    	else {
    		if (layout_parent.hasClass('popup-padding-bottom')) {
    			layout_parent.setStyle('padding-bottom', '0');
    		}
    	}
    });
    
    $$('a.action-link.cancel').removeEvents('click').addEvent('click', function() {
    	var parent = this.getParent('.action-pop-up');
    	if (parent) {
    		parent.hide();
    		var layout_parent = parent.getParent('.layout_middle');
    		if (!layout_parent) layout_parent = popup.getParent('#global_content');
    		if (layout_parent.hasClass('popup-padding-bottom')) {
    			layout_parent.setStyle('padding-bottom', '0');
    		}
    	}
    });
    
    if (typeof addEventForPlayBtn == 'function') { 
	  	addEventForPlayBtn(); 
	}
});
</script>
