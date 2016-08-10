<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>
<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
  <li style="width:200px">
    <ul >

      <li class="hecore-menu-tab products <?php if ($this->menu == 'edit'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(array(
          'route'=>'admin_default',
          'module'=>'store',
          'controller'=>'gateway',
          'action'=>'edit',
          'gateway_id'=>$this->gateway->getIdentity()
        ), $this->translate('STORE_Account Settings'), array('class'=>'hecore-menu-link') ); ?>
      </li>

      <li class="hecore-menu-tab products <?php if ($this->menu == 'button'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(array(
          'route'=>'admin_default',
          'module'=>'store',
          'controller'=>'gateway',
          'action'=>'button',
          'gateway_id'=>$this->gateway->getIdentity()
        ), $this->translate('STORE_Button Settings'), array('class'=>'hecore-menu-link') ); ?>
      </li>
    </ul>
  </li>
</ul>
</div>