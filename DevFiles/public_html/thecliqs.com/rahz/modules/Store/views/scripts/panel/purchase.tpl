<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: products.tpl  5/18/12 12:17 PM mt.uulu $
 * @author     Mirlan
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
<div class="he-items">

<h3>
  <?php echo $this->htmlLink(array('route' => 'store_panel',
                                   'action'=> 'purchases'), $this->translate('My Purchases')); ?>
  &nbsp;&#187;&nbsp;<?php echo $this->translate('Purchase'); ?>
</h3>

<div class="profile_fields">
  <h4>
    <?php echo $this->translate('STORE_Order Details'); ?>
  </h4>
  <ul>
    <li>
      <span><?php echo $this->translate('Order Key'); ?>:</span>
      <span><?php echo $this->order->ukey ?></span>
    </li>
    <li>
      <span><?php echo $this->translate('Status'); ?>:</span>
      <span><?php echo ucfirst($this->order->status); ?></span>
    </li>
  </ul>

  <h4>
    <?php echo $this->translate('STORE_Payment Details'); ?>
  </h4>
  <ul>
    <li>
      <span><?php echo $this->translate('Gateway'); ?>:</span>
      <span>
        <?php if (isset($this->gateway)): ?>
          <?php echo $this->gateway->getTitle(); ?>
        <?php else: ?>
          <?php echo $this->translate('Unknown Gateway'); ?>
        <?php endif; ?>
      </span>
    </li>

    <li>
      <span><?php echo $this->translate('Payment Date'); ?>:</span>
      <span><?php echo $this->timestamp($this->order->payment_date); ?></span>
    </li>
    <li>
      <span><?php echo $this->translate('Item Amount'); ?>:</span>
      <?php if($this->order->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->item_amt); ?></span>
        </span>
      <?php else : ?>
        <span class="store-price">
          <?php echo $this->locale()->toCurrency($this->order->item_amt, $this->order->currency); ?>
        </span>
      <?php endif; ?>
    </li>
    <li>
      <span><?php echo $this->translate('Tax Amount'); ?>:</span>
      <?php if($this->order->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->tax_amt); ?></span>
        </span>
      <?php else : ?>
        <span class="store-price">
          <?php echo $this->locale()->toCurrency($this->order->tax_amt, $this->order->currency); ?>
        </span>
      <?php endif; ?>
    </li>
    <li>
      <span><?php echo $this->translate('Shipping Amount'); ?>:</span>
      <?php if($this->order->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->shipping_amt); ?></span>
        </span>
      <?php else : ?>
        <span class="store-price">
          <?php echo $this->locale()->toCurrency($this->order->shipping_amt, $this->order->currency); ?>
        </span>
      <?php endif; ?>
    </li>
    <li>
      <span><?php echo $this->translate('Total Amount'); ?>:</span>
      <?php if($this->order->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->total_amt); ?></span>
        </span>
      <?php else : ?>
        <span class="store-price">
          <?php echo $this->locale()->toCurrency($this->order->total_amt, $this->order->currency); ?>
        </span>
      <?php endif; ?>
    </li>
  </ul>

  <?php if (isset($this->country)): ?>
  <h4>
    <?php echo $this->translate('Shipping Details'); ?>
  </h4>
  <ul>
    <?php if (isset($this->country)): ?>
    <li>
      <span><?php echo $this->translate('Country') ?></span>
      <span><?php echo $this->country->location; ?></span>
    </li>
    <?php endif; ?>
    <?php if (isset($this->state)): ?>
    <li>
      <span><?php echo $this->translate('STORE_State/Region') ?></span>
      <span><?php echo $this->state->location; ?></span>
    </li>
    <?php endif; ?>
    <li>
      <span><?php echo $this->translate('City'); ?></span>
      <span><?php echo $this->order->shipping_details['city']; ?></span>
    </li>
    <li>
      <span><?php echo $this->translate('Zip'); ?></span>
      <span><?php echo $this->order->shipping_details['zip']?></span>
    </li>
    <li>
      <span><?php echo $this->translate('STORE_Address Line'); ?></span>
      <span><?php echo $this->order->shipping_details['address_line_1']?></span>
    </li>
    <?php if (isset($this->order->shipping_details['address_line_2'])): ?>
    <li>
      <span><?php echo $this->translate('STORE_Address Line 2'); ?></span>
      <span><?php echo $this->order->shipping_details['address_line_2']?></span>
    </li>
    <?php endif; ?>
    <li>
      <span><?php echo $this->translate('STORE_Phone'); ?></span>
      <span><?php echo $this->order->shipping_details['phone_extension'] + '-' + $this->order->shipping_details['phone']; ?></span>
    </li>
  </ul>
  <?php endif; ?>
