<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: wish-list.tpl  26.04.12 13:36 TeaJay $
 * @author     Taalay
 */
?>
<?php echo $this->content()->renderWidget('store.navigation-tabs'); ?>

<div class="layout_left">
  <div id='panel_options'>
    <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render()
    ?>
  </div>
</div>

<div class="layout_middle">
  <h3>
    <?php echo $this->translate('My Wishlist'); ?>
  </h3>
  <br/>
  <?php if ($this->paginator->count() > 0) : ?>
    <div class="he-items" id="stores-items">
      <ul class="he-item-list">
        <?php
          /**
           * @var $item Store_Model_Product
           */

          foreach( $this->paginator as $item ):
        ?>
          <li>
            <div class="he-item-photo">
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
            </div>

            <div class="he-item-options store-item-options" style="text-align: center">
              <?php echo $this->getPriceBlock( $item ); ?>
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
                <h3>
                  <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20))?>
                  <?php if ($item->sponsored) : ?>
                    <img class="icon" src="application/modules/Store/externals/images/sponsored.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>">
                  <?php endif; ?>
                  <?php if ($item->featured) : ?>
                    <img class="icon" src="application/modules/Store/externals/images/featured.png" title="<?php echo $this->translate('STORE_Featured'); ?>">
                  <?php endif; ?>
                </h3>
              </div>
              <div class="rating">
                <?php echo $this->itemRate($item->getType(), $item->getIdentity()); ?>
              </div>
              <div class="clr"></div>
              <div class="he-item-details">
                <span class="float_left"><?php echo $this->translate('STORE_Category') . ': '; ?>&nbsp;</span>
                <span class="float_left"><?php echo ((null !== ($category = $item->getCategory()->category)) ? $this->htmlLink($item->getCategoryHref(array('action'=>'products')), $this->translate($item->getCategory()->category), array()) : ("<i>".$this->translate("Uncategorized")."</i>")); ?></span><br>
                <span class="float_left"><?php echo $this->translate('Posted'); ?>&nbsp;</span>
                <span class="float_left"><?php echo $this->timestamp($item->creation_date); ?>&nbsp;</span>
                <?php if( $item->hasStore() ): ?>
                  <?php echo $this->translate('in %s store', $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank'))); ?>
                <?php endif; ?>
                <br />
              </div>

              <div class="he-item-desc">
                <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription()), 80) ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php else : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('STORE_There are no wished products.');?>
      </span>
    </div>
  <?php endif; ?>
</div>