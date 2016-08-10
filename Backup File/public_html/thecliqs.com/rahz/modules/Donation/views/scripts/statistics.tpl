<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       17.09.12
 * @time       17:52
 */?>


<div class="menu_right" style="width:200px">
  <ul class="menu_dashboard_links">
    <li style="width:200px">
      <ul >
        <li class="hecore-menu-tab  <?php if (Zend_Controller_Front::getInstance()->getRequest()->getControllerName() == 'statistics'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'donation' ,'controller'=>'statistics', 'action' => 'index', 'donation_id' => $this->donation_id),'default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_Chart'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab  <?php if (Zend_Controller_Front::getInstance()->getRequest()->getControllerName() == 'transactions'): ?>active-menu-tab<?php endif; ?>">
          <a href="<?php echo $this->url(array('module' => 'donation', 'controller'=>'transactions', 'action' => 'list', 'donation_id' => $this->donation_id),'default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_List'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab">
          <?php echo $this->htmlLink($this->donation->getHref(), $this->translate('DONATION_Back'), array('class' => 'hecore-menu-link'));?>
        </li>
      </ul>
    </li>
  </ul>
</div>