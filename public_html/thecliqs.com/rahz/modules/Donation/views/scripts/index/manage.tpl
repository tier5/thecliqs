<div class="headline">
  <h2>
    <?php echo $this->translate('Donations');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
    ?>
  </div>
  <?php endif; ?>
</div>

<div class="layout_right">
  <?php echo $this->content()->renderWidget('donation.donation-search'); ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
      // Render the menu
       echo $this->navigation()
        ->menu()
        ->setContainer($this->quickNavigation)
        ->render();
      ?>
    </div>
  <?php endif; ?>

</div>
<div class="layout_middle">
  <?php echo $this->render('manage_list.tpl'); ?>
</div>