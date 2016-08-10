<?php
    $staticBaseUrl = $this -> layout() -> staticBaseUrl;
    $this -> headLink() 
        -> prependStylesheet('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css') 
        -> prependStylesheet($staticBaseUrl . 'application/modules/Ynjobposting/externals/styles/upload_photo/jquery.fileupload.css');

    $this -> headScript() 
        -> appendFile($staticBaseUrl . 'application/modules/Ynjobposting/externals/scripts/jquery-1.7.1.min.js') 
        -> appendScript('jQuery.noConflict();') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynjobposting/externals/scripts/js/vendor/jquery.ui.widget.js') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynjobposting/externals/scripts/js/jquery.iframe-transport.js') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynjobposting/externals/scripts/js/jquery.fileupload.js') 
        -> appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');
?>
 <div id="file-wrapper">
 	<div id="file-label" class="form-label">
 	    <label for="header" class="optional"><?php echo $this->translate('Upload photo(s)') ?></label>
    </div>
  <div class="form-element">
  	<p class="description"><?php echo $this->translate('You can upload a JPG, JPEG, GIF or PNF file.'); ?></p>
  	<p class="description"><?php echo $this->translate('The file size limit is 5 mb. If you upload does not work, try uploading a smaller picture.'); ?></p>
  	<p class="description"><?php echo $this->translate('Maximum 3 photos uploaded.'); ?></p>
	<!-- The fileinput-button span is used to style the file input field as button -->
  <span class="btn fileinput-button btn-success" type="button">
      <i class="glyphicon glyphicon-plus"></i>
      <span><?php echo $this->translate("Add Photos")?></span>
      <!-- The file input field used as target for the file upload widget -->
      <input id="fileupload" type="file" name="files[]" multiple accept="image/*">
  </span>
  <button type="button" class="btn btn-danger delete" onclick="clearList();">
      <i class="glyphicon glyphicon-trash"></i>
      <span><?php echo $this->translate("Clear List")?></span>
  </button>

  <!-- The global progress bar -->
  <div class="progress-contain">
      <div id="progress" class="progress" style="display: none; margin-top: 10px; width: 400px; float:left">
          <div class="progress-bar progress-bar-success"></div>
      </div>
      <span id="progress-percent" style="margin-top: 10px;"></span>
  </div>

  <!-- The container for the uploaded files -->
  <div class="files-contain <?php if (sizeof($this->photos)) echo 'success';?>">
    <ul id="files" class="files">
        <?php $count = 0; ?>
        <?php foreach ($this->photos as $photo) : ?>
            <li id="<?php echo $count?>" class="file-success">
                <a class="file-remove" onclick = "removeFile(<?php echo $count?>, <?php echo $photo['id']?>)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>
                <span class="choose-default-photo"><input id="radio-photo-<?php echo $photo['id']?>" type="radio" name="default_photo" value="<?php echo $photo['id']?>" <?php if ($photo['isDefault']) echo 'checked'?>/><label for="radio-photo-<?php echo $photo['id'] ?>"><?php echo $this->translate('Default')?></label></span>
                <img class="file-image" src="<?php echo $photo['url']?>"/>
                <input type="hidden" name="photo_ids[]" value="<?php echo $photo['id']?>" />
            </li>
        <?php $count++; ?>
        <?php endforeach; ?>
    </ul>
  </div>
 </div>
</div>
<script>
jQuery(function () 
{
    // Change this to the location of your server-side upload handler:
    var children = $('files').getChildren('li');
    var count = children.length;
    var url = '<?php echo $this->url(array('module' => 'ynjobposting', 'controller' => 'jobs', 'action' => 'upload-photo'), 'default')?>';
    jQuery('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) 
        {
    		$('files').style.display = 'block';
            jQuery('.files-contain').addClass('success');
            jQuery.each(data.result.files, function (index, file) 
            {
            	var text = "";
            	var ele = jQuery('<li/>');
            	ele.attr('id', count);
            	var children = $('files').getChildren('li');
            	var childLength = children.length;
            	if (childLength >= 3) {
            	    children[0].destroy();
            	}
            	if(file.status)
            	{
            		text = '<a class="file-remove" onclick = "removeFile('+ count +', ' + file.photo_id + ')" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
            		text += '<span class="choose-default-photo"><input id="radio-photo-'+file.photo_id+'" type="radio" name="default_photo" value="'+file.photo_id+'" /><label for="radio-photo-'+file.photo_id+'"><?php echo $this->translate('Default')?></label></span>';
            		text += '<img class="file-image" src="'+file.photo_url+'"/>';
            		text += '<input type="hidden" name="photo_ids[]" value="'+file.photo_id+'" />';
            		ele.addClass('file-success');
            		ele.html(text).appendTo('#files');
            		$('html5uploadfileids').value = $('html5uploadfileids').value + ' ' + file.photo_id;
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
            jQuery('#progress-percent').css('display', 'inline-block').text(
                progress + '%'
            );
        }
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
});
function removeFile(count, photo_id)
{
	jQuery('#' + count).remove();
	if(photo_id) {
        $('html5uploadfileids').value = $('html5uploadfileids').value.replace(photo_id, '');
	}
	
    if( $('files').getChildren().length==0 ) {
        $('files').hide();
        $('progress').hide();
        $('progress-percent').set('style', 'margin-top: 10px; display: none');
        $$('.files-contain')[0].set('class','files-contain');        
    }	
    
    
	return false;
}
function clearList()
{
	$('files').style.display = 'none';
    jQuery('.files-contain').removeClass('success');
	jQuery('#files').text('');
	$('html5uploadfileids').value = '';
	$('progress').style.display = 'none';
	$('progress-percent').innerHTML = '';
	return false;
}
</script>
<script type="text/javascript">
    window.addEvent('domready', function () {
        <?php if (sizeof($this->photos) > 0) :?>
            $('files').style.display = 'block';
        <?php endif; ?>
    });
</script>