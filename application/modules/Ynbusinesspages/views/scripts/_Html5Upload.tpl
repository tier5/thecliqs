 <?php
	$this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
	$staticBaseUrl = $this->layout()->staticBaseUrl;
 	$this->headLink() 
 	->prependStylesheet($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/bootstrap.css')
	->prependStylesheet($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/upload_photo/jquery.fileupload.css');
		
	$this->headScript()
  	->appendFile($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.7.1.min.js')	
	->appendScript('jQuery.noConflict();')
  	->appendFile($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/js/vendor/jquery.ui.widget.js')	
  	->appendFile($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/js/jquery.iframe-transport.js')
	->appendFile($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/js/jquery.fileupload.js')	
	->appendFile($staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/bootstrap.js')		
;	
 ?>
 <div id="file-wrapper">
 	<div class="form-label">&nbsp;</div>
  <div class="form-element">
  	<div style="padding-bottom: 15px"><?php echo $this->translate('CORE_VIEWS_SCRIPTS_FANCYUPLOAD_ADDPHOTOS');?></div>
	<!-- The fileinput-button span is used to style the file input field as button -->
  <span class="btn fileinput-button btn-success" type="button">
      <i class="glyphicon glyphicon-plus"></i>
      <span><?php echo $this->translate("Add Photos")?></span>
      <!-- The file input field used as target for the file upload widget -->
      <input id="fileupload" type="file" name="files[]" accept="image/*" multiple>
  </span>
  <button type="button" class="btn btn-danger delete" onclick="clearList()">
      <i class="glyphicon glyphicon-trash"></i>
      <span><?php echo $this->translate("Clear List")?></span>
  </button>
  <br>
  <br>
  <!-- The global progress bar -->
  <div id="progress" class="progress" style="display: none; width: 400px; float:left">
      <div class="progress-bar progress-bar-success"></div>
  </div>
  <span id="progress-percent" style="padding-left: 10px"></span>
  <!-- The container for the uploaded files -->
  <ul id="files" class="files"></ul>
 </div>
</div>
<script>
var count = 0;
jQuery(function () 
{
    // Change this to the location of your server-side upload handler:
    <?php
    	if ($this->isCover)
    		$url = $this->url(array('module' => 'ynbusinesspages', 'controller' => 'dashboard', 'action' => 'upload-photo'), 'default');
    	else 
    		$url =  $this->url(array('module' => 'ynbusinesspages', 'controller' => 'photo', 'action' => 'upload-photo'), 'default');
    ?>
    var url = '<?php echo $url; ?>';
    jQuery('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        sequentialUploads: 1,
        done: function (e, data) 
        {
        		$('files').style.display = 'block';
	            jQuery.each(data.result.files, function (index, file) 
	            {
	            	var text = "";
	            	var ele = jQuery('<li/>');
	            	ele.attr('id', count);
	            	if(file.status)
	            	{
	            		text = '<a class="file-remove" onclick = "removeFile('+ count +', ' + file.photo_id + ')" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
	            		text += '<span class="file-name">' + file.name + '</span>';
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
            $('submit-wrapper').style.display = 'block';
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
function deletePhoto(photo_id)
{
	<?php
	    	if ($this->isCover)
	    		$deleteUrl = $this->url(array('module'=>'ynbusinesspages','controller'=>'dashboard','action'=>'delete-photo'), 'default');
	    	else 
	    		$deleteUrl = $this->url(array('module'=>'ynbusinesspages','controller'=>'photo','action'=>'delete-photo'), 'default');
	?>
	new Request.JSON({
        url: '<?php echo $deleteUrl; ?>',
        data: {
          'format': 'json',
          'photo_id': photo_id,
          'business_id': $("business_id").value
        },
        'onSuccess' : function(responseJSON, responseText)
    	{
    		element.innerHTML = content;
    		checkbox = document.getElementById('goodgroup_'+group_id);
    		if( status == 1) checkbox.checked=true;
    		else checkbox.checked=false;
    	}
      }).send();
}
function removeFile(count, photo_id)
{
	jQuery('#' + count).remove();
	if(photo_id)
	{
		 deletePhoto(photo_id);
		$('html5uploadfileids').value = $('html5uploadfileids').value.replace(photo_id, '');
	}
	if($('html5uploadfileids').value.trim() == "")
	{
		$('files').style.display = 'none';
		$('progress').style.display = 'none';
		$('progress-percent').innerHTML = '';
		$('submit-wrapper').style.display = 'none';
		$('html5uploadfileids').value = '';
	}
	return false;
}
function clearList()
{
	var ids = $('html5uploadfileids').value;
	var arr = ids.split(" ");
	for(var i = 0; i < arr.length; i ++)
	{
		if(arr[i] > 0)
			deletePhoto(arr[i]);
	}
	$('files').style.display = 'none';
	jQuery('#files').text('');
	$('html5uploadfileids').value = '';
	$('submit-wrapper').style.display = 'none';
	$('progress').style.display = 'none';
	$('progress-percent').innerHTML = '';
	return false;
}
</script>