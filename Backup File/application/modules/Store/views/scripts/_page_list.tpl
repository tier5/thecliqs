<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_list.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<?php if ($this->products->getTotalItemCount() > 0): ?>
  <?php if ($this->page->getStorePrivacy() && !$this->isGatewayEnabled) : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are currently no ' .
          'enabled payment gateways. You must %1$sadd one%2$s before this ' .
          'page is available.', '<a href="' .
          $this->escape($this->url(array('action' => 'gateway', 'page_id' => $this->subject()->getIdentity()), 'store_settings', true)) .
          '">', '</a>');
        ?>
      </span>
    </div>
  <?php endif; ?>
  <div class="he-items">
    <ul class="he-item-list">
      <?php foreach ($this->products as $item): ?>
        <li>
          <div class="he-item-photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>

          <div class="he-item-options store-item-options" style="text-align: center">
            <?php echo $this->getPriceBlock($item); ?>
            <?php if ($item->type == 'simple'): ?>
              <br />
              <div class="store_products_count">
                <?php echo $this->translate(
                array('%s item available', '%s items available', (int)$item->getQuantity()),
                $this->locale()->toNumber($item->getQuantity())); ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="he-item-info">
            <div class="he-item-title">
              <h3><?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20))?></h3>
            </div>

            <div class="he-item-details">
              <span class="float_left"><?php echo $this->translate('STORE_Category') . ': '; ?>&nbsp;</span>
              <span class="float_left"><?php echo ((null !== ($category = $item->getCategory()->category)) ? $this->htmlLink($item->getCategoryHref(array('action' => 'products')), $this->translate($item->getCategory()->category), array()) : ("<i>".$this->translate("Uncategorized")."</i>")); ?></span><br>
              <span class="float_left"><?php echo $this->translate('Posted').': '; ?>&nbsp;</span>
              <span class="float_left"><?php echo $this->timestamp($item->creation_date); ?></span><br>
            </div>

            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription())) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="he-pagination">
      <?php echo $this->paginationControl($this->products, null, array("pagination/products.tpl","store"), array()); ?>
    </div>
    <?php if ($this->product_id) : ?>
      <div class="view-all">
        <a href="javascript:void(0)" onclick="paging.setPage(1)"><?php echo $this->translate('View All Products')?></a>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('There no products added yet.') . ' ';?>
    <?php if ($this->page->getStorePrivacy()): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'store_products',
          'action' => 'create',
          'page_id' => $this->page->getIdentity()),
        $this->translate('STORE_Create Product.')); ?>
    <?php endif; ?>
  </span>
</div>

<?php endif; ?>