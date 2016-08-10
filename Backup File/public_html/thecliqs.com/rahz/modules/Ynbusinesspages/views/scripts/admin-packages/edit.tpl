<h2><?php echo $this->translate("Businsess Page Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>
<br />

<div class="clear">
	<div class="settings">
	<?php
	    echo $this->form->render($this)
	?>
	</div>
</div>

<script type="text/javascript">
 
 function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
 
</script>
