<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: edit.tpl  27.04.12 20:03 TeaJay $
 * @author     Taalay
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />


<div class="settings">
  <?php echo $this->form->render($this)?>
</div>