</div>


<?php if ($this->items->count() > 0): ?>

<h4>
  <?php echo $this->translate('STORE_Products'); ?>
</h4>

<div class="he-pagination">
  <div>
    <?php $count = $this->items->count() ?>
    <?php echo $this->translate(array("%s product is checked out.", "%s products are checked out.", $count),
    $this->locale()->toNumber($count)) ?>
  </div>
</div>

<br/>

<ul class="he-item-list">

  <?php
  /**
   * @var $item    Store_Model_Orderitem
   * @var $product Store_Model_Product
   */

  foreach ($this->items as $item): $product = $item->getItem();?>
    <li>
      <div class="he-item-photo">
        <?php if ($product == null): ?>
          <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png', null, array('class' => 'thumb_icon')); ?>
        <?php else : ?>
          <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.icon')) ?>
        <?php endif; ?>
      </div>

      <div class="he-item-options store-item-options" style="text-align: center">
        <?php if($item->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($item->total_amt); ?></span>
          </span>
        <?php else : ?>
          <span class="store-price">
            <?php echo $this->locale()->toCurrency((double)$item->total_amt, $item->currency); ?>
          </span>
        <?php endif; ?>
        <br/>
        <?php if ($product->type == 'simple'): ?>
          <?php echo $this->translate('STORE_Quantity') . ': '; ?>
          <?php echo $this->locale()->toNumber($item->qty); ?>
          <br />
        <?php endif; ?>
        <?php echo $this->downloadButton($item); ?>
      </div>

      <div class="he-item-info">
        <div class="he-item-title">
          <h3>
            <?php if ($product == null): ?>
              <?php echo $this->string()->truncate($item->name, 20)?>
            <?php else: ?>
              <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 20))?>
            <?php endif; ?>
          </h3>
        </div>
        <div class="clr"></div>
        <div class="he-item-details">
          <?php if ($product != null && $product->hasStore()): ?>
            <?php echo $this->translate('Store') . ': '; ?>
            <?php echo $this->htmlLink($product->getStore()->getHref(), $product->getStore()->getTitle(), array('target' => '_blank')) ?>
            <br />
          <?php endif; ?>

          <?php echo $this->translate('Status') . ': '; ?>
          <span style="font-weight: bold;"><?php echo $this->translate($item->status); ?></span>
          <br />

          <?php if ($this->api->params_string($item->params)) : ?>
            <?php echo $this->translate('STORE_Parameters') . ': '; ?>
            &nbsp;&nbsp;<p><?php echo $this->api->params_string($item->params); ?></p>
            <br />
          <?php endif; ?>

          <?php if ($product == null): ?>
            <span class="store-item-removed"><?php echo $this->translate('STORE_Removed'); ?></span><br/>
          <?php elseif ($product->isDigital()): ?>
            <?php echo $this->translate('STORE_Left Download Count') . ': '; ?>
            <b>
              <?php if ($this->downloadCount <= 0): ?>  <?php echo $this->translate('STORE_Unlimited') ?>
              <?php ; elseif (($count = ((int)($this->downloadCount - $item->download_count))) <= 0): ?><?php echo $this->translate('STORE_Over') ?>
              <?php ; else: ?><?php echo $this->locale()->toNumber($count); ?><?php endif; ?>
            </b>
          <?php endif; ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php else: ?>
  <div class="tip"><span><?php echo $this->translate("STORE_There are no products."); ?></span></div>
<?php endif; ?>

</div>
</div>