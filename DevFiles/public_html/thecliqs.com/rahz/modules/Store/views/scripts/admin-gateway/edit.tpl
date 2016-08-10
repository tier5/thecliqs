<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: edit.tpl 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->render('admin/_gatewayMenu.tpl'); ?>

<div class="settings admin_home_middle" style="clear: none;">
   <h2><?php echo $this->translate("STORE_%s Settings", $this->gateway->getTitle()); ?></h2>
  <?php echo $this->form->render($this) ?>
</div>
