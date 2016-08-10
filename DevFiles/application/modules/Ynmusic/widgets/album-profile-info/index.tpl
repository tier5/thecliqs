
<?php 
	$album = $this -> album;
	$id = $album -> getIdentity();
?>

<div class="music-action-privacy clearfix">
	<div class="album-action music-action">
		<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist() || Engine_Api::_()->ynmusic()->canCreatePlaylist()) :?>
		<div class="list-view-album-action-add-playlist show-hide-action">
			<a class="action-link show-hide-btn" href="javascript:void(0)" title="<?php echo $this->translate('Add to playlist')?>"><i class="fa fa-plus"></i></a>
			<div class="action-pop-up" style="display: none">
				<?php if (Engine_Api::_()->ynmusic()->canAddToPlaylist()): ?>
				<div class='album-action-add-playlist dropdow-action-add-playlist'>
					<span><?php echo $this-> translate('add to') ?></span>
					<?php $url = $this->url(array('action'=>'render-playlist-list', 'subject'=>$album->getGuid()),'ynmusic_playlist', true)?>
					<div rel="<?php echo $url;?>" class="music-loading add-to-playlist-loading" style="display: none;text-align: center">
						<span class="ajax-loading">
					    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
						</span>
					</div>
					<div class="add-to-playlist-notices"></div>
					<div class="box-checkbox">
						<?php echo $this->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $album)); ?>
					</div>
			    </div>
			    <?php endif;?>
			    <?php if (Engine_Api::_()->ynmusic()->canCreatePlaylist()): ?>
				<div class="album-action-dropdown music-action-dropdown">
					<a href="javascript:void(0);" onclick="addNewPlaylist(this, '<?php echo $album->getGuid()?>');" class="action-link add-to-playlist" data="<?php echo $album->getGuid()?>"><i class="fa fa-plus"></i><span class="label"><?php echo $this->translate('Add to new playlist')?></span></a>
					<span class="play_list_span"></span>
					
					<a class="action-link cancel"><i class="fa fa-times"></i><span class="label"><?php echo $this->translate('Cancel')?></span></a>
				</div>
				<?php endif;?>
			</div>
		</div>
		<?php endif;?>
		
		<?php $url = $this->url(array('action' => 'download', 'id' => $album -> getIdentity()), 'ynmusic_album', true);?>	
		<a class="action-link download smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>" title="<?php echo $this->translate('Download')?>"><i class="fa fa-download"></i></a>
		
		<?php if ($this->viewer()->getIdentity()): ?>
		<?php $url = $this -> url(array(
	        'module' => 'activity',
	        'controller' => 'index',
	        'action' => 'share',
	        'type' => 'ynmusic_album',
	        'id' => $album->getIdentity(),
	        'format' => 'smoothbox'),'default', true) ?>
		<a class="action-link share smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>" title="<?php echo $this->translate('Share')?>"><i class="fa fa-share-alt"></i></a>
		<?php endif;?>
		
		<?php if ($album->isEditable()) :?>
		<?php $url = $this->url(array('action' => 'edit', 'album_id' => $album -> getIdentity()), 'ynmusic_album', true);?>	
		<a class="action-link edit" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>" title="<?php echo $this->translate('Edit')?>"><i class="fa fa-pencil-square-o"></i></a>
		<?php endif;?>
		
		<?php if ($album->isDeletable()) :?>
		<?php $url = $this->url(array('action' => 'delete', 'id' => $album -> getIdentity(), 'redirect' => true), 'ynmusic_album', true);?>
		<a class="action-link delete smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>" title="<?php echo $this->translate('Delete')?>"><i class="fa fa-trash"></i></a>
		<?php endif;?>
		
		<?php if ($this->viewer()->getIdentity() && !$album->isOwner($this->viewer())):?>
		<?php $url = $this->url(array(
	        'module' => 'core',
	        'controller' => 'report',
	        'action' => 'create',
	        'subject' => $album->getGuid(),
	        'format' => 'smoothbox'),'default', true)?>
	    <a class="action-link report smoothbox" href="<?php echo $url?>" rel="<?php echo $album->getIdentity()?>" title="<?php echo $this->translate('Report')?>"><i class="fa fa-ban"></i></a>
		<?php endif;?>
	</div>
	
	<div class="album-creation-date_privacy music-creation-date_privacy">
		<span class="play-count"><i class="fa fa-headphones"></i><?php echo $album -> play_count;?></span>
		<span class="creation-date"><?php echo  $this -> translate('Posted on %s',date('M d, Y', $album -> getCreationDate() -> getTimestamp()));?></span>
		<span class="privacy">
			<?php $privacy = Engine_Api::_()->ynmusic()->getItemViewPrivacy($album);?>
			<p class="<?php echo $privacy['role'] ?>"><?php echo $privacy['label']?></p>
		</span>
	</div>
</div>

<?php if (!empty($album->description)):?>
<div class="album-description music-description">
<?php echo $this->viewMore($album -> getDescription(), 1024);?>
</div>
<?php endif;?>

<?php $artists = $album->getArtists();?>
<?php if (!empty($artists)) :?>
<div class="album-artists music-artists">
	<span class="label"><i class="fa fa-microphone"></i><?php echo $this->translate('Artists')?>:</span>
	<span class="value"><?php echo implode(', ', $artists)?></span>
</div>
<?php endif;?>

<?php $genres = $album->getGenres();?>
<?php if (!empty($genres)) :?>
<div class="album-genres music-genres">
	<span class="label"><i class="fa fa-folder-open"></i><?php echo $this->translate('Genres')?>:</span>
	<span class="value"><?php echo implode(', ', $genres)?></span>
</div>
<?php endif;?>

<?php $tags = Engine_Api::_()->ynmusic()->getTagArray($album);?>
<?php if (!empty($tags)):?>
<div class="album-tags music-tags">
	<span class="label"><i class="fa fa-tags"></i><?php echo $this->translate('Tags')?>:</span>
	<span class="value">
		<ul class="tag-list">
			<?php foreach ($tags as $tag):?>
			<li class="tag-item"><a href="<?php echo $this->url(array('tag' => $tag->tag_id), 'ynmusic_album', true)?>"><?php echo $tag->text?></a></li>
			<?php endforeach;?>
		</ul>
	</span>
</div>
<?php endif;?>

<div class="music-addthis">
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynmusic_addthis_pubid', 'younet');?>" async="async"></script>
<div class="addthis_sharing_toolbox"></div>
</div>

<?php $songs = $album -> getSongs(); ?>
<?php if (count($songs)) :?>
<div class="album-songs-detail music-songs" id="music-songs-detail">
	<?php echo $this->partial('_song-list.tpl', 'ynmusic', array('songs' => $songs, 'detail' => true, 'parent' => $album));?>
</div>
<?php endif;?>	


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
	
	function redirectToManagePage() {
		setTimeout(function() {
      		Smoothbox.close();
			window.location.href = '<?php echo $this->url(array('action'=>'manage'), 'ynmusic_album', true)?>';
    	}, 500);
	}
</script>