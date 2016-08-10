<?php if( $this->paginator->getTotalItemCount() > 0 || $this->canUpload ): ?>

<div class="ynbusinesspages-profile-module-header">
    <div class="ynbusinesspages-profile-header-right">
        <!-- Menu Bar -->
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'ynbusinesspages_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
          'tab' => $this->identity,
        ), '<i class="fa fa-list"></i>'.$this->translate('View All Photos'), array(
          'class' => 'buttonlink'
      )) ?>
    <?php endif; ?>
   
      <?php if( $this->canUpload ): ?>
        <?php echo $this->htmlLink(array(
            'route' => 'ynbusinesspages_extended',
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $this->subject()->getGuid(),
            'tab' => $this->identity,
          ), '<i class="fa fa-plus-square"></i>'.$this->translate('Upload Photos'), array(
            'class' => 'buttonlink'
        )) ?>
      <?php endif; ?>
    </div> 
    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_photo", "Photos", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>     
</div>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<script type="text/javascript">
en4.core.runonce.add(function()
{
    var anchor = $('ynbusinesspages_profile_photos').getParent();
    $('ynbusinesspages_profile_photos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_photos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_photos_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('ynbusinesspages_profile_photos_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>
  <ul class="thumbs" id="ynbusinesspages_profile_photos">
    <?php 
    $thumb_photo = 'thumb.normal';
		if(defined('YNRESPONSIVE'))
		{
			$thumb_photo = 'thumb.profile';
		}
    foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </a>
        <p class="thumbs_info">
          <?php echo $this->translate('By');?>
          <?php $owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($photo);
          echo $this->htmlLink($owner ->getHref(), $owner -> getTitle(), array('class' => 'thumbs_author')) ?>
          <br />
          <?php echo $this->timestamp($photo->creation_date) ?>
        </p>
      </li>
    <?php endforeach;?>
  </ul>
<div class="ynbusinesspages-paginator">
  <div id="ynbusinesspages_profile_photos_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="ynbusinesspages_profile_photos_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this business yet.');?>
    </span>
  </div>

<?php endif; ?>