<div class="uiContextualDialogContent">
	<div class="uibusinessTooltipHovercardStage">
		<div class="label"><?php echo $this->translate('Visit us:')?></div>
		<?php if (count($this->websites) < 1 || $this->websites[0] == '') : ?>
        <p class="notice"><?php echo $this->translate('No websites available.')?></p>
        <?php else: ?>
		<div class="website-list">
		    <?php foreach ($this->websites as $website) : ?>
		    <div class="website-item">
		        <?php $websiteURl = $website;?>
		        <?php if((strpos($websiteURl,'http://') === false) && (strpos($websiteURl,'https://') === false)) $websiteURl = 'http://'.$websiteURl; ?>
		        <a target="_blank" href="<?php echo $websiteURl?>"><?php echo $this->string()->truncate ($website, 40)?></a>
		    </div>
		    <?php endforeach;?>
		</div>
		<?php endif; ?>
	</div>
	<div class="uibusinessTooltipHovercardFooter">
	</div>
</div>
