<?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
	$this->headScript()
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')	
		->appendScript('jQuery.noConflict();')
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')	
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')	
		->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js')
		
        ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery-ui-1.10.4.min.js')
        ->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.form.min.js');	

	$album = $this -> album;
	$id = $album -> getIdentity();

	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($album->cover_id)->current();
	$coverPhotoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/album_cover_default.png';
	$hasCover = false;
	if($coverFile){
		$coverPhotoUrl = $coverFile->map();
		$hasCover = true;
	}	
	
	$photoUrl = $album->getPhotoUrl();
	if (!$photoUrl)	 $photoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/nophoto_album_thumb_profile.png';
?>

<div id="music-detail-<?php echo $album->getGuid()?>" rel="<?php echo $album->getGuid()?>" class="music-item music-detail album-cover music-profile-cover" style="background-image: url('<?php echo $coverPhotoUrl?>')">
	<img src="<?php echo $coverPhotoUrl ?>" alt="" class="album-bg-profile-cover music-bg-profile-cover" style="<?php echo 'top: '.$album->cover_top.'px'?>" />
	<img src="<?php echo $coverPhotoUrl ?>" alt="" class="album-bg-profile-cover music-bg-profile-cover reposition-cover" style="<?php echo 'top: '.$album->cover_top.'px'?>" />
	<div class="profile-cover-bg-gradient"></div>
	
	<div id="album-cover-loading" class="music-loading" style="display: none;">
		<span class="upload-loading" style="text-align: center">
	    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
		</span>
	</div>
	
	<?php if ($album->isEditable()) :?>
	<div class="change-cover-btn">
		<a id="change-cover-btn" href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Change cover')?></a>
		<input class="music-cover-upload" id="archievement-cover-upload" type="file" accept="image/*" style="display: none"/>
	</div>
	
	<div class="reposition-cover-btn" <?php if (!$hasCover) echo 'style="display:none;"'?>>
		<a id="reposition-cover-btn" href="javascript:void(0)" onclick="repositionCover();"><i class="fa fa-arrows-v"></i><?php echo $this->translate('Reposition cover')?></a>
		<div class="reposition-cover-buttons" style="display: none;">
            <a href="javascript:void(0)" onclick="saveReposition();"><?php echo $this->translate('Save Position')?></a>
            <a href="javascript:void(0)" onclick="cancelReposition();"><?php echo $this->translate('Cancel')?></a>
            <input class="cover-position" name="pos" value="<?php echo $album->cover_top?>" type="hidden">
        </div>
	</div>
	<?php endif;?>
	
	<div class="album-box-profile-info music-box-profile-info">

		<div class="album-title-released-date">
			<div id="album-title" class="music-profile-title">
				<?php echo $album;?>
			</div>
			
			<?php if ($album ->released_date) :?>
			<div id="album-released-date" class="music-released-date">
				<?php echo date('M d, Y', $album -> getReleasedDate() -> getTimestamp());?>
			</div>
			<?php endif;?>
		</div>

		<div class="box-time-image-song">
			<div class="playing-song-title"></div>
			<?php $firstSong = $album->getFirstSong();?>
			<?php if ($firstSong) : ?>
			<div class="time-song">
				<div class="duration-time"><?php echo date('i:s', $firstSong->duration)?></div>
			</div>
			<?php 
				$noPlayImg = $firstSong->getNoPlayImage();
				$playImg = $firstSong->getPlayImage();
			?>
			<?php if ($noPlayImg && $playImg) :?>
			<div class="image-song">
				<div class="no-play-div" style="background-image: url('<?php echo $noPlayImg?>');">
					<div class="progress-bar no-play"></div>
				</div>
				<div class="play-div" style="width: 0; overflow: hidden; background-image: url('<?php echo $playImg?>');">
					<div class="progress-bar play">
					</div>
				</div>
				<div class="drag-btn"></div>
				<div class="time-hover"></div>
			</div>
			<?php endif;?>
			<?php endif;?>
		</div>
			
		<div class="music-info-bottom">
			<?php if ($firstSong) : ?>
			<div class="play-btn-<?php echo $album->getGuid()?> music-play-btn parent">
				<a href="javascript:void(0)">
					<i rel="<?php echo $album->getGuid()?>" class="fa fa-play"></i>
				</a>
			</div>
			<?php endif;?>
		</div>
	</div>
	
	<div id="album-photo" class="music-profile-photo">
		<div class="change-photo-wrapper" style="background-image: url('<?php echo $photoUrl?>')">
			<div id="album-photo-loading" class="music-loading" style="display: none;">
				<span class="upload-loading" style="text-align: center">
			    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
				</span>
			</div>
			<?php if ($album->isEditable()) :?>
			<div class="change-photo-btn">
				<a id="change-photo-btn" href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Change image')?></a>
				<input class="music-photo-upload" id="archievement-photo-upload" type="file" accept="image/*" style="display: none"/>
			</div>
			<?php endif;?>
		</div>
		<div id="album-songs-info" class="music-songs-info clearfix">
			<div class="song-count">
				<span><?php echo $album->getCountSongs()?></span>
				<?php echo $this->translate(array('ynmusic_song_count', 'Songs', $album->getCountSongs()), $album->getCountSongs())?>
			</div>
			
			<div class="album-duration music-duration">
				<?php echo $album -> getDuration();?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var cover_top = <?php echo $album->cover_top?>;

