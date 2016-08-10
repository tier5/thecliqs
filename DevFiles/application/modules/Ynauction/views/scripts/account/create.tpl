<div class="headline">
  <h2>
    <?php echo $this->translate(' Auction');?>
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
<div class='global_form'>
<?php echo $this->form->render($this); ?>      
</div>