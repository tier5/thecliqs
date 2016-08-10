<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="thumbs" id="ynbusinesspages_newest_photos">
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
  
<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this business yet.');?>
    </span>
  </div>

<?php endif; ?>