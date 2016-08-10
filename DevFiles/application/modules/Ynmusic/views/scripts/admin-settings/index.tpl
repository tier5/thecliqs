<h2><?php echo $this->translate("YouNet Music Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
	<?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
<div class='settings'>
	<ul id="form-error-placement" style="display:none" class="form-errors"><li><ul class="errors"><li><?php  echo $this -> translate("Please drag the placement switch to green color") ?></li></ul></li></ul>
  <?php echo $this->form->render($this); ?>
</div>
</div>

<input type="hidden" id ="color" name="color" value="green"/>

<script type="text/javascript">
	function checkColor() {
		var color = jQuery('#color').val();
		if(color == "red" && (jQuery('#ynmusic_player_display-0:checked').length > 0)) {
			jQuery("#form-error-placement").css('display', 'block');
			return;
		} else {
			jQuery('#ynmusic_settings_form').submit();
		}
	}
</script>
