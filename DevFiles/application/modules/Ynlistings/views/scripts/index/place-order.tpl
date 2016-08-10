<h2><?php echo $this->translate('Publish Listing');?></h2>

<p><?php echo $this->translate('Each listing on %s costs %s when the listing is published. However, if the listing is not got approval from us due to some reasons,
	this fee does not return back to your account. Contact us directly for these cases.',$this -> layout() -> siteinfo['title'],  $this -> locale()->toCurrency($this->publish_fee, $this->currency) )?></p>

<form method="post" action="<?php echo $this->escape($this->url(array('controller'=>'index','action' => 'update-order'), 'ynlistings_general', true)) ?>" class="global_form" enctype="application/x-www-form-urlencoded">
	<div style="margin-left:3em">
		<div style="background-color: #eee">
			<input type='checkbox' checked="true" disabled="disabled">
			&nbsp;<?php echo $this->translate('Publish Fee: %s',$this -> locale()->toCurrency($this->publish_fee, $this->currency));?> 
		</div>
		<div>
			<input id='feature' name='feature' type='checkbox' value='<?php echo $this->feature_fee;?>'>
			&nbsp;<?php echo $this->translate('Feature Listing Fee: %s',$this -> locale()->toCurrency($this->feature_fee, $this->currency));?>
		</div>
	</div>
	<br/>
	<p style="margin-left:4em">
		<?php echo  $this->translate(array('Your listing will be shown on slideshow Feature Listings for %s day', 'Your listing will be shown on slideshow Feature Listings for %s days', $this->feature_period), $this->feature_period);?>
	</p>
	<br/>
	<h3 style="margin-bottom: 10px"><?php echo $this->translate('Select gateway to place order'); ?></h3>   
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
					
					<input type="hidden" name="id" value="<?php echo $this -> listing -> getIdentity()?>"/>

					<?php  if(($this->allowPayCredit) == '1') :?>
						<button name='type' value='paycredit' style="margin-top: 5px" type="submit" >
							<?php echo $this->translate('Pay with Credit') ?>
						</button>
						<?php echo $this->translate(' or ') ?>
					<?php endif; ?>
					
					<a href="<?php echo $this->url(array('action'=>'index'),'ynlistings_general',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	 window.addEvent('domready', function(){
	      $('feature').addEvent('click', function(event) {
	      	  var isChecked = this.getProperty('checked');
		      if(isChecked)
		      {
		      	 $('feature').getParent().set('styles', {
				    'background-color': '#eee'
				 });
		      }
		      else
		      {
		      	 $('feature').getParent().removeProperty('style');
		      }
	      });
	 });
</script>
