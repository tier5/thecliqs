<style>
	.settings .form-element .description,
	.settings .form-description{
		max-width: 100%;
	}
</style>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
</script>
<?php if( $this->form ): ?>
  <div class='clear'>
    <div class='settings'>

      <?php echo $this->form->render($this); ?>

    </div>
  </div>
<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Your changes have been saved.") ?></div>

<?php endif; ?>