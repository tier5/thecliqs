<div class="headline">
  <h2>
    <?php echo $this->translate('Credits') ?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php if (!empty($this->message)) : ?>
  <div class="tip">
    <span>
      <?php echo $this->message; ?>
    </span>
  </div>
<?php return; endif; ?>

<form method="post" action="<?php echo $this->escape($this->url()) ?>?package_id=<?php echo $this->package->package_id ?>"
      class="global_form<?php if ($this->package_id !== null) : ?>_popup<?php endif?>" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h3>
        <?php echo $this->translate('Confirm Subscription') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('You are about to subscribe to the plan: %1$s', '<strong>' .
            $this->translate($this->package->title) . '</strong>') ?>
        <br />
        <?php echo $this->translate('Are you sure you want to do this? You will be charged: %1$s',
            '<strong>' . $this->packageDescription
            . '</strong>') ?>
        <br />
        <span class="float_left" style="margin-top: 3px;"><?php echo $this->translate('Current Balance'); ?>:&nbsp;</span>
        <span class="payment_credit_icon float_left">
          <span class="payment-credit-price"><?php echo $this -> locale() -> toNumber($this->currentBalance); ?></span>
          <?php echo $this->translate('Credits'); ?>
        </span>
        <br />
      </p>
      <?php if (!$this->enoughCredits) : ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('CREDIT_not-enough-credit'); ?>
          </span>
        </div>
      <?php else : ?>
        <p style="padding: 0.7em">
          <?php echo $this->translate('If yes, click the button below and you will be taken to a payment page. When you have completed your payment, please remember to click the button that takes you back to our site.') ?>
        </p>
        <p style="padding: 0.7em">
          <?php echo $this->translate('Please note that no refund will be provided for any unused portion of your current plan.') ?>
        </p>
      <?php endif; ?>
      <div class="form-elements">
        <div class="form-wrapper" id="execute-wrapper">
          <div class="form-element" id="execute-element">
            <?php if ($this->enoughCredits) : ?>
              <button type="submit" id="execute" name="execute"><?php echo $this->translate('Subscribe') ?></button>
              <?php echo $this->translate(' or ') ?>
            <?php endif; ?>
            <?php if (isset($this->cancel_url)) : ?>
              <?php echo $this->htmlLink($this->cancel_url, $this->translate('Cancel'));?>
            <?php else : ?>
              <?php echo $this->htmlLink('javascript://', $this->translate('Cancel'), array('onclick' => 'Smoothbox.close()')) ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" name="gateway_id" id="gateway_id" value="" />
</form>