function repositionCover() {
    jQuery('.reposition-cover').addClass('active');
    jQuery('.music-profile-cover').addClass('nopadding');
    jQuery('#reposition-cover-btn').hide();
    jQuery('.reposition-cover-buttons').show();
    jQuery('.reposition-cover')
    .css('cursor', 's-resize')
    .draggable({
        scroll: false,
        axis: "y",
        cursor: "s-resize",
        drag: function (event, ui) {
            y1 = jQuery('.music-profile-cover').height();
            y2 = jQuery('.reposition-cover').height();
            console.log(y1);
            console.log(y2);
            if (ui.position.top >= 0) {
                ui.position.top = 0;
            }
            else
            if (ui.position.top <= (y1-y2)) {
                ui.position.top = y1-y2;
            }
        },
        
        stop: function(event, ui) {
            jQuery('input.cover-position').val(ui.position.top);
        }
    });
}

function saveReposition() {
    if (jQuery('input.cover-position').length == 1) {
        posY = jQuery('input.cover-position').val();
        new Request.JSON({
            'url': '<?php echo $this->url(array('action'=>'reposition', 'subject'=>$album->getGuid()),'ynmusic_general', true)?>',
            'method': 'post',
            'data' : {
                'position' : posY
            },
            'onSuccess': function(responseJSON, responseText) {
                if (responseJSON.status == true) {
                    cover_top = posY;
                    jQuery('.music-bg-profile-cover').css('top', posY+'px');
                    jQuery('.music-profile-cover').removeClass('nopadding');
                    jQuery('.reposition-cover').removeClass('active');
                    jQuery('.reposition-cover-buttons').hide();
                    jQuery('#reposition-cover-btn').show();
                }
                else {
                    alert(responseJSON.message);
                }            
            }
        }).send();
    }
}

function cancelReposition() {
	jQuery('.music-profile-cover').removeClass('nopadding');
    jQuery('.reposition-cover').removeClass('active');
    jQuery('.reposition-cover').css('top', cover_top+'px');
    jQuery('#reposition-cover-btn').show();
    jQuery('.reposition-cover-buttons').hide();
    jQuery('input.cover-position').val(cover_top);
}
</script>

