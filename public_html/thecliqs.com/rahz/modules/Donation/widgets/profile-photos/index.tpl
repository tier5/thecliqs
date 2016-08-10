<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       13:37
 */?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<?php
  /**
  * @var $photo Donation_Model_Photo
   */
 ?>
<ul class="thumbs">
    <?php foreach( $this->paginator as $photo ): ?>
    <li>
        <a class="thumbs_photo" href="javascript://" onclick="he_show_image('<?php echo $photo->getPhotoUrl();?>')">
            <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
    </li>
    <?php endforeach;?>
</ul>
<?php echo $this->paginationControl($this->paginator); ?>
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this donation yet.');?>
    </span>
</div>

<?php endif; ?>
