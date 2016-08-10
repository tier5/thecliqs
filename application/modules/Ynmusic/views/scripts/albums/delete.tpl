<?php if ($this->success) :?>
<form class="global_form_popup">
<div class="delete-message"><?php echo $this->translate('Album deleted.')?></div>
</form>
<script>
	window.addEvent('domready', function() {
		setTimeout(function() {
			parent.redirectToManagePage();
      	}, 1000);
	});
</script>
<?php else:?>
<?php
    echo $this->form->render($this)
?>
<?php endif;?>