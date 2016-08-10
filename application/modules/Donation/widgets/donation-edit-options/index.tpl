<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       30.08.12
 * @time       15:46
 */
?>

<?php
$url = array(
  'controller' => $this->donation->type,
  'action' => 'edit',
  'donation_id' => $this->donation->getIdentity(),
);

if($this->page){
  $url['page_id'] = $this->page->page_id;
}
?>

<ul class="menu_dashboard_links">
  <li style="width:200px">
    <ul >
      <li class="hecore-menu-tab <?php if (Zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'edit'):?>active-menu-tab<?php endif;?>">
        <a href="<?php echo $this->url($url, 'donation_extended', true); ?>" class="hecore-menu-link icon_back">
          <?php echo $this->translate('Edit Base Information'); ?>
        </a>
      </li>

    <?php if ($this->donation->type != 'fundraise'): ?>
      <?php $url['action'] = 'fininfo'; ?>
      <li class="hecore-menu-tab <?php if (Zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'fininfo'):?>active-menu-tab<?php endif;?>">
        <a href="<?php echo $this->url($url,'donation_extended', true); ?>" class="hecore-menu-link icon_money">
          <?php echo $this->translate('Edit Financial Information'); ?>
        </a>
      </li>
    <?php endif; ?>
      <?php if($this->paginator->getTotalItemCount() > 0): ?>
      <?php
        $url['controller'] = 'photo';
        $url['action'] = 'view';
        $url['photo_id'] = $this->photo->getIdentity();
      ?>

      <li class="hecore-menu-tab <?php if (Zend_Controller_Front::getInstance()->getRequest()->getActionName() == 'view'):?>active-menu-tab<?php endif;?>">
        <a href="<?php echo $this->url($url, 'donation_extended', true); ?>" class="hecore-menu-link icon_edit">
          <?php echo $this->translate('Manage Photos'); ?>
        </a>
      </li>
      <?php endif; ?>
      <?php if($this->donation->status == 'active' && $this->donation->approved): ?>
        <li class="hecore-menu-tab">
          <a href="<?php echo $this->url(array('controller' => 'statistics', 'action' => 'index', 'donation_id' => $this->donation->getIdentity()), 'donation_extended', true); ?>" class="hecore-menu-link icon_statistics">
            <?php echo $this->translate('DONATION_Profile_statistic'); ?>
          </a >
        </li>
      <?php endif; ?>
    </ul>
  </li>
</ul>