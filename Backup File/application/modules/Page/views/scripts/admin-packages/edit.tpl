<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div>
  <?php echo $this->htmlLink(
		array('action' => 'index', 'reset' => false),
		$this->translate('Back to Permission Settings'),
		array('class' => 'icon_admin_back buttonlink'));
	?>
</div>

<br/>

<div class="settings">
  <?php
    echo $this->form->render($this);
  ?>
</div>