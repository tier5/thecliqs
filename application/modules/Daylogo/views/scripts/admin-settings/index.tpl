<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-08-16 16:43 nurmat $
 * @author     Nurmat
 */
?>
<h2>
    <?php echo $this->translate('Daylogo Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<div style="margin: 15px 0;">
  <?php echo $this->translate('DAYLOGO_VIEWS_SCRIPTS_ADMINSETTINGS_LOGOSETTINGS_DESCRIPTION');?>
</div>
<?php endif; ?>

<?php echo $this->form->render($this); ?>