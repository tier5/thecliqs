<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _adminGiftOptions.tpl  22.02.12 18:13 TeaJay $
 * @author     Taalay
 */
?>

<div class="admin_home_right" style="width:200px">
  <ul class="admin_home_dashboard_links">
    <li style="width:200px">
      <ul>
        <li class="hecore-menu-tab gifts <?php if ($this->menu == 'edit'): ?>active-menu-tab<?php endif; ?>">
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'edit', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
            $this->translate('HEGIFT_Edit main settings'),
            array('class'=>'icon_edit_gift', 'style'=>'float: none')
          );?>
        </li>

        <li class="hecore-menu-tab gifts <?php if ($this->menu == 'manage-photo') : ?>active-menu-tab<?php endif; ?>">
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'manage-photo', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
            $this->translate('HEGIFT_Manage Photo'),
            array('class'=>'icon_photo_gift', 'style'=>'float: none')
          );?>
        </li>

        <?php if ($this->gift->type == 1) : ?>
          <li class="hecore-menu-tab gifts <?php if ($this->menu == 'duplicate') : ?>active-menu-tab<?php endif; ?>">
            <?php echo $this->htmlLink(
              $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'duplicate', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
              $this->translate('HEGIFT_Duplicate Gift'),
              array('class'=>'icon_duplicate_gift', 'style'=>'float: none')
            );?>
          </li>
        <?php endif; ?>

        <?php if ($this->gift->type == 2) : ?>
          <li class="hecore-menu-tab gifts <?php if ($this->menu == 'manage-audio') : ?>active-menu-tab<?php endif; ?>">
            <?php echo $this->htmlLink(
              $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'manage-audio', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
              $this->translate('HEGIFT_Manage Audio'),
              array('class'=>'icon_audio_gift', 'style'=>'float: none')
            );?>
          </li>
        <?php endif; ?>

        <?php if ($this->gift->type == 3) : ?>
          <li class="hecore-menu-tab gifts <?php if ($this->menu == 'manage-video') : ?>active-menu-tab<?php endif; ?>">
            <?php echo $this->htmlLink(
              $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'manage-video', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
              $this->translate('HEGIFT_Manage Video'),
              array('class'=>'icon_video_gift', 'style'=>'float: none')
            );?>
          </li>
        <?php endif; ?>

        <li class="hecore-menu-tab products <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'index'), 'admin_default', true),
            $this->translate('HEGIFT_List of gifts'),
            array('class'=>'icon_gifts', 'style'=>'float: none')
          );?>
        </li>

        <li>
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'create'), 'admin_default', true),
            $this->translate('HEGIFT_Add New Gift'),
            array('class'=>'icon_add_gift', 'style'=>'float: none')
          ); ?>
        </li>

        <li class="hecore-menu-tab gifts <?php if ($this->menu == 'change'): ?>active-menu-tab<?php endif; ?>">
          <?php echo $this->htmlLink(
            $this->url(array('module'=>'hegift', 'controller'=>'index', 'action'=>'change', 'gift_id'=> $this->gift->getIdentity()), 'admin_default', true),
            $this->translate('HEGIFT_Change Type'),
            array('class'=>'icon_change_type_gift', 'style'=>'float: none')
          );?>
        </li>
      </ul>
    </li>
  </ul>
</div>