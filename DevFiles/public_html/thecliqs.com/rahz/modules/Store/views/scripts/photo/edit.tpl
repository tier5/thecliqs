<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-09-19 17:07:11 taalay $
 * @author     Taalay
 */

?>

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
        <?php echo $this->htmlLink($this->url(array('controller' => 'photo', 'action' => 'add', 'product_id' => $this->product->getIdentity()), 'store_extended', true), $this->translate('Add Photos'), array(
          'class' => 'buttonlink product_photos_new',
          'id' => 'store_product_addphotos',
        )) ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<div style="overflow: hidden;">
<h3>
  <?php echo $this->htmlLink($this->product->getHref(), $this->product->getTitle()) ?>
  (<?php echo $this->translate(array('%s photo', '%s photos', $this->product->count()),$this->locale()->toNumber($this->product->count())) ?>)
</h3>
<?php if($this->paginator->getTotalItemCount() > 0) : ?>
  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
    <?php if($this->success)
      echo "<div>All changes was saved</div>";?>
    <?php echo $this->form->product_id; ?>
    <ul class='store_product_editphotos'>
      <?php foreach( $this->paginator as $photo ): ?>
        <li>
          <div class="store_product_editphotos_photo">
            <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
          </div>
          <div class="store_product_editphotos_info">
            <?php
              $key = $photo->getGuid();
              echo $this->form->getSubForm($key)->render($this);
            ?>
            <div class="store_product_editphotos_cover">
              <input type="radio" name="main" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->product->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
            </div>
            <div class="store_product_editphotos_label">
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
  