<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: detail.tpl  17.05.12 13:41 TeaJay $
 * @author     Taalay
 */
?>

<div class="store-transaction-details">

  <h2 class="payment_transaction_detail_headline">
    <span><?php echo $this->translate('Information'); ?></span>
  </h2>

  <dl class="payment_transaction_details">
    <dd><?php echo $this->translate('STORE_Store Name'); ?></dd>
    <dt><?php echo $this->page->__toString(); ?></dt>

    <?php if (null != ($owner = $this->page->getOwner())): ?>
      <dd><?php echo $this->translate('STORE_Owner Name'); ?></dd>
      <dt><?php echo $owner->__toString(); ?></dt>
    <?php endif; ?>

    <dd><?php echo $this->translate('Status'); ?></dd>
    <dt style="font-weight: bold"><?php echo ucfirst($this->request->status); ?></dt>

    <dd><?php echo $this->translate('Requested Amount'); ?></dd>
    <dt><?php echo $this->toCurrency($this->request->amt); ?></dt>

    <dd><?php echo $this->translate('Gateway fee'); ?></dd>
    <dt><?php echo $this->toCurrency($this->request->getGatewayFee()); ?></dt>

    <dd style="font-weight: bold;"><?php echo $this->translate('Gross Amount'); ?></dd>
    <dt style="font-weight: bold;"><?php echo $this->toCurrency($this->request->amt - $this->request->getGatewayFee()); ?></dt>

    <dd><?php echo $this->translate('Requested Date'); ?></dd>
    <dt><?php echo $this->timestamp($this->request->request_date); ?></dt>

    <dd><?php echo $this->translate('Requested Message'); ?></dd>
    <dt><?php echo Engine_String::strip_tags($this->request->request_message); ?></dt>

    <?php if ($this->request->response_date && $this->request->status != 'waiting'): ?>
      <dd><?php echo $this->translate('Response Date'); ?></dd>
      <dt><?php echo $this->timestamp($this->request->response_date); ?></dt>

      <dd><?php echo $this->translate('Response Message'); ?></dd>
      <dt><?php echo Engine_String::strip_tags($this->request->response_message); ?></dt>
    <?php endif; ?>
  </dl>

  <?php if (isset($this->gateway)): ?>
  <h2 class="payment_transaction_detail_headline">
    <span><?php echo $this->translate('Payment'); ?></span>
  </h2>
  <dl class="payment_transaction_details">
    <dd><?php echo $this->translate('Member'); ?></dd>
    <dt><?php echo $this->user->__toString(); ?></dt>

    <dd><?php echo $this->translate('Date'); ?></dd>
    <dt><?php echo $this->timestamp($this->order->payment_date) . ' (' . $this->locale()->toDateTime($this->order->payment_date) . ')'; ?></dt>

    <dd><?php echo $this->translate('Gateway'); ?></dd>
    <dt><?php echo $this->gateway->getTitle(); ?></dt>

    <dd><?php echo $this->translate('Currency'); ?></dd>
    <dt><?php echo $this->order->currency; ?></dt>
  </dl>
  <?php endif; ?>
</div>