<?php if ($this->notAuth) :?>
<div class="ynmusic-download-notice">
	<div class="message">
		<?php echo $this->translate('This album is empty or you don\'t have permission to download.');?>
	</div>
</div>
<script type="text/javascript">
	window.addEvent('domready', function() {
		setTimeout(function(){
			parent.Smoothbox.close();
		}, 2000);
	});
</script>
<?php else:?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		window.open('<?php echo $this->url(array('auth'=>true))?>','_blank');
		parent.Smoothbox.close();
	});
</script>	
<?php endif;?>