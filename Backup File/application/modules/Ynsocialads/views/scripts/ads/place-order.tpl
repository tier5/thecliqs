<h2><?php echo $this->translate('Review Your Ad');?></h2>
<ul class="review_your_ads">
	<li>
		<div class='payment_label'><?php echo $this->translate('Ads')?></div>
		<div class='payment_value'><?php echo $this->ad->name?></div>
	</li>
	<li>
		<div class='payment_label'><?php echo $this->translate('Preview')?></div>
		<div class=''>
			<div class="ynsocial_ads" style="width: 300px;">
				<div class="ynsocial_ads_content">
					<div class="ynsocial_ads_item">
						<span class="ynsocial_ads_setting">
						</span>
						<?php if ($this->ad->ad_type != 'banner') :?>
							<a class="ynsocial_ads_cont_title" href="<?php echo $this->ad->getLinkUpdateStats()?>/preview/1">
								<?php echo $this->translate($this->ad->name);?>
							</a>
						<?php endif;?>
						<a class="ynsocial_ads_cont_image" href="<?php echo $this->ad->getLinkUpdateStats()?>/preview/1">
							<img src="<?php echo $this->ad -> getPhotoUrl('thumb.normal') ?>"/>
						</a>
						<?php if($this->ad->description) :?>
							<div class="ynsocial_ads_cont"><?php echo $this->translate($this->ad->description)?></div>
						<?php endif;?>
					</div>	
				</div>
			</div>
		</div>
	</li>
	<li>
		<div class='payment_label'><?php echo $this->translate('Campaign')?></div>
		<div class='payment_value'><?php echo $this->ad->getCampaignName()?> </div>
	</li>
	<li>
		<div class='payment_label'><?php echo $this->translate('Package')?></div>
		 <a class='smoothbox' href="<?php echo $this->url(array('action' => 'view-package','id' => $this->ad->getPackage()->getIdentity())) ?>">
			<?php echo $this->ad->getPackage()->title?>
		</a>
	</li>
	<li>
		<div class='payment_label'><?php echo $this->translate('Your Goal')?></div>
		<div class='payment_value' style="color: Red"><?php echo $this->ad->benefit_total." ".$this->package->benefit_type."s"?></div>
	</li>
	<li>
		<div class='payment_label'><?php echo $this->translate('Total price')?></div>
		<div class='payment_value' style="color: Red"><?php echo $this->locale() -> toCurrency($this->total_pay, $this->package->currency)?> </div>
	</li>
	<?php
		$target = $this->ad->getTarget();
        $target = $target->toArray();
        $target['birthdate'] = array(
            'min' => $target['age_from'],
            'max' => $target['age_to']
        );
        $audiences = Engine_Api::_()->ynsocialads()->getAudiences($target);
        $audiences = $audiences;
	?>
	<li>
		<div class='payment_label'><?php echo $this->translate('Audiences')?></div>
		<div class='payment_value'><?php echo count($audiences)." ".$this->translate('People');?> </div>
	</li>
</ul>
</br>
<h3 style="margin-bottom: 10px"><?php echo $this->translate('Select gateway to place order'); ?></h3>
  <form method="post" action="<?php echo $this->escape($this->url(array('controller'=>'ads','action' => 'update-order'), 'ynsocialads_extended', true)) ?>"
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
              <input type="hidden" name="package" value="<?php echo $this -> package -> getIdentity()?>"/>
              <button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
                <?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
              </button>
               	 <?php echo $this->translate(' or ') ?>
            <?php endforeach; ?>
            <input type="hidden" name="id" value="<?php echo $this -> ad -> getIdentity()?>"/>
              <?php if(($this->allowPayLater) == '1') :?>
					  <button name='type' value='paylater' style="margin-top: 5px" type="submit" >
					      <?php echo $this->translate('Pay Later') ?>
					  </button>
					  <?php echo $this->translate(' or ') ?>
			  <?php endif; ?>
			  <?php  if(($this->allowPayVirtual) == '1') :?>
					  <button name='type' value='payvirtual' style="margin-top: 5px" type="submit" >
		                <?php echo $this->translate('Pay with Virtual Money') ?>
					  </button>
					  <?php echo $this->translate(' or ') ?>
			  <?php endif; ?>
			  <?php  if(($this->allowPayCredit) == '1') :?>
					  <button name='type' value='paycredit' style="margin-top: 5px" type="submit" >
		                <?php echo $this->translate('Pay with Credit') ?>
					  </button>
					   <?php echo $this->translate(' or ') ?>
			  <?php endif; ?>
			   <a href="<?php echo $this->url(array('action'=>'index'),'ynsocialads_ads',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
          </div>
        </div>
      </div>
    </div>
  </form>
