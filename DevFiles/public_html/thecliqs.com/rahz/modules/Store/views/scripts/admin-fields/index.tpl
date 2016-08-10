<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("STORE_Create your own field system in Store Plugin."); ?>
</p>

<br />

<div class="admin_fields_type">
  <h3><?php echo $this->translate("STORE_Editing Store Category"); ?>:</h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("STORE_Add Question"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate("STORE_Add Heading"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate("STORE_Rename Category"); ?></a>
  <?php if( $this->option_id != 1 ): ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate("STORE_Delete Category"); ?></a>
  <?php endif; ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate("STORE_Create New Category"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order"); ?></a>
</div>

<br />

<ul class="admin_fields">
    <?php foreach( $this->secondLevelMaps as $map ): ?>
      <?php echo $this->adminFieldMeta($map) ?>
    <?php endforeach; ?>
</ul>

<br />
<br />