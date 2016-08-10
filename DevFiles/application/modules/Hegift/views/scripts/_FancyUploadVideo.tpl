<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _FancyUpload.tpl 2012-02-17 17:07:11 taalay $
 * @author     Taalay
 */
?>

<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
  ));
?>

<script type="text/javascript">
  var uploadCount = 0;
  var extraData = <?php echo $this->jsonInline($this->data); ?>;

  window.addEvent('domready', function() { // wait for the content
    var up_video = new FancyUpload2($('demo-status-video'), $('demo-list-video'), { // options object
      // we console.log infos, remove that in production!!
      verbose: <?php echo ( APPLICATION_ENV == 'development' ? 'true' : 'false') ?>,
      appendCookieData: true,
      timeLimit: 0,

      // set cross-domain policy file
      policyFile : '<?php echo (_ENGINE_SSL ? 'https://' : 'http://')
          . $_SERVER['HTTP_HOST'] . $this->url(array(
            'controller' => 'cross-domain'),
            'default', true) ?>',

      // url is read from the form, so you just have to change one place
      url: $('form-upload').action + '?gift_type=video',
      
      // path to the SWF file
      path: '<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.swf'; ?>',
      
      typeFilter: {
        //'Videos (*.avi, *.flv, *.matroska, *.mov, *.mp4, *.3gp, *.mpeg, *.swf, *.wav)': '*.avi, *.flv, *.matroska, *.mov, *.mp4, *.3gp, *.mpeg, *.swf, *.wav'
      },
      
      // this is our browse button, *target* is overlayed with the Flash movie
      target: 'demo-browse-video',
      data: extraData,
      multiple: false,
      
      // graceful degradation, onLoad is only called if all went well with Flash
      onLoad: function() {
        $('demo-status-video').removeClass('hide'); // we show the actual UI
        $('demo-fallback-video').destroy(); // ... and hide the plain form
        
        // We relay the interactions with the overlayed flash to the link
        this.target.addEvents({
          click: function() {
            return false;
          },
          mouseenter: function() {
            this.addClass('hover');
          },
          mouseleave: function() {
            this.removeClass('hover');
            this.blur();
          },
          mousedown: function() {
            this.focus();
          }
        });
        
        // Interactions for the 2 other buttons
        $('demo-clear-video').addEvent('click', function() {
        up_video.remove(); // remove all files
        var video = document.getElementById('fancyuploadvideo');

        video.value ="";
        return false;
      });

      },
      
      // Edit the following lines, it is your custom event handling
      
      /**
       * Is called when files were not added, "files" is an array of invalid File classes.
       *
       * This example creates a list of error elements directly in the file list, which
       * hide on click.
       */
      onSelectFail: function(files) {
        files.each(function(file) {
          new Element('li', {
            'class': 'validation-error',
            html: file.validationErrorMessage || file.validationError,
            title: MooTools.lang.get('FancyUpload', 'removeTitle'),
            events: {
              click: function() {
                this.destroy();
              }
            }
          }).inject(this.list, 'top');
        }, this);
      },
      
      onComplete: function hideProgress() {
        var demostatuscurrent = document.getElementById("demo-status-video-current");
        var demostatusoverall = document.getElementById("demo-status-video-overall");
        var demosubmit = document.getElementById("upload-wrapper");
        
        demostatuscurrent.style.display = "none";
        demostatusoverall.style.display = "none";
        demosubmit.style.display = "block";
        $('demo-browse-video').style.display = 'none';
      },
      
      onFileStart: function() {
        uploadCount += 1;
      },
      onFileRemove: function(file) {
        var url = (<?php echo ($this->user)?1:0;?>) ?
                '<?php echo $this->url(array('action'=>'remove'), 'hegift_own', true)?>' :
                '<?php echo $this->url(array('module'=>'hegift', 'controller' => 'index', 'action'=>'remove'), 'admin_default', true) ?>';
        uploadCount -= 1;
        file_id = file.file_id;
        request = new Request.JSON({
          'format' : 'json',
          'url' : url,
          'data': {
             'file_id' : file_id
          },
          'onSuccess' : function(responseJSON) {
            if ($("demo-list-video").getChildren('li').length == 0)
            {
              var democlear  = document.getElementById("demo-clear-video");
              var demolist   = document.getElementById("demo-list-video");
              var demosubmit = document.getElementById("upload-wrapper");
              democlear.style.display  = "none";
              demolist.style.display   = "none";
              demosubmit.style.display = "none";
              $('demo-browse-video').style.display = 'block';
            }
            return false;
          }
        });
        request.send();
        var video = document.getElementById('fancyuploadvideo');

        if (uploadCount == 0) {
          var democlear = document.getElementById("demo-clear-video");
          var demolist = document.getElementById("demo-list-video");
          var demosubmit = document.getElementById("upload-wrapper");
          democlear.style.display = "none";
          demolist.style.display = "none";
          demosubmit.style.display = "none";
          $('demo-browse-video').style.display = 'block';
        }
        video.value = video.value.replace(file_id, "");
      },
      onSelectSuccess: function(file) {
        $('demo-list-video').style.display = 'block';
        $('demo-browse-video').style.display = 'none';
        var democlear = document.getElementById("demo-clear-video");
        var demostatuscurrent = document.getElementById("demo-status-video-current");
        var demostatusoverall = document.getElementById("demo-status-video-overall");

        democlear.style.display = "inline";
        demostatuscurrent.style.display = "block";
        demostatusoverall.style.display = "block";
        up_video.start();
      },
      /**
       * This one was directly in FancyUpload2 before, the event makes it
       * easier for you, to add your own response handling (you probably want
       * to send something else than JSON or different items).
       */
      onFileSuccess: function(file, response) {
        var json = new Hash(JSON.decode(response, true) || {});

        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate("Upload complete.")) ?></span>');
          var video = document.getElementById('fancyuploadvideo');
          video.value = video.value + json.get('file_id') + " ";
          file.file_id = json.get('file_id');
        } else {
          file.element.addClass('file-failed');

          file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate("An error occurred:")) ?></span> ' + (json.get('error') ? (json.get('error')) : response));
          //file.info.set('html', '<span>An error occurred:</span> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
        }
      },
      
      /**
       * onFail is called when the Flash movie got bashed by some browser plugin
       * like Adblock or Flashblock.
       */
      onFail: function(error) {
        switch (error) {
          case 'hidden': // works after enabling the movie and clicking refresh
            alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).")) ?>');
            break;
          case 'blocked': // This no *full* fail, it works after the user clicks the button
            alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).")) ?>');
            break;
          case 'empty': // Oh oh, wrong path
            alert('<?php echo $this->string()->escapeJavascript($this->translate("A required file was not found, please be patient and we'll fix this.")) ?>');
            break;
          case 'flash': // no flash 9+
            alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, install the latest Adobe Flash plugin.")) ?>');
        }
      }
        
    });
  });
</script>

<input type="hidden" name="fancyuploadvideo" id="fancyuploadvideo" value ="" />
<fieldset id="demo-fallback-video">
  <legend><?php echo $this->translate('File Upload');?></legend>
  <p>
    <?php echo $this->translate('This form is just an example fallback for the unobtrusive behaviour of FancyUpload. If this part is not changed, something must be wrong with your code.');?>
  </p>
  <label for="demo-videoupload">
    <?php echo $this->translate('Upload a Video:');?>
    <input type="file" name="Filedata" />
  </label>
</fieldset>

<div id="demo-status-video" class="hide">
  <div>
    <?php echo $this->translate('HEGIFT_Add Video DESC');?>
  </div>
  <div>
    <a class="buttonlink icon_video_gift" href="javascript:void(0);" id="demo-browse-video"><?php echo $this->translate('Add Video');?></a>
    <a class="buttonlink icon_clearlist" href="javascript:void(0);" id="demo-clear-video" style='display: none;'><?php echo $this->translate('Clear List');?></a>
  </div>
  <div class="demo-status-video-overall" id="demo-status-video-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
  </div>
  <div class="demo-status-video-current" id="demo-status-video-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>
<ul id="demo-list-video"></ul>
