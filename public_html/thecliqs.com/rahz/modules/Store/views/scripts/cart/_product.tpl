<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _product.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<?php
/**
 * @var $item Store_Model_Cartitem
 */
  foreach($this->items as $item): $product = $item->getProduct();?>
<?php if ( $product instanceof Store_Model_Product): ?>
<li class="store-cart-item <?php if ( isset($this->item) && $this->item->getIdentity() == $item->getIdentity() ): ?> new <?php endif; ?>" id="cartitem-id-<?php echo $item->getIdentity(); ?>">
	<div class="he-item-photo">
		<?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
	</div>
	<div class="he-item-options">
		<?php echo $this->htmlLink(
		'javascript://',
		'<img class="cart-remove-button" src="application/modules/Store/externals/images/remove.png">',
		array(
			'class'=>'store-cart-item-remove',
			'onclick'=>'store_cart.product.remove(' . $product->getIdentity() . ', ' . $item->getIdentity() . ')',
      'title'=>$this->translate('delete')
		)
	); ?>
	</div>

	<div class="he-item-info">
		<div class="he-item-title">
			<h4><?php echo $this->htmlLink($product->getHref(),$this->string()->truncate($product->getTitle(), 20))?></h4>
		</div>

    <?php echo $this->translate('Price') . ': '; ?>
		<?php echo $this->getPrice($item); ?>
    <p>
      <?php if ( !$product->isDigital() ): ?>
        <?php echo $this->translate('STORE_Quantity') . ': '; ?>
        <?php echo $item->qty?>
      <?php endif; ?>
    </p>
	</div>
</li>
<?php endif; ?>
<?php endforeach; ?>