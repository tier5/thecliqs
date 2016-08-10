<?php 
$this -> headScript() 
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/music-actions.js') 
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/render-music-player.js');
?>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
    <div class="headline">
		<h2>
			<?php echo $this->business->__toString()." ";
				echo $this->translate('&#187; Social Music');
			?>
		</h2>
    </div>
	</div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="ynmusic_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>

	<div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
		<!-- Menu Bar -->
        <div class="ynbusinesspages-profile-module-header">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-header-right">
                <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Business'), array(
                'class' => 'buttonlink'
                )) ?>

                <?php if ($this->canCreate):?>
                	<?php $label = ($this->type == 'album') ? $this->translate('Create Social Music Album') : $this->translate('Create Social Music Song');?>
                    <?php echo $this->htmlLink(array(
                    'route' => 'ynmusic_song',
                    'action' => 'upload',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                    ), '<i class="fa fa-plus-square"></i>'.$label, array(
                    'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>      

            <div class="ynbusinesspages-profile-header-content">
               <?php if( $this->paginator->getTotalItemCount() > 0 ): 
               $business = $this->business;?>
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
                <?php 
                if ($this->type == 'album') 
                	echo $this-> translate(array("ynmusic_album_count", "Albums", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());	
				else
				 	echo $this-> translate(array("ynmusic_song_count", "Songs", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());
                ;?>
                <?php endif; ?>
            </div>
        </div>        
		
		<!-- Content -->
		<?php 
        if ($this->type == 'album') 
        	echo $this->partial('_album-listing.tpl', 'ynmusic', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'business_id' => $this->business->getIdentity(), 'paging' => true));	
		else
		 	echo $this->partial('_song-listing.tpl', 'ynmusic', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'business_id' => $this->business->getIdentity(), 'paging' => true));
        ;?>
        </div>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>

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
    if (typeof addEventForPlayBtn == 'function') { 
	  	addEventForPlayBtn(); 
	}
	if (typeof addEventsForSocialMusicPopup == 'function') { 
	  	addEventsForSocialMusicPopup(); 
	}
});
</script>