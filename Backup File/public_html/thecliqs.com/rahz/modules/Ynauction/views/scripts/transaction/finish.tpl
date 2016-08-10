<form method="post" action="<?php echo $this->continue_url; ?>"
      class="global_form store-transaction-form" enctype="application/x-www-form-urlencoded">
  <div>
    <div>

      <?php if( $this->status == 'pending' ): ?>

        <h3>
          <?php echo $this->translate('Payment Pending') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('STORE_PAYMENT_PENDING_THANK_YOU') ?>
        </p>
        <div class="form-elements">
          <div  class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Back to Basket') ?>
            </button>
          </div>
        </div>

      <?php elseif( $this->status == 'completed' ): ?>

        <h3>
          <?php echo $this->translate('Payment Complete') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('Thank you! Your payment has ' .
              'completed successfully.') ?>
        </p>
        <div class="form-elements">
          <div  class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Continue') ?>
            </button>
          </div>
        </div>

      <?php elseif( $this->status == 'half-completed' ): ?>

        <h3>
          <?php echo $this->translate('Payment half completed') ?>
        </h3>
        <p class="form-description">
            <?php echo $this->translate('STORE_Some of the transactions cannot be complete. ' .
                'Please, try to checkout again.') ?>

          <?php if( !empty($this->error) ): ?>
              <br/>
              <?php echo $this->translate($this->error) ?>
            <?php endif; ?>
        </p>
        <div class="form-elements">
          <div  class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Back to Home') ?>
            </button>
          </div>
        </div>

      <?php else: //if( $this->status == 'failed' ): ?>

        <h3>
          <?php echo $this->translate('Payment Failed') ?>
        </h3>
        <p class="form-description">
          <?php if( empty($this->error) ): ?>
            <?php echo $this->translate('Our payment processor has notified ' .
                'us that your payment could not be completed successfully. ' .
                'We suggest that you try again with another credit card ' .
                'or funding source.') ?>
            <?php else: ?>
              <?php echo $this->translate($this->error) ?>
            <?php endif; ?>
        </p>
        <div class="form-elements">
          <div  class="form-wrapper">
            <button type="submit">
              <?php echo $this->translate('Back to Home') ?>
            </button>
          </div>
        </div>

      <?php endif; ?>

    </div>
  </div>
</form>
