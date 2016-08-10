<?php echo $this->content()->renderWidget('yncontest.main-menu') ?>
<?php echo $this->partial('_contest_menu.tpl', array(
		'contest'=>$this->contest_id,
		'create'=>false,		
		));?>
<?php echo $this->form->render($this);?>


<style type="text/css">
input[type="checkbox"] + label, input[type="radio"] + label {
	cursor: default;
}
</style>
<script type = "text/javascript">
window.addEvent('domready',function(){

	
	if ($('age_limit-limit').checked == true) {		
		$('limit-wrapper').show();		
	}
	else {
		$('limit-wrapper').hide();	
	}	
});
function showAgeLimit(){
	if ($('age_limit-limit').checked == true) {
		$('limit-wrapper').show();	
	}
	else{
		$('limit-wrapper').hide();	
	}
}

	
}
</script>