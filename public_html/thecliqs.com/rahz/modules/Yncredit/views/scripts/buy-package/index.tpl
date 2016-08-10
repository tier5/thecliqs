<?php if( $this->status == 'pending' ): 
	echo $this -> translate("YNCREDIT_PAYMENT_PENDING_PAYMENT")?>
<?php else: ?>
  <form method="post" action="<?php echo $this->escape($this->url(array('action' => 'update-order'), 'yncredit_package', true)) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Pay for Access') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('YNCREDIT_PAYMENT_GATEWAY_SELECT') ?>
        </p>
        <p style="font-weight: bold; padding-left: 10px; padding-top: 15px; padding-bottom: 15px;">
          <?php echo $this -> translate("You choose")." ". $this -> locale()->toCurrency($this -> package->price, $this -> currency). $this -> translate(array(" for %s credit", " for %s credits", $this -> package -> credit), $this -> package -> credit); ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <?php foreach( $this->gateways as $gatewayInfo ):
              $gateway = $gatewayInfo['gateway'];
              $plugin = $gatewayInfo['plugin'];
              $first = ( !isset($first) ? true : false );
              ?>
              <?php if( !$first ): ?>
                <?php echo $this->translate(' or ') ?>
              <?php endif; ?>
              <input type="hidden" name="package" value="<?php echo $this -> package -> getIdentity()?>"/>
              <button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
                <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </form>
<?php endif; ?>