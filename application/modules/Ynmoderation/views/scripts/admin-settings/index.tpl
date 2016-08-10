<style>
.settings {
	width: 50%;
}
.settings form {
	background-color: transparent;
}
.settings form > table {
	margin-bottom: 15px;
}
</style>

<script type="text/javascript">
	function setValueForHField(flag){
		$$("input[name='enable_or_disable']").set("value", flag);
	}
		
	function selectAll()
	{
		  var i;
		  var multidelete_form = $('ynmoderation_modules_form');
		  var inputs = multidelete_form.elements;
		  for (i = 1; i < inputs.length; i++) {
			    if (!inputs[i].disabled) {
			      inputs[i].checked = inputs[0].checked;
			    }
		  }
	}
</script>

<?php
	//$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Ynmoderation/externals/scripts/switch_slider.js'); 
?>
<h2>
  <?php echo $this->translate('Moderation Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
		<?php if ($this->updatedStatus): ?>
			<ul class="form-notices"><li><?php echo $this->translate("Your changes have been saved") ?>.</li></ul>
		<?php endif;?>
		<?php if( count($this->paginator) ): ?>
		<form id='ynmoderation_modules_form' method="post" action="<?php echo $this->url();?>" onSubmit="setValueForHField(1)">
		<table class='admin_table'>
		  <thead>
		    <tr>
		      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
		      <th class='admin_table_short'>ID</th>
		      <th><?php echo $this->translate("Module Name") ?></th>
		      <th><?php echo $this->translate("Status") ?></th>
		    </tr>
		  </thead>
		  
		  <tbody>
		    <?php foreach ($this->paginator as $item): ?>
		      <tr>
		        <td><input type='checkbox' class='checkbox' name='m_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
		        <td><?php echo $item->getIdentity() ?></td>
		        <td><?php echo $item->name ?></td>
		        <td><?php echo ($item->enabled == '1') ? $this->translate("Enabled") : $this->translate("Disabled"); ?></td>
		      </tr>
		    <?php endforeach; ?>
		  </tbody>
		</table>
		
		<input type="hidden" name="enable_or_disable" value="0" />
		<button type='submit'><?php echo $this->translate("Enable Selected") ?></button>
		<button type='button' name='disable' onclick="setValueForHField(0); document.forms['ynmoderation_modules_form'].submit();"><?php echo $this->translate("Disable Selected") ?></button>
		
		</form>
		
		<br/>
		<div>
		  <?php echo $this->paginationControl($this->paginator); ?>
		</div>
		
		<?php else: ?>
		  <div class="tip">
		    <span>
		      <?php echo $this->translate("There are no modules yet.") ?>
		    </span>
		  </div>
		<?php endif; ?>
  </div>
</div>





