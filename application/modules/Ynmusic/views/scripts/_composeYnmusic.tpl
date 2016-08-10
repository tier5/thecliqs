
<?php
  $this
  	->headScript()
  	->appendFile($this->baseUrl() . '/externals/soundmanager/script/soundmanager2'. (APPLICATION_ENV == 'production' ? '-nodebug-jsmin' : '' ) . '.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynmusic/externals/scripts/composer_music.js')
    ->appendFile($this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->baseUrl() . '/externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->baseUrl() . '/externals/fancyupload/FancyUpload2.js')
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.Ynmusic({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Social Music')) ?>',
      lang : {
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Loading song, please wait...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading song, please wait...')) ?>',
        'Unable to upload music. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload music. Please click cancel and try again.')) ?>',
        'Song got lost in the mail. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Song got lost in the mail. Please click cancel and try again.')) ?>'
      },
      requestOptions : {
       'url'  : en4.core.baseUrl  + 'social-music/songs/upload-song/format/json'+'?type='+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl  + 'social-music/songs/upload-song/format/json'+'?type='+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>

