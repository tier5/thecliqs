<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-file.tpl  21.09.11 16:47 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->render('admin/_productsMenu.tpl'); ?>

<div class='settings' style="clear: none">
  <?php echo $this->form->render($this) ?>
</div>