<?php 
	$playlist = $this -> playlist;
	$id = $playlist -> getIdentity();
?>

<div class="music-action-privacy clearfix">
	<div class="playlist-action music-action">
		<?php if ($this->viewer()->getIdentity()) :?>
		<?php $url = $this -> url(array(
	        'module' => 'activity',
	        'controller' => 'index',
	        'action' => 'share',
	        'type' => 'ynmusic_playlist',
	        'id' => $playlist->getIdentity(),
	        'format' => 'smoothbox'),'default', true) ?>
		<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>" title="<?php echo $this->translate('Share')?>"><i class="fa fa-share-alt"></i></a>
		<?php endif;?>
		
		<?php if ($playlist->isEditable()) :?>
		<?php $url = $this->url(array('action' => 'edit', 'id' => $playlist -> getIdentity()), 'ynmusic_playlist', true);?>
		<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>" title="<?php echo $this->translate('Edit')?>"><i class="fa fa-pencil-square-o"></i></a>
		<?php endif;?>
		
		<?php if ($playlist->isDeletable()) :?>
		<?php $url = $this->url(array('action' => 'delete', 'id' => $playlist -> getIdentity(), 'redirect' => true), 'ynmusic_playlist', true);?>
		<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>" title="<?php echo $this->translate('Delete')?>"><i class="fa fa-trash"></i></a>
		<?php endif;?>
		
		<?php if ($this->viewer()->getIdentity() && !$playlist->isOwner($this->viewer())):?>
		<?php $url = $this->url(array(
	        'module' => 'core',
	        'controller' => 'report',
	        'action' => 'create',
	        'subject' => $playlist->getGuid(),
	        'format' => 'smoothbox'),'default', true)?>
	    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $playlist->getIdentity()?>" title="<?php echo $this->translate('Report')?>"><i class="fa fa-ban"></i></a>
		<?php endif;?>
	</div>

	<div class="playlist-creation-date_privacy music-creation-date_privacy">
		<span class="play-count"><i class="fa fa-headphones"></i><?php echo $playlist -> play_count;?></span>
		<span class="creation-date"><?php echo  $this -> translate('Posted on %s',date('M d, Y', $playlist -> getCreationDate() -> getTimestamp()));?></span>
		<span class="privacy">
			<?php $privacy = Engine_Api::_()->ynmusic()->getItemViewPrivacy($playlist);?>
			<p class="<?php echo $privacy['role'] ?>"><?php echo $privacy['label']?></p>
		</span>
	</div>
</div>

<?php if (!empty($playlist->description)):?>
<div class="playlist-description music-description">
<?php echo $this->viewMore($playlist -> getDescription(), 1024);?>

</div>
<?php endif;?>

<?php $genres = $playlist->getGenres();?>
<?php if (!empty($genres)) :?>
<div class="playlist-genres music-genres">
	<span class="label"><i class="fa fa-folder-open"></i><?php echo $this->translate('Genres')?>:</span>
	<span class="value"><?php echo implode(', ', $genres)?></span>
</div>
<?php endif;?>

<?php $tags = Engine_Api::_()->ynmusic()->getTagArray($playlist);?>
<?php if (!empty($tags)):?>
<div class="playlist-tags music-tags">
	<span class="label"><i class="fa fa-tags"></i><?php echo $this->translate('Tags')?>:</span>
	<span class="value">
		<ul class="tag-list">
			<?php foreach ($tags as $tag):?>
			<li class="tag-item"><a href="<?php echo $this->url(array('tag' => $tag->tag_id), 'ynmusic_playlist', true)?>"><?php echo $tag->text?></a></li>
			<?php endforeach;?>
		</ul>
	</span>
</div>
<?php endif;?>

<div class="music-addthis">
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynmusic_addthis_pubid', 'younet');?>" async="async"></script>
<div class="addthis_sharing_toolbox"></div>
</div>

<?php $songs = $playlist -> getSongs(); ?>
<?php if (count($songs)) :?>
<div class="playlist-songs-detail music-songs" id="music-songs-detail">
	<?php echo $this->partial('_song-list.tpl', 'ynmusic', array('songs' => $songs, 'detail' => true, 'parent' => $playlist));?>
</div>
<?php endif;?>	

<script type="text/javascript">
	en4.core.language.addData({'Like': ' <?php echo $this->translate('Like')?>'});
	en4.core.language.addData({'Unlike': ' <?php echo $this->translate('Unlike')?>'});
	
	window.addEvent('domready', function(){
		<?php foreach($songs as $song) :?>
		$$('#add-to-playlist-<?php echo $song->getGuid()?>').addEvent('click', function(){
			$$('.play_list_span').each(function(el){
				el.empty();
			});
			var data = '<?php echo $song->getGuid()?>';
			var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynmusic_playlist', true);?>';
			var request = new Request.HTML({
	            url : url,
	            data : {
	            	subject: data,
	            },
	            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
	                $('play_list_'+ data).innerHTML = responseHTML;
	                eval(responseJavaScript);
	            }
	        });
	        request.send();
		});
		<?php endforeach;?>
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
	        	}).delay(2000, notice);
	        }
	    });
	    request.send();
	}
	
	function redirectToManagePage() {
		setTimeout(function() {
      		Smoothbox.close();
			window.location.href = '<?php echo $this->url(array('action'=>'manage'), 'ynmusic_playlist', true)?>';
    	}, 500);
	}
</script>