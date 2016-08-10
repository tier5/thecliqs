<div id="ynmusic-songs-listing-widget" class="music-listing">

    <div class="ynmusic-block-count-mode-view <?php if (empty($this->mode_enabled)) echo 'not-mode-view'?>">

    	<div id="ynmusic-total-item-count"><?php echo $this->translate(array('ynmusic_song_count_num_ucf', '%s Songs', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></div>
    	
    	<div id="ynmusic-view-mode-<?php echo $this->identity;?>" class="ynmusic-modeview-button">
    		<?php if(in_array('list', $this -> mode_enabled)):?>
            <span class="" rel="ynmusic_list-view" title="<?php echo $this->translate('List View')?>"><i class="fa fa-th-list"></i></span>
            <?php endif;?>
            <?php if(in_array('grid', $this -> mode_enabled)):?>
            <span class="" rel="ynmusic_grid-view" title="<?php echo $this->translate('Grid View')?>"><i class="fa fa-th"></i></span>
            <?php endif;?>
    	</div>
    </div>
	
	<div id="ynmusic-listing-content-<?php echo $this ->identity;?>" class="ynmusic-listing-content">
		<?php echo $this->partial('_song-listing.tpl', 'ynmusic', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
	</div>
</div>

<script type="text/javascript">
en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});
var mode_enabled<?php echo $this->identity?> = [];
<?php foreach ($this->mode_enabled as $mode) :?>
mode_enabled<?php echo $this->identity?>.push('ynmusic_<?php echo $mode?>-view');
<?php endforeach;?>
if (mode_enabled<?php echo $this->identity?>.length == 0) {
	mode_enabled<?php echo $this->identity?>.push('ynmusic_list-view');
}

window.addEvent('domready', function(){
	var myCookieViewMode = getCookie('ynmusic-listing-modeview-<?php echo $this -> identity; ?>');
    if ( myCookieViewMode == '') {
        myCookieViewMode = '<?php echo $this->class_mode?>';
    }
    
    if (mode_enabled<?php echo $this->identity?>.indexOf(myCookieViewMode) == -1) {
    	myCookieViewMode = mode_enabled<?php echo $this->identity?>[0];
    }
    
    $$('#ynmusic-view-mode-<?php echo $this -> identity;?> > span[rel='+myCookieViewMode+']').addClass('active');
    $$('#ynmusic-listing-content-<?php echo $this -> identity; ?>').addClass(myCookieViewMode);
    
    // Set click viewMode
    $$('#ynmusic-view-mode-<?php echo $this -> identity;?> > span').addEvent('click', function(){
        var viewmode = this.get('rel');
        var content = $('ynmusic-listing-content-<?php echo $this -> identity; ?>');

        setCookie('ynmusic-listing-modeview-<?php echo $this -> identity; ?>', viewmode, 1);

        // set class active
        $$('#ynmusic-view-mode-<?php echo $this->identity;?> > span').removeClass('active');
        this.addClass('active');

        content
            .removeClass('ynmusic_list-view')
            .removeClass('ynmusic_grid-view');

        content.addClass( viewmode );
    });
});

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
    		if(p_height - y_position < (c_height + 21)) {
    			layout_parent.addClass('popup-padding-bottom');
    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
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
</script>