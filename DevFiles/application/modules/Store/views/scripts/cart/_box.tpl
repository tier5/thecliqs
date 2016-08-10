<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _box.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div class='store-cart-button' id='store-cart-button'>
  <div>
    <div>
      <div class="store-counters"><?php echo $this->totalCount; ?></div>
      <div id='store-cart-slider'></div>
    </div>
  </div>
</div>

<div class="store-cart-body" id="store-cart-items-slider">
  <ul id='store-cart-items' class="he-item-list">
    <?php if ($this->items != null) : ?>
      <?php echo $this->partial(
        'cart/_product.tpl',
        'store',
        array(
          'items' => $this->items,
          'currency' => $this->currency)
    ); ?>
    <?php endif; ?>

    <li class="store-cart-empty<?php if ($this->totalCount > 0): ?> hidden <?php endif; ?>" id="store-cart-empty">
      <?php echo $this->translate('STORE_Your cart is empty'); ?>
    </li>
  </ul>

  <div class="store-cart-checkout<?php if ($this->totalCount <= 0): ?> hidden <?php endif; ?>" id="store-cart-checkout">
    <div><?php echo $this->translate('Total'); ?>:
      <span class="store-price">
        <span
          class="store-prices"><?php echo $this->locale()->toCurrency((double)$this->totalPrice, $this->currency); ?></span>
      </span>
    </div>
    <button onclick="store_cart.checkout();">
      <span class="store-checkout-button"><?php echo $this->translate('STORE_Checkout'); ?> </span>
    </button>
  </div>
  <div id='store-cart-temp' class="hidden"></div>
</div>