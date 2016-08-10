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
    <?php echo $this->form->render($this); ?>
    </div>
</div>

<script>
window.addEvent('domready', function() {
	$$('.global_form').addEvent('submit', function() {
		var btn = this.getElement('#buttons-wrapper');
		if (btn) {
			btn.hide();
		}
	})
});
</script>