<div class="headline">
    <h2>
        <?php echo $this->translate('File Sharing'); ?>
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
<div class="tip">
	<span><?php echo $this->translate("You don't have permission to view this page");?></span>
</div>