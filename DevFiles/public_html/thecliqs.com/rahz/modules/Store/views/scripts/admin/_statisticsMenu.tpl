<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>
<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
    <li style="width:200px">
      <ul >
        <li class="hecore-menu-tab products <?php if ($this->menu == 'chart'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'store','controller'=>'statistics','action' => 'chart'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('STORE_Products Chart'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab products <?php if ($this->menu == 'list'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'store','controller'=>'statistics','action' => 'list'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('STORE_Products List'); ?>
          </a>
        </li>

        <?php if($this->isPageEnabled): ?>
        <li class="hecore-menu-tab products <?php if ($this->menu == 'commission-chart'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'store','controller'=>'statistics','action' => 'commission-chart'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('STORE_Commission Chart'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab products <?php if ($this->menu == 'commission-list'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'store','controller'=>'statistics','action' => 'commission-list'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('STORE_Commission List'); ?>
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </li>
  </ul>
</div>