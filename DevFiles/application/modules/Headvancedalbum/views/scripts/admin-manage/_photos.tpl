<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _photos.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>

<?php if ($this->paginator->getCurrentItemCount() > 0): ?>

<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
  <a class="pagination"
     href="javascript:photos.load_page(<?php echo ($this->paginator->getCurrentPageNumber() - 1); ?>);">
    <?php echo $this->translate("Previous"); ?>
  </a>
  <?php endif; ?>

<?php foreach ($this->paginator as $item): ?>
  <a href="javascript:photos.set_photo_featured(<?php echo $item->getIdentity(); ?>);" class="item"
     title="<?php $item->getTitle(); ?>">
    <span style="position: relative;" class='photo' style='background-image: url()'>
        <img id="featured_<?php echo $item->getIdentity(); ?>" style="position: absolute;" src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Headvancedalbum/externals/images/featured<?php echo $item->he_featured; ?>.png">
        <?php echo $this->itemPhoto($item, 'thumb.normal'); ?>
    </span>
    <div class="clr"></div>
  </a>
  <?php endforeach; ?>

<?php if ($this->paginator->count() > $this->paginator->getCurrentPageNumber()): ?>
  <a class="pagination"
     href="javascript:photos.load_page(<?php echo ($this->paginator->getCurrentPageNumber() + 1); ?>);">
    <?php echo $this->translate("Next"); ?>
  </a>
  <?php endif; ?>

<?php else: ?>
<div class="no_result">
  <?php echo $this->translate('HEADVANCEDALBUM_You have not added any photos yet.'); ?>
</div>
<?php endif; ?>