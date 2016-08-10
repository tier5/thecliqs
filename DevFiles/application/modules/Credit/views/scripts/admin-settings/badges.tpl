<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: badges.tpl  20.03.12 15:00 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Credits Plugin') ?>
</h2>

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

<p>
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMININDEX_BADGES_DESCRIPTION") ?>
</p>

<br />
  
<?php if ($this->hebadge) : ?>
  <?php echo $this->translate('CREDIT_ADMIN_BADGES_IS_ENABLED_DESCRIPTION')?>
<?php else : ?>
  <?php echo $this->translate('CREDIT_ADMIN_BADGES_IS_DISABLED_DESCRIPTION')?>
<?php endif; ?>