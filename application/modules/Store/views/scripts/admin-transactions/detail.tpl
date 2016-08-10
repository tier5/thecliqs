<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: detail.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>

<div class="settings">

  <h2 class="payment_transaction_detail_headline">
    <?php echo $this->translate("Transaction Details") ?>
  </h2>

  <?php if ($this->transaction) : ?>
  <dl class="payment_transaction_details">
    <dd>
      <?php echo $this->translate('Member') ?>
    </dd>
    <dt>
      <?php if ($this->user && $this->user->getIdentity()): ?>
        <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>
      <?php if (!_ENGINE_ADMIN_NEUTER): ?>
        <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
          $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
        <?php endif; ?>
      <?php else: ?>
        <i><?php echo $this->translate('Deleted Member') ?></i>
      <?php endif; ?>
    </dt>

    <dd>
      <?php echo $this->translate('Payment Gateway') ?>
    </dd>
    <dt>
      <?php if ($this->gateway): ?>
        <?php echo $this->translate($this->gateway->title) ?>
      <?php else: ?>
        <i><?php echo $this->translate('Unknown Gateway') ?></i>
      <?php endif; ?>
    </dt>

    <dd>
      <?php echo $this->translate('Payment State') ?>
    </dd>
    <dt>
      <?php echo $this->translate(ucfirst($this->transaction->state)) ?>
    </dt>

    <dd>
      <?php echo $this->translate('Payment Amount') ?>
    </dd>
    <dt>
      <?php if ($this->transaction->via_credits) : ?>
        <span class="store_credit_icon">
          <span class="store-credit-price"><?php echo $this->api->getCredits($this->transaction->amt); ?></span>
        </span>
      <?php else : ?>
        <?php echo $this->locale()->toCurrency($this->transaction->amt, $this->transaction->currency) ?>
        <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
      <?php endif; ?>
    </dt>

      <dd>
        <?php echo $this->translate('Gateway fee') ?>
      </dd>
      <dt>
        <?php if ($this->transaction->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits(0); ?></span>
          </span>
        <?php else : ?>
          &ndash;<?php echo $this->locale()->toCurrency($this->transaction->gateway_fee, $this->transaction->currency) ?>
          <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
        <?php endif; ?>
      </dt>

      <dd><?php echo $this->translate('Commission Amount') ?></dd>
      <dt>
        <?php if ($this->transaction->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits($this->order->commission_amt); ?></span>
          </span>
        <?php else : ?>
          &ndash;<?php echo $this->locale()->toCurrency((float)($this->order->commission_amt), $this->transaction->currency) ?>
          <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
        <?php endif; ?>
      </dt>

    <?php if ($this->type == 'products'): ?>
      <?php if ($this->items && $this->items->count() > 0): ?>
        <dd>
          <?php echo $this->translate('STORE_Products') . ' (' . $this->items->count() . ')'; ?>
        </dd>
        <dt>
        <ul class="he-item-list admin-transaction-products-list" style="display: inline-block;">
          <?php
          /**
           * @var $item    Store_Model_Orderitem
           * @var $product Store_Model_Product
           */

          foreach ($this->items as $item): ?>
            <li>
              <?php if (null == ($product = $item->getItem())): ?>
                <?php echo $this->string()->truncate($item->getTitle(), 30) ?>
              <?php else: ?>
                <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($item->getTitle(), 30)) ?>
              <?php endif; ?>
              <?php if (!$item->isItemDigital()): ?>
                <div>
                  <span><?php echo $this->translate('STORE_Quantity') . ':'; ?></span>
                  <span><?php echo $this->locale()->toNumber($item->qty); ?></span>
                </div>
              <?php endif; ?>
              <div>
                <span><?php echo $this->translate('Total Amount') . ':'; ?></span>
                <?php if ($item->via_credits) : ?>
                  <span class="store_credit_icon">
                    <span class="store-credit-price"><?php echo $this->api->getCredits($item->total_amt); ?></span>
                  </span>
                <?php else : ?>
                  <span><?php echo $this->locale()->toCurrency($item->total_amt, $item->currency); ?></span>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        </dt>
      <?php endif; ?>
    <?php endif; ?>

      <dd><?php echo $this->translate('Total Gross Amount') ?></dd>
      <dt style="font-weight: bold">
        <?php if ($this->transaction->via_credits) : ?>
          <span class="store_credit_icon">
            <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->getStoreInfo($this->page_id, 'total_amt') - ($this->order->commission_amt)); ?></span>
          </span>
        <?php else : ?>
          <?php echo $this->locale()->toCurrency((float)($this->order->getStoreInfo($this->page_id, 'total_amt') - ($this->order->commission_amt + $this->transaction->gateway_fee)), $this->transaction->currency) ?>
          <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
        <?php endif; ?>
      </dt>

    <dd>
      <?php echo $this->translate('Gateway Transaction ID') ?>
    </dd>
    <dt>
      <?php if (!empty($this->transaction->gateway_transaction_id) && !$item->via_credits): ?>
      <?php echo $this->htmlLink(array(
        'route'          => 'admin_default',
        'module'         => 'store',
        'controller'     => 'transactions',
        'action'         => 'detail-transaction',
        'transaction_id' => $this->transaction->transaction_id,
      ), $this->transaction->gateway_transaction_id, array(
        //'class' => 'smoothbox',
        'target' => '_blank',
      )) ?>
      <?php else: ?>
      -
      <?php endif; ?>
    </dt>
    <dd>
      <?php echo $this->translate('Date') ?>
    </dd>
    <dt>
      <?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
    </dt>

    <?php if (isset($this->country)): ?>
    <dd style="font-weight: bold">
      <br/>
      <?php echo $this->translate("Shipping Details") ?>
    </dd>
    <dt><br/>&nbsp; </dt>
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
    <?php endif; ?>
  </dl>
  <?php else : ?>
    <?php echo $this->translate('Invalid Data'); ?>
  <?php endif; ?>
</div>