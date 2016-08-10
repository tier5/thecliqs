<div class="headline">
  <h2>
    <?php echo $this->translate('Social Media Importer'); ?>
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