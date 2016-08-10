<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _browse_list.tpl  4/26/12 1:02 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php if ($this->justHtml): ?>
<div>
<?php endif; ?>

<?php if (!$this->details): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate(
        'STORE_You have not set your shipping details yet. Please, follow the following url to set your details: %1s',
        $this->htmlLink(array(
            'route'     => 'store_extended',
            'controller'=> 'panel',
            'action'    => 'address'),
          $this->translate('Shipping Details'), array('style' => 'font-weight: bold'))
      ); ?>
    </span>
  </div>
<?php endif; ?>

  <div class="he-items" style="position: relative;">
    <a id="cart_loader_browse" class="cart_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Credit/externals/images/loader.gif', ''); ?></a>
    <ul class="he-item-list" id="store_cart_items">
      <?php
      /**
       * @var $item    Store_Model_Cartitem
       * @var $product Store_Model_Product
       */
      foreach ($this->paginator as $item): $product = $item->getProduct(); ?>

        <li id="store-cart-product-<?php echo $item->getIdentity(); ?>"
            class="<?php echo ($item->isCheckable($this->via_credits)) ? 'supported' : 'unsupported'; ?>">

          <div class="he-item-photo">
            <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
          </div>

          <div class="he-item-options store-item-options">
            <div class="store-price-block">
              <?php echo $this->getPrice($product); ?> <br/>
              <?php if ($product->type == 'simple'): ?>
                <div class="store_products_count"><?php echo $this->translate('STORE_Quantity') . ': ' . $item->qty; ?></div>
              <?php endif; ?>
              <div class="store-remove-from-cart">
                <?php echo $this->htmlLink(
                  'javascript:store_cart.product.remove(' . $product->getIdentity() . ', ' . $item->getIdentity() . ')',
                  $this->translate('Remove'),
                  array(
                    'class' => 'buttonlink store-cart-item-remove',
                    'id'    => 'remove_cart_' . $product->getIdentity() . '_' . $item->getIdentity()
                  ));
                ?>
                <span class="remove_from_cart_loader"
                      id="remove_loader_<?php echo $product->getIdentity() . '_' . $item->getIdentity()?>"
                      style="display: none"></span>
              </div>
            </div>

            <?php if (!$item->isCheckable()): ?>
              <div class="tip">
                <span style="color: red; font-weight: bold;">
                  <?php echo $this->translate('STORE_Purchasing is not possible. %1s', $this->htmlLink(array(
                    'route'      => 'store_extended',
                    'controller' => 'cart',
                    'action'     => 'see-details',
                    'item_id'    => $item->getIdentity(),
                  ),
                  $this->translate('See Details'),
                  array('class'=> 'smoothbox')));?>
                </span>
              </div>
            <?php endif; ?>
          </div>

          <div class="he-item-info store-item-info">
            <div class="he-item-title">
              <h3><?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 20))?></h3>
            </div>
            <div style="display: inline-block;">
              <div class="rating">
                <?php echo $this->itemRate('store_product', $product->getIdentity()); ?>
              </div>
              <div class="clr"></div>
              <div class="he-item-details">
                <?php echo $this->translate('Posted'); ?>
                <?php echo $this->timestamp($product->creation_date); ?>
                <?php if ($product->hasStore()): ?>
                  <?php echo $this->translate('in %s store', $this->htmlLink($product->getStore()->getHref(), $this->string()->truncate($product->getStore()->getTitle(), 20), array('target' => '_blank', 'title' => $product->getStore()->getTitle()))); ?>
                <?php endif; ?>
                <br>
              </div>
            </div>

            <?php if (count($item->params) > 0 && is_array($item->params)): ?>
              <div class="float_right">
                <table class="float_left">
                  <?php foreach ($item->params as $param): ?>
                    <tr>
                      <td class="label"><strong><?php echo $param['label']; ?><strong>:&nbsp;&nbsp;</td>
                      <td><u><?php echo $param['value']; ?></u></td>
                    </tr>
                  <?php endforeach; ?>
                </table>
              </div>
            <?php endif; ?>
            <br />
            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($product->getDescription())) ?>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="he-pagination">
      <?php echo $this->paginationControl($this->paginator, null, array("pagination/index.tpl", "store")); ?>
    </div>
  </div>

<?php if ($this->justHtml): ?>
</div>
<?php endif; ?>