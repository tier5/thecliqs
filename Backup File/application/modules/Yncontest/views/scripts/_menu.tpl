<div class="headline">
  <h2>
    <?php echo $this->translate('Contest');?>
  </h2>
  <?php 
	$navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynidea_main');  
  if( count($navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>