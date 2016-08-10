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
		
	$artist = $this -> artist;
	$id = $artist -> getIdentity();

	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($artist->cover_id)->current();
	$coverPhotoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/artist_cover_default.png';
	$hasCover = false;
	if($coverFile){
		$coverPhotoUrl = $coverFile->map();
		$hasCover = true;
	}	
	
	$photoUrl = $artist->getPhotoUrl();
	if (!$photoUrl)	 $photoUrl = $this->baseUrl().'/application/modules/Ynmusic/externals/images/nophoto_artist_thumb_profile.png';
?>

<div id="music-detail-<?php echo $artist->getGuid()?>" rel="<?php echo $artist->getGuid()?>" class="music-item music-detail artist-cover music-profile-cover" style="background-image: url(<?php echo $coverPhotoUrl ?>)">
	<img src="<?php echo $coverPhotoUrl ?>" alt="" class="playlist-bg-profile-cover music-bg-profile-cover" style="<?php echo 'top: '.$artist->cover_top.'px'?>" />
	<img src="<?php echo $coverPhotoUrl ?>" alt="" class="playlist-bg-profile-cover music-bg-profile-cover reposition-cover" style="<?php echo 'top: '.$artist->cover_top.'px'?>" />
	
	<div class="profile-cover-bg-gradient"></div>
	
	<div id="playlist-cover-loading" class="music-loading" style="display: none;">
		<span class="upload-loading" style="text-align: center">
	    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
		</span>
	</div>
	
	<?php if ($this->viewer()->isAdmin()) :?>
	<div class="change-cover-btn">
		<a id="change-cover-btn" href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Change cover')?></a>
		<input class="music-cover-upload" id="archievement-cover-upload" type="file" accept="image/*" style="display: none"/>
	</div>
	
	<div class="reposition-cover-btn" <?php if (!$hasCover) echo 'style="display:none;"'?>>
		<a id="reposition-cover-btn" href="javascript:void(0)" onclick="repositionCover();"><i class="fa fa-arrows-v"></i><?php echo $this->translate('Reposition cover')?></a>
		<div class="reposition-cover-buttons" style="display: none;">
            <a href="javascript:void(0)" onclick="saveReposition();"><?php echo $this->translate('Save Position')?></a>
            <a href="javascript:void(0)" onclick="cancelReposition();"><?php echo $this->translate('Cancel')?></a>
            <input class="cover-position" name="pos" value="<?php echo $artist->cover_top?>" type="hidden">
        </div>
	</div>
	<?php endif;?>
	
	<div id="artist-photo" class="music-profile-photo">
		<div class="change-photo-wrapper" style="background-image: url('<?php echo $photoUrl?>')">
			<div id="playlist-photo-loading" class="music-loading" style="display: none;">
				<span class="upload-loading" style="text-align: center">
			    	<img src='application/modules/Ynmusic/externals/images/loading.gif'/>
				</span>
			</div>
			
			<?php if ($this->viewer()->isAdmin()) :?>
			<div class="change-photo-btn">
				<a id="change-photo-btn" href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Change image')?></a>
				<input class="music-photo-upload" id="archievement-photo-upload" type="file" accept="image/*" style="display: none"/>
			</div>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="artist-box-profile-info music-box-profile-info">
		<div id="artist-title" class="music-profile-title">
			<?php echo $artist;?>
		</div>
		
		<?php if (!empty($artist -> country)) :?>
		<div id="artist-country" class="music-profile-country">
			<?php echo '<i class="fa fa-map-marker"></i> &nbsp;'.$artist -> country;?>
		</div>
		<?php endif;?>
		
		<?php if (!empty($artist -> short_description)) :?>
		<div id="artist-short_description" class="music-profile-short_description">
			<?php echo strip_tags($artist -> short_description);?>
		</div>
		<?php endif;?>	
		
		<div class="music-addthis">
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynmusic_addthis_pubid', 'younet');?>" async="async"></script>
		<div class="addthis_sharing_toolbox"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
var cover_top = <?php echo $artist->cover_top?>;

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
            'url': '<?php echo $this->url(array('action'=>'reposition', 'subject'=>$artist->getGuid()),'ynmusic_general', true)?>',
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
    		'item_id' => $artist -> getIdentity(),
    		'item_type' => $artist -> getType(),
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
		                		cover_top = 0;
		                		parent_cover.getElement('.reposition-cover-btn').show();
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
    		'item_id' => $artist -> getIdentity(),
    		'item_type' => $artist -> getType(),
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