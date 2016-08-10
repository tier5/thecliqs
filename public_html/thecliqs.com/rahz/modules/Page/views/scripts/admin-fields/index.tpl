<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
  function switchDisplay(id) {
    var el = document.getElementById(id);
    if (!!el)
      el.style.display = (el.style.display=='none')?'':'none';
    return false;
  }
</script>

<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>

<h2><?php echo $this->translate("Page Fields"); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("Create your own field system in Page Plugin."); ?>
</p>

<br />

<div class="admin_fields_type">
  <h3><?php echo $this->translate("Editing Page Type"); ?>:</h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add Field"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate("Add Heading"); ?></a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate("Edit Category"); ?></a>
  <?php if( $this->option_id != 1 ): ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate("Delete Category"); ?></a>
  <?php endif; ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate("Create New Category"); ?></a>
  <?php if($this->term): ?>
    <a href="javascript:void(0);" onclick="switchDisplay('terms_create')" class="buttonlink admin_fields_options_editterm">
      <?php
        echo $this->translate("Edit Agree Terms");
      ?>
    </a>
  <?php endif; ?>
  <?php if(!$this->term): ?>
    <a href="javascript:void(0);" onclick='switchDisplay("terms_create")' class="buttonlink admin_fields_options_addterm">
      <?php echo $this->translate("Add Agree Terms"); ?>
    </a>
  <?php endif; ?>
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

<?php echo $this->form->setAttrib('style', 'display: none')->render(); ?>
