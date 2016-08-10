<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _store_list_edit.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<?php if ($this->products->getTotalItemCount() > 0): ?>
  <div class="he-items">
    <ul class="he-item-list">
      <?php foreach ($this->products as $item): ?>
        <li>
          <div class="he-item-photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>

          <?php if( $item->owner_id == $this->viewer->getIdentity() || $this->page->isOwner($this->viewer)) : ?>
            <div class="he-item-options">
              <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'product_id' => $item->getIdentity()), 'store_products'), $this->translate('STORE_Edit Product'), array(
                'class' => 'buttonlink product_manage'
              )) ?>
              <br>
              <?php echo $this->htmlLink($this->url(array('action' => 'delete', 'product_id' => $item->getIdentity()), 'store_products'), $this->translate('Delete Product'), array(
                'class' => 'buttonlink product_delete smoothbox'
              )) ?>
              <br>
              <?php echo $this->htmlLink($this->url(array('action' => 'copy', 'product_id' => $item->getIdentity()), 'store_products'), $this->translate('STORE_Copy Product'), array(
                'class' => 'buttonlink product_copy'
              )) ?>
              <br>
            </div>
          <?php endif; ?>

          <div class="he-item-info">
            <div class="he-item-title">
              <h3><?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20))?></h3>
              <?php echo $this->getPrice($item); ?>
            </div>

            <div class="he-item-details">
              <span class="float_left"><?php echo $this->translate('STORE_Category') . ': '; ?>&nbsp;</span>
              <span class="float_left"><?php echo ((null !== ($category = $item->getCategory()->category)) ? $this->htmlLink($item->getCategoryHref(array('action' => 'products')), $item->getCategory()->category, array()) : ("<i>".$this->translate("Uncategorized")."</i>")); ?></span><br>
              <span class="float_left"><?php echo $this->translate('Posted').': '; ?>&nbsp;</span>
              <span class="float_left"><?php echo $this->timestamp($item->creation_date); ?></span><br>
              <?php if ($item->type == 'simple'): ?>
                <?php echo $this->translate(
                array('%s item available', '%s items available', (int)$item->getQuantity()),
                $this->locale()->toNumber($item->getQuantity())); ?>
              <?php endif; ?>
            </div>

            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription())) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="he-pagination">
      <?php echo $this->paginationControl($this->products, null, array("pagination/store.tpl","store"), array()); ?>
    </div>
  </div>
<?php else: ?>
	<div class="tip">
    <span>
      <?php echo $this->translate('You do not have any products yet.');?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('Get started by %1$screating%2$s your first product.', '<a href="javascript:store_page.create()">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>