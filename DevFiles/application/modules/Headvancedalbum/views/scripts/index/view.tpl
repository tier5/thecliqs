<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

?>

<h2>
  <?php echo $this->translate('%1$s\'s Album: %2$s',
    $this->album->getOwner()->__toString(),
    ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
  ); ?>
</h2>

<?php if( '' != trim($this->album->getDescription()) ): ?>
  <p>
    <?php echo $this->album->getDescription() ?>
  </p>
  <br />
<?php endif ?>

<?php if( $this->mine || $this->canEdit ): ?>
  <div class="album_options">
    <?php echo $this->htmlLink(array('route' => 'headvancedalbum_add_photos', 'album_id' => $this->album->album_id), $this->translate('Add More Photos'), array(
      'class' => 'buttonlink icon_photos_new'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'editphotos', 'album_id' => $this->album->album_id), $this->translate('Manage Photos'), array(
      'class' => 'buttonlink icon_photos_manage'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'edit', 'album_id' => $this->album->album_id), $this->translate('Edit Settings'), array(
      'class' => 'buttonlink icon_photos_settings'
    )) ?>
    <?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'delete', 'album_id' => $this->album->album_id, 'format' => 'smoothbox'), $this->translate('Delete Album'), array(
      'class' => 'buttonlink smoothbox icon_photos_delete'
    )) ?>

    <a href="<?php echo $this->baseUrl();?>/album/album/view/album_id/<?php echo $this->album->album_id;?>"
       class="buttonlink icon_photos_sort">
      <?php echo $this->translate('HEADVANCEDALBUM_SORT');?>
    </a>

  </div>
<?php endif;?>


<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Headvancedalbum/externals/scripts/hap.js');
?>
<script type="text/javascript">

  function viewMore()
  {
    hapPhotos.loadMore(function (res) {
      if (!res.is_next) {
        $('viewMore').hide();
      }
    });
  }

  en4.core.runonce.add(function (){
    (new HapInstance({
      request_url: '<?php echo $this->url();?>?format=json',
      loading_on_scroll: false
    }));
  });
</script>

<div class="hapLoader" id="hapLoader"></div>
<div class="hapLoader" id="hapBuildLoader"></div>

<div class="layout_middle">

  <ul class="hapPhotos" id="hapPhotos">
    <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');?>
  </ul>

  <?php if ($this->is_next):?>
    <a href="javascript:void(0);" onclick="viewMore();" id="viewMore"><?php echo $this->translate('HEADVANCEDALBUM_SEE_MORE');?></a>
  <?php endif;?>

</div>


<br/>


