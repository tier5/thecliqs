<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: level.tpl  06.08.12 16:54 TeaJay $
 * @author     Taalay
 */
?>
<h2>
  <?php echo $this->translate('Credits Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/credit/settings/level/id/'+level_id;
  }
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->render('_adminSettingsMenu.tpl'); ?>

<div class='settings' style="clear: none;">
  <?php echo $this->form->render($this) ?>
</div>