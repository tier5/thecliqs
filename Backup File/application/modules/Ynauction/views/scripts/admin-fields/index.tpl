<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminFieldsController.php
 * @author     Minh Nguyen
 */
?>


<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>

<h2><?php echo $this->translate("Auction Plugin"); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('YNAUCTION_FIELDS_VIEWS_SCRIPTS_ADMINFIELDS_DESCRIPTION') ?>
</p>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate('Add Question'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading" style="display:none;"><?php echo $this->translate('Add Heading') ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order') ?></a>
</div>

<br />


<ul class="admin_fields">
  <?php foreach( $this->topLevelMaps as $field ): ?>
    <?php echo $this->adminFieldMeta($field) ?>
  <?php endforeach; ?>
</ul>

<br />
<br />

<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>
