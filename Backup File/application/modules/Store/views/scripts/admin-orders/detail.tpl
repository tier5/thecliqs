<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: detail.tpl  5/17/12 6:12 PM mt.uulu $
 * @author     Mirlan
 */
?>

<div class="settings">

  <h2 class="payment_transaction_detail_headline" style="padding: 5px 10px">
    <?php echo $this->translate("STORE_Order Details") ?>
  </h2>

  <dl class="payment_transaction_details">
    <dd><?php echo $this->translate('Member') ?></dd>
    <dt>
      <?php if ($this->user && $this->user->getIdentity()): ?>
      <?php echo $this->user->__toString(); ?>
      <?php if (!_ENGINE_ADMIN_NEUTER): ?>
        <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
          $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
        <?php endif; ?>
      <?php else: ?>
      <i><?php echo $this->translate('Deleted Member') ?></i>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Status') ?></dd>
    <dt>
      <?php echo ucfirst($this->item->status); ?>
    </dt>

    <dd><?php echo $this->translate('STORE_Product Title'); ?></dd>
    <?php if ($this->product): ?>
      <dt><?php echo $this->product->__toString(); ?></dt>
    <?php else: ?>
      <dt><?php echo $this->item->name; ?></dt>
    <?php endif; ?>

    <dd><?php echo $this->translate('STORE_Parameters'); ?></dd>
    <dt><?php echo $this->storeApi->params_string($this->item->params); ?></dt>

    <dd><?php echo $this->translate('STORE_Quantity'); ?></dd>
    <dt><?php echo $this->locale()->toNumber($this->item->qty); ?></dt>

    <dd><?php echo $this->translate('STORE_Last Update Date'); ?></dd>
    <dt><?php echo $this->timestamp($this->item->update_date); ?></dt>
  </dl>
  <br/>

  <h2 class="payment_transaction_detail_headline" style="padding: 5px 10px">
    <?php echo $this->translate("STORE_Payment Details") ?>
  </h2>

  <dl class="payment_transaction_details">
    <dd><?php echo $this->translate('Item Amount') ?></dd>
    <dt>
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->item_amt * $this->item->qty); ?></span>
        </span>
      <?php else : ?>
        <?php echo $this->locale()->toCurrency((float)($this->item->item_amt * $this->item->qty), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Tax Amount') ?></dd>
    <dt>
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->tax_amt * $this->item->qty); ?></span>
        </span>
      <?php else : ?>
        <?php echo $this->locale()->toCurrency((float)($this->item->tax_amt * $this->item->qty), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Shipping Amount') ?></dd>
    <dt>
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->shipping_amt * $this->item->qty); ?></span>
        </span>
      <?php else : ?>
        <?php echo $this->locale()->toCurrency((float)($this->item->shipping_amt * $this->item->qty), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Commission Amount') ?></dd>
    <dt>
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits($this->item->commission_amt * $this->item->qty); ?></span>
        </span>
      <?php else : ?>
        &ndash;<?php echo $this->locale()->toCurrency((float)($this->item->commission_amt * $this->item->qty), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Gateway fee') ?></dd>
    <dt>
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits(0); ?></span>
        </span>
      <?php else : ?>
        &ndash;<?php echo $this->locale()->toCurrency((float)($this->item->getGatewayFee()), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <dd><?php echo $this->translate('Total Gross Amount') ?></dd>
    <dt style="font-weight: bold;">
      <?php if ($this->item->via_credits) : ?>
        <span class="store_credit_icon" style="border-top: 1px ridge">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->item->total_amt * $this->item->qty - ($this->item->commission_amt * $this->item->qty)); ?></span>
        </span>
      <?php else : ?>
        <?php echo $this->locale()->toCurrency((float)($this->item->total_amt * $this->item->qty - ($this->item->commission_amt * $this->item->qty + $this->item->getGatewayFee())), $this->item->currency) ?>
        <?php echo $this->translate('(%s)', $this->item->currency) ?>
      <?php endif; ?>
    </dt>

    <?php if($this->gateway): ?>
    <dd><?php echo $this->translate('Gateway') ?></dd>
    <dt><?php echo ucfirst($this->gateway->getTitle()); ?></dt>
    <?php endif; ?>

    <dd><?php echo $this->translate('Date') ?></dd>
    <dt><?php echo $this->timestamp($this->order->payment_date) ?></dt>
  </dl>
  <br/>

  <?php if ( !$this->item->isItemDigital() ): ?>
  <h2 class="payment_transaction_detail_headline" style="padding: 5px 10px">
    <?php echo $this->translate("Shipping Details") ?>
  </h2>

  <dl class="payment_transaction_details">
    <?php if (isset($this->country)): ?>
      <dd><?php echo $this->translate('Country') ?></dd>
      <dt><?php echo $this->country->location; ?></dt>
    <?php endif; ?>
    <?php if (isset($this->state)): ?>
      <dd><?php echo $this->translate('STORE_State/Region') ?></dd>
      <dt><?php echo $this->state->location; ?></dt>
    <?php endif; ?>
      <dd><?php echo $this->translate('City'); ?></dd>
      <dt><?php echo $this->order->shipping_details['city']; ?></dt>

      <dd><?php echo $this->translate('Zip'); ?></dd>
      <dt><?php echo $this->order->shipping_details['zip']?></dt>

      <dd><?php echo $this->translate('STORE_Address Line'); ?></dd>
      <dt><?php echo $this->order->shipping_details['address_line_1']?></dt>
    <?php if (isset($this->order->shipping_details['address_line_2'])): ?>
      <dd><?php echo $this->translate('STORE_Address Line 2'); ?></dd>
      <dt><?php echo $this->order->shipping_details['address_line_2']?></dt>
    <?php endif; ?>
      <dd><?php echo $this->translate('STORE_Phone'); ?></dd>
      <dt><?php echo $this->order->shipping_details['phone_extension'] + '-' + $this->order->shipping_details['phone']; ?></dt>
  </dl>
  <?php endif; ?>
</div>