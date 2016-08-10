<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
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
<?php if(!$this->become):?>
<div class='global_form'>
<?php echo $this->form->render($this); ?>      
</div>
<?php elseif($this->become->approved == 1): ?>
<h3>
<?php echo $this->translate("Congratualtions. You can create auction and publish them for bidding"); ?>
</h3>
<?php else:?>
<h3>
<?php echo $this->translate("Your request has been submitted and waiting to be approved!"); ?>
</h3>
<?php endif; ?>