
<form action="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('controller' => 'donors', 'action' => 'index'),'donation_extended',true); ?>" class="global_form donation-transaction-form">
  <div>
    <div>
      <?php if ($this->status == 'pending'): ?>
        <h3>
          <?php echo $this->translate('DONATION_Payment Pending') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('DONATION_PAYMENT_PENDING_DESCRIPTION') ?>
        </p>
      <?php  elseif(in_array($this->status, array('completed','complete'))): ?>
        <h3>
          <?php echo $this->translate('DONATION_Payment Complete') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('DONATION_PAYMENT_COMPLETED_DESCRIPTION') ?>
        </p>
      <?php else: ?>
        <h3>
          <?php echo $this->translate('DONATION_Payment Failed') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('DONATION_PAYMENT_FAILED_DESCRIPTION') ?>
        </p>
      <?php endif; ?>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('DONATION_Go to Top Donors List') ?>
          </button>
        </div>
      </div>
    </div>
  </div>
</form>