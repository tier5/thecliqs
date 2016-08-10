<div class='accordion_wrapper'>
  <div class="acc_toggler"><div><?php echo $this->parentTitle; ?></div></div>
  <?php
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_accordion.tpl', 'accordion'))
      ->setUlClass('navigation')
      ->render();
  ?>
</div>