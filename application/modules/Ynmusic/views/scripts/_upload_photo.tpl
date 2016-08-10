
<?php $item = $this -> item;?>
<?php $id = $item -> getIdentity();?>

<div id="photo_<?php echo $id;?>-wrapper" class="form-wrapper">    
	<div class="form-label">            
    	<label><?php echo $this->translate('Photo')?></label>
    </div>
    <div class="form-element">
        <input class="section-photo-upload-<?php echo $id;?>" id="archievement-photo-upload-<?php echo $id;?>" type="file" accept="image/*">
        <br />
        <span class="upload-loading" style="display: none; text-align: center">
		    <img src='application/modules/Ynmusic/externals/images/loading.gif'/>
		</span> 
        <span class="upload-status form-description">
        	<?php $photoUrl = $item->getPhotoUrl();?> 
        	<?php if ($photoUrl):?>
        	<img id="uploadPreviewMain" src="<?php echo $photoUrl;?>"/>
        	<?php endif; ?>
        </span>
        <input type="hidden" id="photo_id_<?php echo $id;?>" class="upload-photos" name="photo_id_<?php echo $id;?>" value="<?php if ($item) echo $item->photo_id?>"/>
    	<p class="error"></p>
    </div>
</div>

<div id="cover_<?php echo $id;?>-wrapper" class="form-wrapper">    
	<div class="form-label">            
    	<label><?php echo $this->translate('Cover')?></label>
    </div>
    <div class="form-element">
        <input class="section-cover-upload-<?php echo $id;?>" id="archievement-cover-upload-<?php echo $id;?>" type="file" accept="image/*">
        <br />
        <span class="upload-loading" style="display: none; text-align: center">
		    <img src='application/modules/Ynmusic/externals/images/loading.gif'/>
		</span>  
        <p class="upload-status description">
        	<?php 
    		$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->item->cover_id)->current();
			$coverPhotoUrl = null;
			if($coverFile){
				$coverPhotoUrl = $coverFile->map();
			}	
			if($coverPhotoUrl) :?>
				<img id="uploadPreviewCover" src="<?php echo $coverPhotoUrl;?>"/>
        	<?php endif;?>
        </p>
        <input type="hidden" id="cover_id_<?php echo $id;?>" class="upload-photos" name="cover_id_<?php echo $id;?>" value="<?php if ($item) echo $item->cover_id?>"/>
    	<p class="error"></p>
    </div>
</div>
            
<script type="text/javascript">
 	window.addEvent('domready', function() {
		//for upload photos
	    var url_photo = '<?php echo $this->url(array(
	    		'action' => 'upload-photo',
	    		'item_id' => $item -> getIdentity(),
	    		'item_type' => $item -> getType(),
	    		'upload_type' => 'photo_id',
	    		'save' => 0
		), 'ynmusic_general', true)?>';
		
	    $$('.section-photo-upload-<?php echo $id;?>').each(function(el) {
	        var div_parent = el.getParent('#photo_<?php echo $id;?>-wrapper');
	        var id = el.get('id');
	        jQuery('#'+id).fileupload({
	            url: url_photo,
	            dataType: 'json',
	            done: function (e, data) {
	                var status_div = div_parent.getElement('.upload-status');
	                var photo_id = div_parent.getElement('#photo_id_<?php echo $id;?>');
	                jQuery.each(data.result.files, function (index, file) {
	                	var loading = div_parent.getElement('.upload-loading');
	            		loading.hide();
	                    if(file.status) {
	                    	status_div.innerHTML = '<img id="uploadPreviewMain" src="'+file.photo_url+'"/>';
	                    	photo_id.set('value', file.photo_id);
	                    }
	                    else {
	                    	status_div.innerHTML = '<?php echo $this->translate('Upload fail!')?> '+file.error;
	                    }
	                });
	            },
	            progressall: function (e, data) {
	            	var loading = div_parent.getElement('.upload-loading');
	            	loading.show();
	            	var status_div = div_parent.getElement('.upload-status');
	            	status_div.innerHTML = '';
	          	},
	        }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');        
	    });
	    
	    //for upload covers
	    var url_cover = '<?php echo $this->url(array(
	    		'action' => 'upload-photo',
	    		'item_id' => $item -> getIdentity(),
	    		'item_type' => $item -> getType(),
	    		'upload_type' => 'cover_id',
	    		'save' => 0
		), 'ynmusic_general', true)?>';
	    
	    $$('.section-cover-upload-<?php echo $id;?>').each(function(el) {
	        var div_parent = el.getParent('#cover_<?php echo $id;?>-wrapper');
	        var id = el.get('id');
	        jQuery('#'+id).fileupload({
	            url: url_cover,
	            dataType: 'json',
	            done: function (e, data) {
	                var status_div = div_parent.getElement('.upload-status');
	                var photo_id = div_parent.getElement('#cover_id_<?php echo $id;?>');
	                jQuery.each(data.result.files, function (index, file) {
	                	var loading = div_parent.getElement('.upload-loading');
	            		loading.hide();
	                    if(file.status) {
	                        status_div.innerHTML += '<img id="uploadPreviewCover" src="'+file.photo_url+'"/>';
	                        photo_id.set('value', file.photo_id);
	                    }
	                    else {
	                    	status_div.innerHTML = '<?php echo $this->translate('Upload fail!')?> '+file.error;
	                    }
	                });
	            },
	            progressall: function (e, data) {
	            	var loading = div_parent.getElement('.upload-loading');
	            	loading.show();
	            	var status_div = div_parent.getElement('.upload-status');
	            	status_div.innerHTML = '';
	          	},
	        }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');        
	    });
	});
</script>