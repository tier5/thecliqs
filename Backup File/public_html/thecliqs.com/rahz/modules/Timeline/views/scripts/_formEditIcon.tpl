
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _formEditImage.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Adilet
 */

?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
  ?>
<div>
  <?php $thumb = $this->subject();?>
  <?php if ($thumb->photo_id ):?>
    <?php echo $this->itemPhoto($thumb, 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  <?php else:?>
    <img alt="" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/icons/thumbs/default.png">
  <?php endif;?>
</div>
<br/>
<div id="preview-thumbnail" class="preview-thumbnail">
  <?php if ($thumb->photo_id ):?>
  <?php echo $this->itemPhoto($thumb, 'thumb.icon', "", array('id' => 'previewimage')) ?>
  <?php else:?>
  <img alt="" src="<?php echo $this->baseUrl();?>/application/modules/Timeline/externals/images/icons/thumbs/default.png">
  <?php endif;?>
</div>
<div id="thumbnail-controller" class="thumbnail-controller">
  <?php if ($thumb->photo_id):?>
    <?php   $file = Engine_Api::_()->getItemTable('storage_file')->getFile($thumb->photo_id, "thumb.profile");
      $size = getimagesize($file->storage_path);
      if ($size[0] > 125 && $size[1] > 86) :?>
      <a href="javascript:void(0);" onclick="lassoStart();"> <?php echo $this->translate('Edit Thumbnail') ?></a>
    <?php endif;?>
  <?php endif;?>
</div>
<script type="text/javascript">
  var orginalThumbSrc;
  var originalSize;
  var loader = new Element('img', { src:en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
  var lassoCrop;

  var lassoSetCoords = function (coords) {
    var deltaw = (coords.w - 115) / coords.w;
    var deltah = (coords.h - 76) / coords.h;
    $('coordinates').value =
      coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;

    $('previewimage').setStyles({
      top:-( coords.y - (coords.y * deltah) ),
      left:-( coords.x - (coords.x * deltaw) ),
      height:( originalSize.y - (originalSize.y * deltah) ),
      width:( originalSize.x - (originalSize.x * deltaw) )
    });
  }

  var lassoStart = function () {
    if (!orginalThumbSrc) orginalThumbSrc = $('previewimage').src;
    originalSize = $("lassoImg").getSize();

    lassoCrop = new Lasso.Crop('lassoImg', {
      ratio:[15, 10],
      preset:[10, 10, 125, 86],
      handleSize:8,
      opacity:.6,
      color:'#7389AE',
      border:'<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
      onResize:lassoSetCoords,
      bgimage:''
    });

    $('previewimage').src = $('lassoImg').src;
    //$('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+sourceImg+'"/>';
    $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes');?></a> <?php echo $this->translate('or');?> <a href="javascript:void(0);" onclick="lassoCancel();"><?php echo $this->translate('cancel');?></a>';
    $('coordinates').value = 10 + ':' + 10 + ':' + 125 + ':' + 86;
  }

  var lassoEnd = function () {
    $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...');?></div>";
    lassoCrop.destroy();
    $('EditPhoto').submit();
  }

  var lassoCancel = function () {
    $('preview-thumbnail').innerHTML = '<img id="previewimage" src="' + orginalThumbSrc + '"/>';
    $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail');?></a>';
    $('coordinates').value = "";
    lassoCrop.destroy();
  }

  var uploadCoverPhoto = function () {
    $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...')?></div>";
    $('EditPhoto').submit();
    $('Filedata-wrapper').innerHTML = "";
  }
</script>