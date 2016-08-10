<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _adminSettingsMenu.tpl  06.08.12 17:02 TeaJay $
 * @author     Taalay
 */
?>

<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
  <li style="width:200px">
    <ul >
      <li class="hecore-menu-tab <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'credit', 'controller'=>'settings', 'action'=>'index'), 'admin_default', true),
          $this->translate('Global Settings'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>

      <li class="hecore-menu-tab <?php if ($this->menu == 'level'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'credit', 'controller'=>'settings', 'action'=>'level'), 'admin_default', true),
          $this->translate('Level Settings'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>

      <li class="hecore-menu-tab <?php if ($this->menu == 'exchange'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'credit', 'controller'=>'settings', 'action'=>'exchange'), 'admin_default', true),
          $this->translate('Exchange'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>

      <li class="hecore-menu-tab <?php if ($this->menu == 'badges'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'credit', 'controller'=>'settings', 'action'=>'badges'), 'admin_default', true),
          $this->translate('Badges'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>
    </ul>
  </li>
</ul>
</div>