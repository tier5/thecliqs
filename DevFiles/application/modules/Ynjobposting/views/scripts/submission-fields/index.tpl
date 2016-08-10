<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>
<div style='display: none'><?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?></div>
<h3><?php echo $this->translate('LELONG') ?></h3>
<span style='margin-top: -5px; float:right; display: block' class='review open_all_icon'>   </span>
<span style='margin-top: -5px; float:right; display: none' class='review close_all_icon'>   </span>
<div id='add_review_field_wrapper'>
	<div class="admin_fields_options">
	  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add Question") ?></a>
	  
	  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;">Save Order</a>
	</div>
	<br />
	<ul class="admin_fields">
	  <?php foreach( $this->secondLevelMaps as $map ): ?>
	    <?php echo $this->adminFieldMeta($map) ?>
	  <?php endforeach; ?>
	</ul>
</div>