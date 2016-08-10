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

      <li class="hecore-menu-tab products <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
        <a href="<?php echo $this->url(
          array('module'=>'store', 'controller'=>'settings', 'action' => 'index'),
          'admin_default', true); ?>" class="hecore-menu-link">
          <?php echo $this->translate('STORE_Global'); ?>
        </a>
      </li>

      <li class="hecore-menu-tab products <?php if ($this->menu == 'level'): ?>active-menu-tab<?php endif; ?>">
        <a href="<?php echo $this->url(array('controller'=>'level', 'module'=>'store'), 'admin_default', true); ?>" class="hecore-menu-link">
          <?php echo $this->translate('Level'); ?>
        </a>
      </li>
    </ul>
  </li>
</ul>
</div>