<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')	
	->appendScript('jQuery.noConflict();')
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')	
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')	
	->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');	
?>
<?php
	$album = $this -> album;
	$songs  = $album -> getSongs();
?>
<input name="order" type="hidden" id="songs-order" value=""/>
<input name="deleted" type="hidden" id="songs-deleted" value=""/>
<div id="album-song-items">
<?php foreach($songs as $song) :?>
		<div id="<?php echo $song -> getIdentity();?>" class="song-item">
			<?php if (!$song->isViewable()) :?>
			<div class="disabled"></div>
			<?php endif;?>
			<span class="play-btn-<?php echo $song->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $song->getGuid()?>" class="fa fa-play"></i></a></span>
			<span class="song-title"><?php echo $song;?></span>
			<div class="song-btn">
				<span class="music-song-move album-song-move"><i class="fa fa-arrows"></i></span>
				<?php if ($song->isEditable()) :?>
				<span data-id="<?php echo $song -> getIdentity();?>" class="music-song-edit album-song-edit ynmusic-song-edit"><i class="fa fa-pencil"></i></span>
				<?php endif;?>
				<?php if ($song->isDeletable()) :?>
				<span class="music-song-remove album-song-remove"><i class="fa fa-times"></i></span>
				<?php endif;?>
			</div>
			<?php if ($song->isEditable()) :?>
			<span class="ynmusic-edit-form-section" id="edit-form-<?php echo $song -> getIdentity();?>"></span>
			<?php endif;?>
		</div>
<?php endforeach;?>
</div>


<script type="text/javascript">
	
	function clearForm(songID) {
		$('edit-form-'+songID).innerHTML = "";
	}
	
	function submitForm(songID) {
		var data = {
        	'song_id': songID,
            'title': $('title_'+songID).get('value'),
            'description': $('description_'+songID).get('value'),
            'genre_ids': $('genre_ids_'+songID).get('value'),
            'artist_ids': $('artist_ids_'+songID).get('value'),
            'tags': $('tags_'+songID).get('value'),
            'photo_id': $('photo_id_'+songID).get('value'),
            'cover_id': $('cover_id_'+songID).get('value'),
            'downloadable': ($('downloadable_'+songID).checked) ? 1 : 0,
            'auth_view': $('auth_view_'+songID).get('value'),
            'auth_comment':	$('auth_comment_'+songID).get('value'),		
      	};
      	if ($('album_id_'+songID)) {
      		data['album_id'] = $('album_id_'+songID).get('value');
      	}
		var url = "<?php echo $this -> url(array('action' => 'edit-song'), 'ynmusic_song', true);?>";
			new Request.JSON({
		        url: url,
		        method: 'post',
		        data: data,
		        'onSuccess' : function(responseJSON, responseText)
		        {
		        	if(responseJSON.error_code ==  0)
		        	{
		        		var ele_divs = $('album-song-items').getChildren('#'+songID);
						if (ele_divs.length > 0) {
							ele_divs[0].getElement('span.song-title a').set('text', $('title_'+songID).get('value'));
						}
		        		if(responseJSON.move == 0) {
			        		var htmlText = "<div class='tip'><span><?php echo $this -> translate('Your changes have been saved.');?></span></div>"
			        		$('edit-form-'+songID).innerHTML = htmlText;
		        		} else {
		        			var htmlText = "<div class='tip'><span><?php echo $this -> translate('Your song have been moved.');?></span></div>"
			        		$('album-song-items').getElement('#'+songID).innerHTML = htmlText;
		        		}
					}
				}
		}).send();
	}

    en4.core.runonce.add(function(){
        new Sortables('album-song-items', {
            contrain: false,
            clone: true,
            handle: 'span.album-song-move',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                var order = this.serialize().toString();
                $('songs-order').set('value', order);
            }
        });
    });
    
    window.addEvent('domready', function() {
	    $$('.ynmusic-song-edit').addEvent('click', function(){
	    		
	    		$$('.ynmusic-edit-form-section').each(function(el) {
	    			el.innerHTML = "";
				});
				
    			var song_id = this.get('data-id');
    			
	    		$$('.open-content').removeClass('open-content');
	    		this.addClass('open-content');
	    		
	    	 	var url = '<?php echo $this->url(array('action' => 'get-form-edit'), 'ynmusic_song', true) ?>';
				var request = new Request.HTML({
			      url : url,
			      data : {
			        'type' : 'ajax',
			        'song_id': song_id,
			      },
			      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			      		$('edit-form-'+song_id).innerHTML = responseHTML;
			      		eval(responseJavaScript);
			      }
			    });
				request.send();
	    });
	    
	    $$('.album-song-remove').addEvent('click', function() {
    		var parent = this.getParent('.song-item');
    		var id = parent.get('id');
    		var ids = $('songs-deleted').get('value');
    		if (ids == '') ids = id;
    		else ids = ids+','+id;	
    		$('songs-deleted').set('value', ids);
    		parent.destroy();
    		
    		if ($$('#edit_songs-wrapper .song-item').length == 0) {
    			$('edit_songs_header-wrapper').hide();
    			$('edit_songs-wrapper').hide();
    		}
    	});
    });
</script>

