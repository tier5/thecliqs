<h3><?php echo $this->translate("Business Theme");?></h3>
<?php echo $this -> form -> render($this);?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		var selectedID = 'package_<?php echo $this -> business -> theme ?>';
		$(selectedID).set('checked', 'true');
	});
</script>