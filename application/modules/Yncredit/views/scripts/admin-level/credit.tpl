 <h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchCreditLevelSettings = function(level_id){    
    window.location.href = en4.core.baseUrl +'admin/yncredit/level/credit/id/'+level_id;
  };
  var disableOrEnableModule = function(module, level_id, obj, status)
  {
  	var content = obj.innerHTML;
	    obj.innerHTML= "<img style='margin-top:4px;' src='application/modules/Yncredit/externals/images/loading.gif'></img>";
	    new Request.JSON({
	      'format': 'json',
	      'url' : '<?php echo $this->url(array('module' => 'yncredit', 'controller' => 'level', 'action' => 'disable-enable-module'), 'admin_default') ?>',
	      'data' : {
	        'format' : 'json',
	        'name' : module,
	        'level_id' : level_id,
	        'status' : status
	      },
	      'onRequest' : function(){
	      },
	      'onSuccess' : function(responseJSON, responseText)
	      {
	        obj.innerHTML = content;
	        if(status == 0)
		  	{
		  		obj.innerHTML = '<?php echo $this -> translate("enable")?>';
		  		obj.setAttribute('onclick', 'Enable this module');
		  		status = 1;
		  	}
		  	else
		  	{
		  		obj.innerHTML = '<?php echo $this -> translate("disable")?>';
		  		obj.setAttribute('onclick', 'Disable this module');
		  		status = 0;
		  	}
	        obj.setAttribute('onclick', "javascript:disableOrEnableModule('" + module + "', '" + level_id + "', this, " + status +")");
	      }
	    }).send();
  };
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div style="margin-bottom: 15px;">
	<?php echo $this->translate("YNCREDIT_FORM_ADMIN_LEVEL_DESCRIPTION");?>
</div>

<div class='clear' style="float: right; width: 83%;">
  <div class='settings'>
    <form class="yncredit_form_assign_credit" method="post">
    	<div id="level_id-element" class="form-element">
				<select name="level_id" id="level_id" onchange="javascript:fetchCreditLevelSettings(this.value);">
					<?php foreach ($this->levelOptions as $level_key => $level_title): ?>
						<option value="<?php echo $level_key;?>" <?php echo ($this->levelId == $level_key) ? 'selected="selected"' : "";?>><?php echo $this->translate($level_title); ?></option>
					<?php endforeach; ?>
				</select>
		</div>
    	
		<table class="yncredit_assign_credits" style="width: 100%;">
			<tr class="header">
				<th style="padding-left: 5px; padding-right: 10px"><?php echo $this->translate("Module");?></th>
				<th><?php echo $this->translate("Action");?></th>
				<th class="center"><?php echo $this->translate("No of First Actions");?></th>
				<th class="center"><?php echo $this->translate("Credit/Action");?></th>
				<th class="center"><?php echo $this->translate("Credit for Next Action");?></th>
				<th class="center"><?php echo $this->translate("Max Credit/Period");?></th>
			</tr>
			<?php $previousCredit = null;?>
			<?php foreach ($this->credits as $k => $credit):?>
			<?php if ( ($k == 0) || ( ($k > 0) & ($credit->module != $previousCredit->module) ) ): ?>
              <tr>
              	<td colspan="6" class="admin_yncredit_module">
              		<?php echo ucfirst($this->translate('YNCREDIT_MODULE_'. strtoupper($credit->module))); ?>
              		- <a id = "<?php echo $credit->module?>" href="javascript:;" onclick="javascript:disableOrEnableModule('<?php echo $credit->module?>', '<?php echo $this->levelId?>', this, <?php echo (int)in_array($credit->module , $this -> disableModules);?>)" title="<?php if(in_array($credit->module , $this -> disableModules)) echo $this -> translate("Enable this module"); else echo $this -> translate("Disable this module");?>">
              			<?php if(in_array($credit->module , $this -> disableModules)) echo $this -> translate("enable"); else echo $this -> translate("disable");?>
              		</a>
              	</td>
              </tr>
            <?php endif; ?>
            <?php $previousCredit = $credit;?>
			<tr>
				<td></td>
				<td><?php echo $this->translate('YNCREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $credit->action_type), '_'))); ?></td>
				<td class="center"><input type="text" value="<?php echo $credit->first_amount ?>" class="yncredit_ticket_text_input" name="first_amount__<?php echo $credit->credit_id;?>" /></td>
				<td class="center"><input type="text" value="<?php echo $credit->first_credit ?>" class="yncredit_ticket_text_input" name="first_credit__<?php echo $credit->credit_id;?>" /></td>
				<td class="center"><input type="text" value="<?php echo $credit->credit ?>" class="yncredit_ticket_text_input" name="credit__<?php echo $credit->credit_id;?>" /></td>
				<td class="center">
					<input type="text" value="<?php echo $credit->max_credit ?>" class="yncredit_ticket_text_input" name="max_credit__<?php echo $credit->credit_id;?>" /> /
					<input type="text" value="<?php echo $credit->period ?>" class="yncredit_ticket_text_input" name="period__<?php echo $credit->credit_id;?>" />
					<?php echo $this->translate("day(s)");?>
				</td>
			</tr>
			<?php endforeach;?>
			<tr style="height: 44px;">
				<td colspan="6" style="padding-left: 8px;">
					<button name="submit" id="submit" type="submit" value="save"><?php echo $this->translate("Save Changes");?></button>
					<button name="submit_set_default" id="submit_set_default" type="submit" value="reset"><?php echo $this->translate("Set Default");?></button>
				</td>
			</tr>
		</table>
		 
	</form>
  </div>
</div>
<?php echo $this->render('_adminLevelMenu.tpl'); ?>

