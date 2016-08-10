<div class="global_form_popup" style="width: 600px">
	<h3> <?php echo $this -> translate("Send Mass Credits/Debits")?></h3>
	<form method="post" action="<?php echo $this -> url()?>">
		<?php if(count($this -> users)):?>
			<div class="yncredit_mass_members">
                <fieldset>
    				<legend><?php echo $this -> translate("To Members")?></legend>
    				<ul>
    					<?php foreach($this -> users as $user):?>
    						<li id="member_<?php echo $user -> getIdentity()?>">
    							 <input type="hidden" name="members[]" value="<?php echo $user -> getIdentity()?>">
    							 <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('target' => '_blank', 'title' => $user -> getTitle())) ?>
    							 <a class="member-delete" href="javascript:;" onclick="delete_member(<?php echo $user -> getIdentity()?>)"></a>
    						 </li>
    					<?php endforeach;?>
    				</ul>
                </fieldset>
			</div>
		<?php endif;?>
		<div class="yncredit_mass_levles">
            <fieldset>
    			<legend><?php echo $this -> translate("To Member Levels")?></legend>
    			<select name="levels[]" id="levels" multiple="multiple">
    				<?php foreach($this -> levels as $level):
    					if($level -> type != "public"):?>
    			    	<option <?php if(in_array($level -> level_id, $this -> selected_levels)) echo 'selected="selected"'?> value="<?php echo $level -> level_id?>"><?php echo $level -> getTitle()?></option>
    			    <?php endif; endforeach;?>
    			</select>
            </fieldset>
		</div>
		<div class="yncredit_mass_credit">
			<span><?php echo $this -> translate("Credit")?></span>
			<input type="text" name="credit" id="credit" value="" onkeypress="return onlyNumbers(event);">
			<ul class="form-options-wrapper">
				<li><input type="radio" name="credit_type" id="credit_type-1" value="1" checked="checked"><label for="credit_type-1"><?php echo $this -> translate('Credit')?></label></li>
				<li><input type="radio" name="credit_type" id="credit_type-0" value="0"><label for="credit_type-0"><?php echo $this -> translate('Debit')?></label></li>
			</ul>
		</div>
		<div id="send_credit-wrapper" class="form-wrapper">
			<div id="send_credit-label" class="form-label">&nbsp;</div>
			<div id="send_credit-element" class="form-element" style="display: block;">
				<button name="send_credit" id="send_credit" type="submit" ><?php echo $this -> translate("Apply")?></button>
				<?php echo $this->translate("or");?>
				<a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();"><button><?php echo $this->translate("Cancel")?></button></a>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
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
  var delete_member = function(id)
  {
  	$('member_' + id).destroy();
  }
</script>