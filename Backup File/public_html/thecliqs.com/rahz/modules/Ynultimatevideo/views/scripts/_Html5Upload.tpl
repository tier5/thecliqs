<?php
	$this->headScript()
->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js');
?>

<div id="file-wrapper">
    <script type="text/javascript">
        function fileSelected()
        {
            var file = document.getElementById('fileToUpload').files[0];
            if (file) {
                var fileSize = 0;
                if (file.size > 1024 * 1024)
                    fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
                else
                    fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';

                document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
                document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
                document.getElementById('progress').style.display = 'inline-block';
                document.getElementById('progressNumber').style.display = 'inline-block';
                document.getElementById('demo-upload').style.display = 'inline-block';
            }
        }

        function uploadFile()
        {
            document.getElementById('demo-upload').style.display = 'none';
            var fd = new FormData();
            fd.append('fileToUpload', document.getElementById('fileToUpload').files[0]);
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener("progress", uploadProgress, false);
            xhr.addEventListener("load", uploadComplete, false);
            xhr.addEventListener("error", uploadFailed, false);
            xhr.addEventListener("abort", uploadCanceled, false);
            var allow = 0;
            if(document.getElementById('allow_upload_channel') && document.getElementById('allow_upload_channel').checked)
            {
                allow = 1;
            }
            xhr.open("POST", "<?php echo $this->url(array('action' => 'upload-video'), 'ynultimatevideo_general', true)?>", true);
            fd.append('allow', allow);
            xhr.send(fd);
        }

        function uploadProgress(evt)
        {
            if (evt.lengthComputable)
            {
                var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                $('progressNumber').innerHTML = percentComplete.toString() + '%';
                jQuery('#progress').find('.progress-bar').css('width', percentComplete + '%');
            }
        }

        function uploadComplete(evt)
        {
            /* This event is raised when the server send back a response */
            var json = JSON.decode(evt.target.responseText);
            var element = document.getElementById('upload_status');;
            if (json.status == 1)
            {
                $('code').value=json.code;
                $('id').value=json.video_id;
                $('form-upload').submit();
            }
            else
            {
                element.addClass('tip');
                element.set('html', '<span><b>Upload has failed: </b>' + (json.error ? (json.error) : evt.target.responseText)) + "</span>";
                document.getElementById('progress').style.display = 'none';
                document.getElementById('demo-upload').style.display = 'none';
            }
        }

        function uploadFailed(evt)
        {
            var element = document.getElementById('upload_status');
            element.addClass('tip')
            element.innerHTML = "<span><?php echo $this->translate('There was an error attempting to upload the file.')?></span>";
            document.getElementById('progress').style.display = 'none';
            document.getElementById('demo-upload').style.display = 'none';
        }

        function uploadCanceled(evt)
        {
            var element = document.getElementById('upload_status');
            element.addClass('tip')
            element.innerHTML = "<span><?php echo $this->translate('The upload has been canceled by the user or the browser dropped the connection.')?></span>";
            document.getElementById('progress').style.display = 'none';
            document.getElementById('demo-upload').style.display = 'none';
        }
    </script>
    <div class="form-label">&nbsp;</div>
    <div class="form-element">
        <div id="demo-status">
            <div class="select_file">
                <input type="file" accept="video/*"  name="fileToUpload" id="fileToUpload" onchange="fileSelected();"/>
            </div>
        </div>
        <div class="file_info" id="file_info">
            <div id="fileName"></div>
            <div id="fileSize"></div>
        </div>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo_youtube_allow', 0)): ?>
        <div id="allow_upload_channel-element" class="form-element">
            <input type="hidden" name="allow_upload_channel" value=""><input type="checkbox" name="allow_upload_channel" id="allow_upload_channel" value="1">
            <label for="allow_upload_channel" class="optional"><?php echo $this->translate("Also upload this video to YouTube"); ?></label>
        </div>
        <?php endif; ?>
        <div style="padding-bottom: 15px"><?php echo $this->translate('Please wait while your video is being uploaded. When your upload is finished, your video will be processed - you will be notified when it is ready to be viewed.'); ?></div>
        <div id="upload_status"></div>

        <!--progress bar-->
        <div id="progress" style="display:none;">
            <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
        </div>

        <span style="display:none;" id="progressNumber" class="progress-text">0%</span>

        <div class="ynultimatevideo-btn-post-video">
            <a class="button" href="javascript:uploadFile();" id="demo-upload" style="display: none;">Post Video</a>
        </div>
    </div>
</div>