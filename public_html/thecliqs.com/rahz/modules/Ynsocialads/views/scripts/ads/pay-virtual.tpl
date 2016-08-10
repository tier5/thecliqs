<div class="headline">
  <h2>
    <?php echo $this->translate('Virtual Money') ?>
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
<form method="post" action="<?php echo ($this->url()) ?>"
      class="global_form<?php if ($this->item_id !== null) : ?>_popup<?php endif?>" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h3>
        <?php echo $this->translate('Confirm Payment') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('You are about to subscribe to the item: %1$s', '<strong>' .
            $this->item . '</strong>') ?>
        <br />
        <?php echo $this->translate('Are you sure you want to do this? You will be charged:') ?>
            <strong>  
            	<?php echo $this->locale() -> toCurrency($this->total_pay, $this->package->currency); ?>
            </strong>
        <br />
        <span class="float_left" style="margin-top: 3px;"><?php echo $this->translate('Current Virtual Balance'); ?>:&nbsp;</span>
        <span class="payment_credit_icon float_left">
          <span class="payment-credit-price"><strong><?php echo $this->locale() -> toCurrency($this->currentVirtualBalance, $this->package->currency); ?></strong></span>
        </span>
        <br />
      </p>
      <div class="form-elements">
      	<input type='hidden' name='item_id' value="<?php echo $this->item_id?>"/>
      	 <div class="form-wrapper" id="execute-wrapper">
          <div class="form-element" id="execute-element">
              <button type="submit" id="execute" name="execute"><?php echo $this->translate('Subscribe') ?></button>
              <?php echo $this->translate(' or ') ?>
              <?php echo $this->htmlLink($this->cancel_url, $this->translate('cancel'));?>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>