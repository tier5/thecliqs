<div class="layout_page_ynauction_help">
  <div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
      <div class="generic_layout_container">    
        <div class="headline">
          <h2>
            <?php echo $this->translate('Auction');?>
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
      </div>
    </div>
  </div>

  <div class="generic_layout_container layout_main">
    <div class="generic_layout_container layout_middle">
      <div class="generic_layout_container">
        <div class="tip"><span> <?php echo $this->translate("No item found.") ?> </span></div>
      </div>
    </div>
  </div>
</div>