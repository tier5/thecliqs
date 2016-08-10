<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: change.tpl  21.04.12 12:38 TeaJay $
 * @author     Taalay
 */
?>

<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
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

<?php echo $this->render('_adminGiftOptions.tpl')?>

<div class="admin_home_middle">
  <div class="settings">
    <?php echo $this->form->render($this)?>
  </div>
</div>