<?php if (!empty($this->message)) : ?>
	<div class="tip">
		<span>
			<?php echo $this->message; ?>
		</span>
	</div>
<?php return; endif; ?>

<form method="post" action="<?php echo ($this->url()) ?>" class="global_form<?php if ($this->item_id !== null) : ?>_popup<?php endif?>" enctype="application/x-www-form-urlencoded">
	<div>
		<h3>
			<?php echo $this->translate('Confirm Payment') ?>
		</h3>
		
		<div class="form-description">
			<p> <?php echo $this->translate('You are about to subscribe to the item: %1$s', '<strong>' .
            $this->item . '</strong>') ?></p>

			<p><?php echo $this->translate('Are you sure you want to do this? You will be charged: %1$s Credits', '<strong>' . $this -> locale() -> toNumber($this->credits) . '</strong>') ?></p>
				
			<span class="float_left" style="margin-top: 3px;"><?php echo $this->translate('Current Balance'); ?></span>
			
			<span class="payment_credit_icon float_left">
				<span class="payment-credit-price">
					<strong><?php echo $this -> locale() -> toNumber($this->currentBalance); ?></strong>
				</span>
				<?php echo $this->translate('Credits'); ?>
			</span>					
		</div>
			
		<?php if (!$this->enoughCredits) : ?>
			<?php if($this -> item -> status == 'draft') :?>
			<div class="tip">
				<span>
					<?php echo $this->translate('YNJOBPOSTING_CREDIT_WARNING_JOB'); ?>
				</span>
			</div>
			<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('CREDIT_not-enough-credit'); ?>
				</span>
			</div>
			<?php endif;?>
		<?php endif; ?>

		<div class="form-elements">
			<input type='hidden' name='item_id' value="<?php echo $this->item_id?>"/>
			<div class="form-wrapper" id="execute-wrapper">
				<div class="form-element" id="execute-element">
					<?php if ($this->enoughCredits) : ?>
						<button type="submit" id="execute" name="execute"><?php echo $this->translate('Subscribe') ?></button>
						<?php echo $this->translate(' or ') ?>
					<?php endif; ?>
					
					<?php echo $this->htmlLink($this->cancel_url, $this->translate('Select another payment gateway'));?>
				</div>
			</div>
		</div>
	</div>
</form>