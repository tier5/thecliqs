<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>
<h2><?php echo $this->translate("YouNet Resume Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Industry Custom Fields") ?></h3>
</br>

<div class="admin_fields_type">
  <p><?php echo $this->translate("YNRESUME_ADMIN_INDUSTRY_FIELD_DESCRIPTION") ?></p>
  <br />
  <h3><?php echo $this->translate("Editing Industry Type:") ?></h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
  <?php echo $this->htmlLink(array(
	    'route' => 'admin_default',
	    'module' => 'ynresume',
	    'controller' => 'industries'
	), $this->translate('Back to Manage Industries'), array('class'=>'buttonlink ynjobposting_icon_back')) ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add Question") ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading">Add Heading</a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;">Save Order</a>
</div>

<br />

<ul class="admin_fields">
  <?php foreach( $this->secondLevelMaps as $map ): ?>
    <?php echo $this->adminFieldMeta($map) ?>
  <?php endforeach; ?>
</ul>

