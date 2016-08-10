<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-09-07 16:37:11 taalay $
 * @author     Taalay
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

en4.core.runonce.add(function(){
  $('video_edit').style.clear = 'none';
});

var current_code;

var ignoreValidation = function() {
  $('upload-wrapper').style.display = "block";
  $('validation').style.display = "none";
  $('code').value = current_code;
  $('ignore').value = true;
}

var updateVideoFields = function()
{
  var video_element = document.getElementById("type");
  var url_element = document.getElementById("url-wrapper");
  var submit_element = document.getElementById("upload-wrapper");

  // clear url if input field on change
  //$('code').value = "";
  $('upload-wrapper').style.display = "none";

  // If video source is empty
  if (video_element.value == 0)
  {
    $('url').value = "";
    url_element.style.display = "none";
    return;
  }

  if ($('code').value && $('url').value)
  {
    $('upload-wrapper').style.display = "block";
    return;
  }

  // If video source is youtube or vimeo
  if (video_element.value == 1 || video_element.value == 2)
  {
    $('url').value = "";
    $('code').value = "";
    url_element.style.display = "block";
    return;
  }

  // If video source is from computer
  if (video_element.value == 3)
  {
    $('url').value = "";
    $('code').value = "";
    url_element.style.display = "none";
    return;
  }

  // if there is video_id that means this form is returned from uploading because some other required field
  if ($('id').value)
  {
    $('upload-wrapper').style.display = "block";
    return;
  }

}

var video = {
  active : false,

  debug : false,

  currentUrl : null,

  currentTitle : null,

  currentDescription : null,

  currentImage : 0,

  currentImageSrc : null,

  imagesLoading : 0,

  images : [],

  maxAspect : (10 / 3), //(5 / 2), //3.1,

  minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),

  minSize : 50,

  maxPixels : 500000,

  monitorInterval: null,

  monitorLastActivity : false,

  monitorDelay : 500,

  maxImageLoading : 5000,

  attach : function()
  {
    var bind = this;
    $('url').addEvent('keyup', function()
    {
      bind.monitorLastActivity = (new Date).valueOf();
    });

    var url_element = document.getElementById("url-element");
    var myElement = new Element("p");
    myElement.innerHTML = "test";
    myElement.addClass("description");
    myElement.id = "validation";
    myElement.style.display = "none";
    url_element.appendChild(myElement);

    var body = $('url');
    var lastBody = '';
    var lastMatch = '';
    var video_element = $('type');
    (function()
    {
      // Ignore if no change or url matches
      if( body.value == lastBody || bind.currentUrl )
      {
        return;
      }

      // Ignore if delay not met yet
      if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay )
      {
        return;
      }

      // Check for link
      var m = body.value.match(/http:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
      if( $type(m) && $type(m[0]) && lastMatch != m[0] )
      {
        if (video_element.value == 1){
          video.youtube(body.value);
        }
        else video.vimeo(body.value);
      }
      else{

      }

      lastBody = body.value;
    }).periodical(250);
  },

  youtube : function(url){
    // extract v from url
    var myURI = new URI(url);
    var youtube_code = myURI.get('data')['v'];
    if( youtube_code === undefined ) {
      youtube_code = myURI.get('file');
    }
    if (youtube_code){
      (new Request.HTML({
        'format': 'html',
        'url' : '<?php echo $this->url(array('controller' => 'video', 'action' => 'validation', 'product_id' => $this->product->getIdentity()), 'store_extended', true) ?>',
        'data' : {
          'ajax' : true,
          'code' : youtube_code,
          'type' : 'youtube'
        },
        'onRequest' : function(){
          $('validation').style.display = "block";
          $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
          $('upload-wrapper').style.display = "none";
        },
        'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
          if (valid){
            $('upload-wrapper').style.display = "block";
            $('validation').style.display = "none";
            $('code').value = youtube_code;
          }
          else{
            $('upload-wrapper').style.display = "none";
            current_code = youtube_code;
            <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here").'</a>'; ?>
                  $('validation').innerHTML = '<?php echo addslashes($this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link)); ?>';
          }
        }
      })).send();
    }
  },

  vimeo :function(url){
    var myURI = new URI(url);
    var vimeo_code = myURI.get('file');
    if (vimeo_code.length > 0){
      (new Request.HTML({
        'format': 'html',
        'url' : '<?php echo $this->url(array('controller' => 'video', 'action' => 'validation', 'product_id' => $this->product->getIdentity()), 'store_extended', true) ?>',
        'data' : {
          'ajax' : true,
          'code' : vimeo_code,
          'type' : 'vimeo'
        },
        'onRequest' : function(){
          $('validation').style.display = "block";
          $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
          $('upload-wrapper').style.display = "none";
        },
        'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
          if (valid){
            $('upload-wrapper').style.display = "block";
            $('validation').style.display = "none";
            $('code').value = vimeo_code;
          }
          else{
            $('upload-wrapper').style.display = "none";
            current_code = vimeo_code;
            <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"; ?>
            $('validation').innerHTML = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link); ?>";
          }
        }
      })).send();
    }
  }
}

en4.core.runonce.add(updateVideoFields);
en4.core.runonce.add(video.attach);

</script>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>


<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity(),'product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
          'class' => 'buttonlink product_back',
          'id' => 'store_product_editsettings',
        )) ?>
        <br>
        <?php if ($this->hasVideo) : ?>
          <?php echo $this->htmlLink($this->url(array('controller' => 'video', 'action' => 'delete', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('Delete Video'), array(
            'class' => 'buttonlink product_video_delete smoothbox',
            'id' => 'store_product_deletevideo',
          )) ?>
          <br>
          <?php echo $this->htmlLink($this->product->getHref(), $this->translate('Play Video'), array(
            'target' => '_blank',
            'class' => 'buttonlink product_video_play',
            'id' => 'store_product_playvideo',
          )) ?>
          <br>
        <?php endif; ?>
      </div>
    </li>
  </ul>
</div>

<?php if($this->video) : ?>
  <div class="settings admin_home_middle" style="clear: none;">
    <?php echo $this->form->render($this); ?>
  </div>
<?php else : ?>
  <div class="admin_home_middle">
    <ul class="form-notices"><li><?php echo $this->translate('There is no video.')?></li></ul>
  </div>
<?php endif; ?>
  