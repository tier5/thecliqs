<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _locationsMenu.tpl  4/3/12 3:06 PM mt.uulu $
 * @author     Mirlan
 */
?>


<div class="admin_home_right" style="width:170px">
  <ul class="admin_home_dashboard_links">
  <li style="width:170px">
    <ul >

      <li class="hecore-menu-tab products <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'locations', 'action'=>'index'), 'admin_default', true),
          $this->translate('STORE_Supported Locations'),
          array('class'=>'icon_locations_default', 'style'=>'float: none')
        );?>
      </li>

      <li class="hecore-menu-tab products <?php if ($this->menu == 'all'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'store', 'controller'=>'locations', 'action'=>'all'), 'admin_default', true),
          $this->translate('STORE_Default Locations'),
          array('class'=>'icon_locations_supported', 'style'=>'float: none')
        );?>
      </li>

    </ul>
  </li>
</ul>
</div>