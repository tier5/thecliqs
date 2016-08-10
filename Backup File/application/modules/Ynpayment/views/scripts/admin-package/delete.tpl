<h2>
  <?php echo $this->translate("Advanced Payment Gateway") ?>
</h2>	
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if( $this->status ): ?>

  <?php echo $this->translate('Plan Deleted'); ?>
  <script type="text/javascript">
    parent.window.location.reload();
  </script>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
