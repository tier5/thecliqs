<?php
    echo $this->form->render($this);
?>
<script type="text/javascript">
	var submitExport = function()
	{
		setTimeout(function()
		{
			parent.Smoothbox.close();
		}, 1000);
	}
</script>