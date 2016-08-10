 <?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
 	$this->headLink() 
 		// ->prependStylesheet('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css')
 		->prependStylesheet($staticBaseUrl . 'application/modules/Ynmusic/externals/styles/upload_song/bootstrap.min.css')
		->prependStylesheet($staticBaseUrl . 'application/modules/Ynmusic/externals/styles/upload_song/jquery.fileupload.css');
		
	$this->headScript()
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')	
		->appendScript('jQuery.noConflict();')
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')	
  		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')	
		->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');
		
	$max_fileSizes = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynmusic_song', $this->viewer(), 'max_filesize');
	$maxSize = '';
	if ($max_fileSizes >= 1048576) {
		$maxSize = round($max_fileSizes/1048576, 2).'GB';
	}
	elseif ($max_fileSizes >= 1024) {
		$maxSize = round($max_fileSizes/1024, 2).'MB';
	}
	else {
		$maxSize = $max_fileSizes.'KB';
	}
	$max_storage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynmusic_song', $this->viewer(), 'max_storage');	
	$maxStorage = '';
	if ($max_storage >= 1048576) {
		$maxStorage = round($max_storage/1048576, 2).'GB';
	}
	elseif ($max_storage >= 1024) {
		$maxStorage = round($max_storage/1024, 2).'MB';
	}
	else {
		$maxStorage = $max_storage.'KB';
	}
?>
<div id="file-wrapper">
 	<div class="form-label">&nbsp;</div>
  	<div class="form-element">
	  	<div style="padding-bottom: 15px"><?php echo $this->translate('_YNMUSIC_UPLOAD_DESCRIPTION', $maxSize, $maxStorage); ?></div>
		<!-- The fileinput-button span is used to style the file input field as button -->
	  	<span class="btn fileinput-button btn-success" type="button">
	      	<i class="glyphicon glyphicon-plus"></i>
	      	<span><?php echo $this->translate("Add Songs")?></span>
	      	<!-- The file input field used as target for the file upload widget -->
	      	<input id="fileupload" type="file" name="files[]" multiple>
	  	</span>
	  	<button type="button" class="btn btn-danger delete" onclick="clearList();">
	     	<i class="glyphicon glyphicon-trash"></i>
	      	<span><?php echo $this->translate("Clear List")?></span>
	  	</button>
	  	<!-- The global progress bar -->
	  	<div id="progress" class="progress" style="display: none; width: 80%; float:left">
	      	<div class="progress-bar progress-bar-success"></div>
	      	<span id="progress-percent" style="padding-left: 10px"></span>
	  	</div>
	  	<!-- The container for the uploaded files -->
	  	<div class="files-contain" id="files-contain">
	  		<ul id="files" class="files"></ul>
	  	</div>
 	</div>
	<div class="form-label">&nbsp;</div>
	<div class="form-element">
  		<input id="ynmusic-confirm-create" type="checkbox" /> &nbsp;
  		<?php
		$terms = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmusic_terms', "");
		if ($terms != "") : ?>
			<?php echo $terms; ?>
  		<?php else :?>
    	<?php echo $this -> translate('By uploading any songs to %s, you hereby certify that you own the copyright in or have all the necessary rights related to such content to upload in.', $this -> layout()->siteinfo['title']);?>
  		<?php endif;?>
  	</div>
</div>

<script>
jQuery(function () {
    // Change this to the location of your server-side upload handler:
    var count = 0;
    var url = '<?php echo $this->url(array('action' => 'upload-song'), 'ynmusic_song')?>';
    jQuery('#fileupload').fileupload({
    	dropZone: jQuery('#files-contain'),
        url: url,
        dataType: 'json',
        done: function (e, data) 
        {
        		$('files').style.display = 'block';
        		var flag = false;
            jQuery.each(data.result.files, function (index, file) 
            {
            	var text = "";
            	var ele = jQuery('<li/>');
            	ele.attr('id', count);
            	ele.addClass('new_file');
            	if(file.status)
            	{
            		flag = true;
            		text = '<a class="file-remove" onclick = "removeFile('+ count +', ' + file.song_id + ')" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
            		text += '<span class="file-name">' + file.name + '</span>';
            		ele.addClass('file-success');
            		ele.html(text).appendTo('#files');
            		$('html5uploadfileids').value = $('html5uploadfileids').value + ' ' + file.song_id;
            	}
            	else
            	{
            		text = '<a class="file-remove" onclick = "removeFile('+ count +', 0)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
            		if(file.name)
            			text += '<span class="file-name">' + file.name + '</span>';
            		text += '<span class="file-info"><span>' + file.error +'</span></span>';
                ele.html(text).appendTo('#files');
              }
            });
            if(flag)
            {
            	if($('submit-wrapper'))
            		$('submit-wrapper').style.display = 'block';
            }
            count ++;
        },
        progressall: function (e, data) 
        {
        	 $('progress').style.display = 'block';
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
            jQuery('#progress-percent').text(
                progress + '%'
            );
        }
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
});
function removeFile(count, song_id) {
	var ul = jQuery('#' + count).parent('ul.files');
	jQuery('#' + count).remove();
	if(song_id)
		$('html5uploadfileids').value = $('html5uploadfileids').value.replace(song_id, '');
	if (ul.find('li').length == 0) {
		ul.hide();
	}
	return false;
}
function clearList() {
	$$('li.new_file').each(function(e){e.destroy()});
	$('html5uploadfileids').value = '';
	if($('submit-wrapper'))
		$('submit-wrapper').style.display = 'none';
	$('progress').style.display = 'none';
	$('progress-percent').innerHTML = '';
	return false;
}
</script>