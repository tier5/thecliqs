<div class="global_form_popup">
	<h3><?php echo $this -> translate("Send Credits")?> </h3>
	<div style="text-align: center; padding-top: 5px;">
		<?php if($this -> error_msg):?>
			<div class="tip">
				<span>
					<?php echo $this -> error_msg?>
				</span>
			</div>
			<button onclick="parent.Smoothbox.close()"><?php echo $this -> translate("Close")?></button>
		<?php else:?>
			<form class="global_form_popup" method="post">
				<div style="margin-bottom: 10px"><?php echo $this -> confirm;?></div>
				<button type="submit"><?php echo $this -> translate("OK")?></button>
				<button onclick="parent.Smoothbox.close()"><?php echo $this -> translate("Cancel")?></button>
			</form>
		<?php endif;?>
	</div>
</div>