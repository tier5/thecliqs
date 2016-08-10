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

<h2><?php echo $this->translate("Page Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<?php echo $this->content()->renderWidget('page.admin-settings-menu'); ?>

<div class="settings admin_home_middle" style="clear: none;">
    <div class="settings">
        <?php echo $this->form->render($this); ?>
    </div>
</div>