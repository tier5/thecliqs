<div class="uiContextualDialogContent">
	<div class="uibusinessTooltipHovercardStage">
		<div class="label"><?php echo $this->translate('Call us:')?></div>
		<?php if (count($this->phones) < 1 || $this->phones[0] == '') : ?>
        <p class="notice"><?php echo $this->translate('No phone number available.')?></p>
        <?php else: ?>
		<div class="phone-list">
		    <?php foreach ($this->phones as $phone) : ?>
		    <div class="phone-item">
		        <a href="tel:<?php echo $phone?>"><?php echo $phone?></a>
		    </div>
		    <?php endforeach;?>
		</div>
		<?php endif;?>
	</div>
	<div class="uibusinessTooltipHovercardFooter">
	</div>
</div>