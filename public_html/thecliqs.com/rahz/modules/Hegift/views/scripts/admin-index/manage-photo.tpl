<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hrgift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: manage-photo.tpl  06.02.12 16:31 TeaJay $
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

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMININDEX_MANAGEPHOTO_DESCRIPTION") ?>
</p>
<br />

<?php echo $this->render('_adminGiftOptions.tpl')?>

<div class="admin_home_middle">
  <div class="settings">
    <?php echo $this->form->render($this)?>
  </div>
</div>