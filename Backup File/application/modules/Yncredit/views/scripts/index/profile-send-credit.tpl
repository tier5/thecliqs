<div class="global_form_popup">
	<h3><?php echo $this -> translate("Send Credits")?> </h3>
	<?php if($this -> confirm || $this -> error_msg):?>
		<div style="text-align: center; padding-top: 5px;">
			<?php if($this -> error_msg):?>
				<div class="tip">
					<span>
						<?php echo $this -> error_msg?>
					</span>
				</div>
				<button onclick="history.go(-1);"><?php echo $this -> translate("OK")?></button>
			<?php else:?>
				<form class="global_form_popup" method="post">
					<div style="margin-bottom: 10px"><?php echo $this -> confirm;?></div>
					<input type="hidden" name = 'credit' value="<?php echo $this -> credit?>" />
					<button name="confirm" type="submit"><?php echo $this -> translate("OK")?></button>
					<button onclick="parent.Smoothbox.close()"><?php echo $this -> translate("Cancel")?></button>
				</form>
			<?php endif;?>
		</div>
	<?php else:?>
		<form method="post" id="send_credit_form" class="global_form_popup">
		    <div class=""><?php echo $this -> translate("Need never be out of touch with friends? You can send credit to your friends when they are running low"); ?></div>
		    <br/>
		    <div class="form-wrapper">
		    	<div class="form-label">
		    		<label class="optional"><?php echo $this -> message?></label>
		    	</div>
				<div id="to-element" class="form-element">
					<span id="tospan2" class="tag tag_user"><?php echo $this -> user?></span>
				</div>
			</div>
			<?php if(!$this -> fail):?>
			<br/>
		    <div id="credit-wrapper" class="form-wrapper">
		    	<div id="credit-label" class="form-label">
		    		<label for="credit" class="required"><?php echo $this -> translate("Credit");?></label>
		    	</div>
				<div id="credit-element" class="form-element">
					<input type="text" name="credit" id="credit" value="" onkeypress="return onlyNumbers(event);">
				</div>
			</div>
			<div id="send_credit-wrapper" class="form-wrapper">
				<div id="send_credit-label" class="form-label">&nbsp;</div>
				<div id="send_credit-element" class="form-element" style="display: block;">
					<button name="send_credit" type="submit" id="send_credit" type="button"><?php echo $this -> translate("Send Credits")?></button>
					<?php echo $this->translate("or");?>
					<a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();"><button><?php echo $this->translate("Cancel")?></button></a>
				</div>
			</div>
			<?php endif;?>
		</form>
	<?php endif;?>
</div>
<?php if(!$this -> fail):?>
<script type="text/javascript">
  var maxSend = <?php echo $this -> max?>;
  var user_id = <?php echo $this -> user_id?>;
 function onlyNumbers(evt) 
  {
    var e = evt;
    if(window.event){ // IE
        var charCode = e.keyCode;
    } else if (e.which) { // Safari 4, Firefox 3.0.4
        var charCode = e.which
    }
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    
    return true;
  }
</script>
<?php endif;?>