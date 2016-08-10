<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: addphotos.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Product Photos');?>
  </h2>
</div>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->render('admin/_productsMenu.tpl'); ?>

<div class="admin_home_middle">
  <div class="settings">
    <?php echo $this->form->render($this); ?>
  </div>
</div>

  