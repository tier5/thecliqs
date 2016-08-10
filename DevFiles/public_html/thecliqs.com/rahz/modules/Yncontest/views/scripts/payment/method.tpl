<?php $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');?>
<h2><?php echo $this->translate('Review Your Order');?></h2>
<ul>
	<li><?php echo $this -> translate('Total cost: %s', $this -> locale()->toCurrency($this->total_pay, $this->currency))?></li>
</ul>	
</br>
<h3 style="margin-bottom: 10px"><?php echo $this->translate('Select gateway to place order'); ?></h3>
  <form method="post" action="<?php echo $this->escape($this->url(array('action' => 'update-order'), 'yncontest_payment', true)) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <?php foreach( $this->gateways as $gatewayInfo ):
              $gateway = $gatewayInfo['gateway'];
              $plugin = $gatewayInfo['plugin'];
              $first = ( !isset($first) ? true : false );
              ?>
              <button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
                <?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
              </button>
               	 <?php echo $this->translate(' or ') ?>
            <?php endforeach; ?>
            <input type="hidden" name="id" value="<?php echo $this -> contest -> getIdentity()?>"/>
			   <a href="<?php echo $this->url(array(),'yncontest_general',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
          </div>
        </div>
      </div>
    </div>
  </form>
