<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/composer_video.js');

    $allowed = 0;
    $user = Engine_Api::_()->user()->getViewer();
    $allowed_upload = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynultimatevideo_video', $user, 'upload');
    $ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->ynultimatevideo_ffmpeg_path;
    if ($allowed_upload && $ffmpeg_path) {
        $allowed = 1;
    }
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        var type = '';
        if (composeInstance.options.type) type = composeInstance.options.type;
        composeInstance.addPlugin(new Composer.Plugin.Ynultimatevideo({
            title : '<?php echo $this->translate('Add Ultimate Video') ?>',
            lang : {
                'Add Video' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
                'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
                'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
                'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
                'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
                'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
                'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
                'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube Video')) ?>',
                'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo Video')) ?>',
                'To upload a video from your computer, please use our full uploader.': '<?php echo $this->string()->escapeJavascript($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create', 'type' => 3), 'ynultimatevideo_general'))) ?>'
            },
            allowed : <?php echo $allowed; ?>,
            type : type,
            requestOptions : {
                'url' : en4.core.baseUrl + 'ultimate-videos/index/compose-upload/format/json/c_type/' + type
            }
        }));
    });
</script>
