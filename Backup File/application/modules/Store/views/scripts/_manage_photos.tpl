<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _manage_photos.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

<form id="store-album-photo-manage" action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <input type="hidden" id="product_id" name="product_id">
  <?php echo $this->form->product_id; ?>
  <ul class='store_product_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="store_product_editphotos_photo">
          <?php echo $this->htmlLink("javascript:he_show_image('{$photo->getPhotoUrl()}')", $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="store_product_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
    <div class="store_product_editphotos_cover">
            <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->product->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
    </div>
    <div class="store_product_editphotos_label">
            <label><?php echo $this->translate('Main Photo');?></label>
    </div>
        </div>
      </li>
    <?php endforeach; ?>

    <?php echo $this->form->submit->render(); ?>
</form>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>

<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('No photos in this product.');?>
  </span>
</div>

<?php endif; ?>