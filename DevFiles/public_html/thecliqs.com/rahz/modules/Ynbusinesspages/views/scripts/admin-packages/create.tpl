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
 
 window.addEvent('domready', function() {
 	$('allow_select_all').addEvent('click', function(e){
 		var value = (this.checked) ? 1 : 0;
 		if(value)
 		{
 			$$('.feature_support').each(function(el) {
 				el.checked = true;
 			});
 		}
 		else
 		{
 			$$('.feature_support').each(function(el) {
 				el.checked = false;
 			});
 		}
 	});
 	
 	$('all_module_support').addEvent('click', function(e){
 		var value = (this.checked) ? 1 : 0;
 		var list = $('modules-element').getElements('input[type="checkbox"]');
 		if(value)
 		{
 			for (i = 0; i < list.length; i++) { 
			    list[i].checked = true;
			}
 		}
 		else
 		{
 			for (i = 0; i < list.length; i++) { 
			    list[i].checked = false;
			}
 		}
 	});
 	
 });
 
 function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
 
</script>
