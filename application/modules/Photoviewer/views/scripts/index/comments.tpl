<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: comments.tpl 08.02.13 10:28 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {

    var taggerInstance = window.taggerInstance<?php echo $this->subject()->getIdentity();?> = new Tagger('imgPlace_<?php echo $this->subject()->getIdentity();?>', {
      'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
      'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
      'createRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions' : {
        'container' : $('media_photo_next')
      },
      'tagListElement' : 'media_tags_<?php echo $this->subject()->getIdentity();?>',
      'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
      'suggestProto' : 'request.json',
      'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
    });

    taggerInstance.addEvent('begin', function (){
      PhotoViewer.viewer().addClass('tagging_process');
      PhotoViewer.noZoom(1);
    });
    taggerInstance.addEvent('end', function (){
      PhotoViewer.viewer().removeClass('tagging_process');
      PhotoViewer.noZoom(0);
    });

  });

</script>


<?php
  $owner = $this->subject()->getOwner();
?>

<div class="owner_info">
  <div class="thumb">
    <a href="<?php echo $owner->getTitle();?>"><?php echo $this->itemPhoto($owner, 'thumb.icon');?></a>
  </div>
  <div class="poster">
    <a href="<?php echo $owner->getHref();?>"><?php echo $owner->getTitle();?></a>
  </div>
</div>

<div class="album_timestamp">
  <?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
</div>

<div class="photo_info">
  <div class="photo_title"><?php echo $this->subject()->getTitle();?></div>
  <div class="photo_description"><?php echo $this->subject()->getDescription();?></div>
</div>


<div class="wpTags" id="media_tags_<?php echo $this->subject()->getIdentity();?>" style="display: none;" onmouseover="PhotoViewer.noZoom(1)" onmouseout="PhotoViewer.noZoom(0)">
  <?php echo $this->translate('Tagged:') ?>
</div>

<div class="external-options" style="display: none;">
  <?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), '<i class="icon-flag"></i>' . $this->translate("Report"), array('class' => 'smoothbox')); ?>
  <?php if ($this->photo->getType() == 'album_photo'):?>
    <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), '<i class="icon-picture"></i>' .$this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
  <?php endif;?>
  <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('photoviewer.downloadable' , 1)):?>
    <?php echo $this->htmlLink(array('module' => 'photoviewer', 'controller' => 'index', 'action' => 'download', 'photo_id' => $this->photo->getIdentity(), 'isPage' => $this->isPage, 'format' => 'smoothbox'), '<i class="icon-download"></i>' .$this->translate('PHOTOVIEWER_Download this photo')) ?>
  <?php endif;?>
</div>


<?php
  // Edit links
  $edit_url = false;
  $delete_url = false;
  if ($this->photo->getType() == 'album_photo'){
    $edit_url = array('module' => 'album', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'route' => 'default');
    $delete_url = array('module' => 'album', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'route' => 'default');
  } else if ($this->photo->getType() == 'advalbum_photo'){
  }

?>

<div class="external-top">
  <?php if ($this->viewer()->getIdentity()):?>
  <?php if( $this->canTag ): ?>
    <?php echo $this->htmlLink('javascript:void(0);', '<i class="icon-tag onlyicon"></i>', array('class' => "wpbtn wpbtn-inverse", 'onclick' => 'taggerInstance'.$this->subject()->getIdentity().'.begin();', 'title' => $this->translate('Add Tag'))) ?>
  <?php endif; ?>
  <?php if( $this->canEdit && $edit_url): ?>
    <?php echo $this->htmlLink($edit_url, '<i class="icon-wrench onlyicon"></i>', array('class' => 'wpbtn wpbtn-inverse smoothbox', 'title' => $this->translate('Edit'))) ?>
  <?php endif; ?>
  <?php if( $this->canDelete && $delete_url): ?>
    <?php echo $this->htmlLink($delete_url, '<i class="icon-trash onlyicon"></i>', array('class' => 'wpbtn wpbtn-inverse smoothbox', 'title' => $this->translate('Delete'))) ?>
  <?php endif; ?>

  <a href="javascript:void(0);" class="actions wpbtn wpbtn-inverse" onclick="PhotoViewer.toggleOptions();" title="<?php echo $this->translate('PHOTOVIEWER_actions');?>">
    <i class="icon-reorder onlyicon"></i>
    <i class="icon-caret-down right"></i></a>
  <?php endif ?>
</div>

<div class="external-bottom">

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
    <a
      onclick="en4.core.comments.unlike('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>'); j(this).parent().find('.like').show();j(this).hide();"
      class="wpbtn wpbtn-danger unlike"
      href="javascript:void(0);" <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?>style="display: none;"<?php endif;?>>
      <i class="icon-thumbs-down"></i>
      <?php echo $this->translate('Unlike');?>
    </a>

    <a
      onclick="en4.core.comments.like('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>');j(this).parent().find('.unlike').show();j(this).hide();"
      class="wpbtn wpbtn-danger like"
      href="javascript:void(0);" <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>style="display: none;"<?php endif;?>>
      <i class="icon-thumbs-up"></i>
      <?php echo $this->translate('Like');?>
    </a>

    <?php endif;?>

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
      <a onclick="$('comment-form').style.display = ''; $('comment-form').body.focus();" class="wpbtn wpbtn-inverse" href="javascript:void(0);">
        <i class="icon-comment-alt"></i>
        <?php echo $this->translate('Comment');?>
      </a>
    <?php endif;?>

    <?php if ($this->viewer()->getIdentity()):?>
      <a class="wpbtn wpbtn-success smoothbox" href="<?php echo $this->url(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => $this->photo->getType(), 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true)?>">
        <i class="icon-reply"></i>
        <?php echo $this->translate('Share');?>
      </a>
    <?php endif;?>

    <a class="wpbtn wpbtn-inverse wp_init" href="<?php echo $this->photo->getHref();?>">
      <i class="icon-picture"></i>
      <?php echo $this->translate('PHOTOVIEWER_GOTO_PHOTO');?>
    </a>

</div>