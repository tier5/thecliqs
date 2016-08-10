<script type="text/javascript">
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
</script>
<div class="uiContextualDialogContent">
	<div class="uibusinessTooltipHovercardStage">
		<div class="label"><?php echo $this->translate('Location:')?></div>
		<?php if (count($this->locations) < 1) : ?>
		<p class="notice"><?php echo $this->translate('No locations available.')?></p>
	    <?php else: ?>
		<div class="location-list">
		    <?php foreach ($this->locations as $location) : ?>
		    <div class="location-item">
		        <?php $url = $this->url(
                    array(
                        'action' => 'direction', 
                        'id' => $location -> getIdentity()
                    ), 
                    'ynbusinesspages_general',
                    true);
                ?>
                <a href="javascript:void(0)" class="get_direction" onclick="checkOpenPopup('<?php echo $url ?>')"><?php echo $location->location?></a>
		    </div>
		    <?php endforeach;?>
		</div>
		<?php endif; ?>
	</div>
	<div class="uibusinessTooltipHovercardFooter">
	</div>
</div>