<script type="text/javascript">
	window.addEvent('domready', function() {
 		$$('.drag-btn').each(function(el) {
 			var parent = el.getParent('.image-song');
 			var width = parent.getSize().x;
 			new Drag(el, {
 				'limit': {
 					x: [0, width]
 				}, 
		    	'modifiers': {'x': 'left', 'y': null}, 
		    	onDrag: function(obj) {
		    		parent.addClass('on-drag');
					var play_div = parent.getElement('.play-div');
					var left = obj.getCoordinates(parent).left;
					left = left + 8;
					var width = parent.getSize().x;
					play_div.setStyle('width', left+'px');
		    	},
		    	onComplete: function(obj) {
		    		parent.removeClass('on-drag');
					var left = obj.getCoordinates(parent).left;
					percent = left*100/width;
					updatePlayerTime(percent);
		    	}
			});
 		});

		$$('.image-song').addEvent('mousemove', function(event) {
			var left = this.getPosition().x;
			var width = this.getSize().x;
			var pos = event.page.x - left;
			var percent = pos*100/width;
			var player = $('ynmusic-player');
			var hoverTime = this.getElement('.time-hover');
			if (player && hoverTime && player.duration) {
				var duration = player.duration;
				var time = percent*duration/100;
				var s = parseInt(time % 60);
	    		var m = parseInt((time / 60) % 60);
	    		if (s < 10) s = '0'+s;
	    		if (m < 10) m = '0'+m;
				hoverTime.innerHTML = m+':'+s;
				hoverTime.setStyle('margin-left', pos);
				hoverTime.show();
			}
			else {
				hoverTime.hide();
			}
		});
		
		$$('.image-song').addEvent('mouseleave', function(event) {
			var hoverTime = this.getElement('.time-hover');
			if (hoverTime) {
				hoverTime.hide();
			}
		});
		
		$$('.image-song').addEvent('click', function(event) {
			var left = this.getPosition().x;
			var width = this.getSize().x;
			var pos = event.page.x - left;
			var percent = pos*100/width;
			updatePlayerTime(percent);
		});
		
 		if ($('change-cover-btn')) {
 			$('change-cover-btn').addEvent('click', function() {
 				$('archievement-cover-upload').click();
 			})
 		}
 		
 		if ($('change-photo-btn')) {
 			$('change-photo-btn').addEvent('click', function() {
 				$('archievement-photo-upload').click();
 			})
 		}
 		
 		var url_cover = '<?php echo $this->url(array(
    		'action' => 'upload-photo',
    		'item_id' => $album -> getIdentity(),
    		'item_type' => $album -> getType(),
    		'upload_type' => 'cover_id',
		), 'ynmusic_general', true)?>';
		
 		if ($('archievement-cover-upload')) {
 			var parent_cover = $('archievement-cover-upload').getParent('.music-profile-cover');
 			var loading_cover = parent_cover.getElement('.music-loading');
 			var button_cover = parent_cover.getElement('.change-cover-btn');
		    jQuery('#archievement-cover-upload').fileupload({
		        url: url_cover,
		        dataType: 'json',
		        done: function (e, data) {
		            jQuery.each(data.result.files, function (index, file) {
		        		loading_cover.hide();
		        		button_cover.show();
		                if(file.status) {
		                	parent_cover.setStyle('background-image', 'url('+file.photo_url+')');
		                	parent_cover.getElements('img.music-bg-profile-cover').each(function(el) {
		                		el.set('src', file.photo_url);
		                		el.setStyle('top', 0);
		                		parent_cover.getElement('.reposition-cover-btn').show();
		                		cover_top = 0;
		                	});
		                	
		                }
		                else {
		                	alert('<?php echo $this->translate('Upload fail: ')?>'+file.error);
		                }
		            });
		        },
		        progressall: function (e, data) {
		        	button_cover.hide();
		        	loading_cover.show();
		      	},
		    }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
        }
        
        var url_photo = '<?php echo $this->url(array(
    		'action' => 'upload-photo',
    		'item_id' => $album -> getIdentity(),
    		'item_type' => $album -> getType(),
    		'upload_type' => 'photo_id',
		), 'ynmusic_general', true)?>';
		
        if ($('archievement-photo-upload')) {
 			var parent = $('archievement-photo-upload').getParent('.change-photo-wrapper');
 			var loading = parent.getElement('.music-loading');
 			var button = parent.getElement('.change-photo-btn');
		    jQuery('#archievement-photo-upload').fileupload({
		        url: url_photo,
		        dataType: 'json',
		        done: function (e, data) {
		            jQuery.each(data.result.files, function (index, file) {
		        		loading.hide();
		        		button.show();
		                if(file.status) {
		                	parent.setStyle('background-image', 'url('+file.photo_url+')');
		                }
		                else {
		                	alert('<?php echo $this->translate('Upload fail: ')?>'+file.error);
		                }
		            });
		        },
		        progressall: function (e, data) {
		        	button.hide();
		        	loading.show();
		      	},
		    }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
        }
 	});
</script>