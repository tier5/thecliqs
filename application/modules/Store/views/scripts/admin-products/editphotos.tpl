<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: editphotos.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php echo $this->getGatewayState(0); ?>

<?php echo $this->render('admin/_productsMenu.tpl'); ?>

<div class="admin_home_middle">
<h3>
  <?php echo $this->translate('Product Photos');?>
</h3>

  <br/>

<h4>
  <?php echo $this->htmlLink($this->product->getHref(), $this->product->getTitle()) ?>
  (<?php echo $this->translate(array('%s photo', '%s photos', $this->product->count()),$this->locale()->toNumber($this->product->count())) ?>)

<div style="float:right;">

  <?php echo $this->htmlLink(
      $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'addphotos', 'product_id'=> $this->product->getIdentity()), 'admin_default', true),
		  $this->translate('Add Photos'),
		  array('class'=>'buttonlink icon_photo_add')
  );?>

</div>
</h4>
<br/>

<?php if($this->paginator->getTotalItemCount() > 0) : ?>
  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
    <?php if($this->success)
      echo "<div>All changes was saved</div>";?>
      <ul class='store_product_admin_editphotos'>
      <?php foreach( $this->paginator as $photo ): ?>
        <li>
          <div class="store_product_admin_editphotos_photo">
            <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
          </div>
          <div class="store_product_admin_editphotos_info">
            <?php
              $key = $photo->getGuid();
              echo $this->form->getSubForm($key)->render($this);
              ?>
            <div class="store_product_admin_editphotos_cover">
              <input type="radio" name="main" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->product->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
            </div>
            <div class="store_product_admin_editphotos_label">
              <label><?php echo $this->translate('Main Photo');?></label>
          </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php echo $this->form->submit->render(); ?>
  </form>

  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
  </div>
<?php else : ?>
  <div>
    <ul class="form-notices"><li><?php echo $this->translate('There is no photos.'); ?></li></ul>
  </div>
<?php endif; ?>
  