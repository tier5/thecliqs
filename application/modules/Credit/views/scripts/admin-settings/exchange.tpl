<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: exchange.tpl  18.07.12 14:48 TeaJay $
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

<ul class="form-errors">
  <li>
    <?php echo $this->translate('CREDIT_VIEWS_SCRIPTS_ADMINSETTINGS_EXCHANGE_ATTENTION', $this->locale()->toCurrency(1, $this->currency)); ?>
  </li>
</ul>

<p>
  <?php echo $this->translate("CREDIT_VIEWS_SCRIPTS_ADMINSETTINGS_EXCHANGE_DESCRIPTION") ?>
</p>
<br />

<div class='settings' style="clear: none;">
  <?php echo $this->form->render($this) ?>
</div>