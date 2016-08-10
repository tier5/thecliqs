<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage-admins.tpl  14.11.11 10:07 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
    page.page_id = <?php echo $this->page->getIdentity(); ?>;
    page.ajax_url = "<?php echo $this->url(array('action' => 'ajax'), 'admin_general'); ?>";
    page.init();
  });
</script>

<?php echo $this->render('_page_options_menu.tpl'); ?>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class="layout_middle">
  <div class="page_edit_admins">
    <div class="global_form">
      <div>
        <div>
          <h3>
            <?php echo $this->translate("Admins"); ?>
          <?php if ($this->is_super_admin) : ?>
              <?php echo $this->htmlLink("javascript:page.choose_admins();", '<img title="'.$this->translate('PAGE Add').'" class="add-admin" src="application/modules/User/externals/images/friends/accepted.png"> '.$this->translate('PAGE Add'), array('class' => 'page_super_admin')); ?>
          <?php endif; ?>
          </h3>
          <br class="clr" />
          <?php if ($this->admins->getTotalItemCount() > 0): ?>
          <div class="page_admin_list">
            <?php foreach ($this->admins as $admin): ?>
            <div class="page_admin_list_item">
              <div class="l">
                <?php echo $this->htmlLink($admin->getHref(), $this->itemPhoto($admin, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_user')), array('class' => 'admin_profile_thumb')); ?>
              </div>
              <div class="r">
                <?php echo $this->htmlLink($admin->getHref(), $this->string()->truncate($admin->getTitle(), 15, '...'), array('class' => 'page_admin', 'title' => $admin->getTitle())); ?>
                <?php if (!$this->page->isOwner($admin) && ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity())) : ?>
                  <div class="page_admin_option">
                    <?php echo $this->htmlLink(
                      $this->url(
                        array(
                          'action' => 'remove',
                          'admin_id' => $admin->getIdentity()
                        ), 'admin_specific')."?page_id=".$this->page->getIdentity(),
                      '<img title="remove" src="application/modules/Page/externals/images/remove.png">',
                      array('class' => 'smoothbox page-admin-remove-button')
                    );?>
                  </div>
                <?php endif; ?>
                <div class="team_box">
                  <span class="team_title" id="admin_title_<?php echo $admin->getIdentity(); ?>"><?php echo $admin->title ? $admin->title : 'Admin'; ?></span>
                  <?php if ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity()) : ?>
                    <a class="team_title_edit" id="admin_title_edit_<?php echo $admin->getIdentity(); ?>" href="javascript:page.edit_admin_title(<?php echo $admin->getIdentity(); ?>);">&nbsp;</a>
                  <?php endif; ?>
                  <div class="team_title_input_box hidden" id="admin_title_input_box_<?php echo $admin->getIdentity(); ?>">
                    <input class="team_title_input" type="text" value="<?php echo $admin->title ? $admin->title : 'Admin'; ?>" id="admin_title_input_<?php echo $admin->getIdentity(); ?>" />
                  </div>
                  <?php if (!$this->page->isOwner($admin) && ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity())) : ?>
                  <div class="page_admin_option">
                    <?php echo $this->htmlLink(
                    $this->url(
                      array(
                        'action' => 'change',
                        'admin_id' => $admin->getIdentity(),
                      ), 'admin_specific')."?page_id=".$this->page->getIdentity().'&type=EMPLOYER',
                    '<img title="'.$this->translate("change to employer").'" src="application/modules/Core/externals/images/move_down.png">',
                    array('class' => 'smoothbox page-admin-change-button')
                  );?>
                  </div>
                  <?php endif; ?>
                  <div class="clr"></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="clr"></div>
        </div>
      </div>
    </div>

    <div class="global_form">
      <div>
        <div>
          <h3>
            <?php echo $this->translate("Employers"); ?>
            <?php if ($this->is_super_admin) : ?>
            <?php echo $this->htmlLink("javascript:page.choose_employers();", '<img title="'.$this->translate('PAGE Add').'" class="add-admin" src="application/modules/User/externals/images/friends/accepted.png"> '.$this->translate('PAGE_EMPLOYER_ADD'), array('class' => 'page_super_admin')); ?>
            <?php endif; ?>
          </h3>
          <br class="clr" />
          <?php if ($this->employers->getTotalItemCount() > 0): ?>
          <div class="page_admin_list">
            <?php foreach ($this->employers as $admin): ?>
            <div class="page_admin_list_item">
              <div class="l">
                <?php echo $this->htmlLink($admin->getHref(), $this->itemPhoto($admin, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_user')), array('class' => 'admin_profile_thumb')); ?>
              </div>
              <div class="r">
                <?php echo $this->htmlLink($admin->getHref(), $this->string()->truncate($admin->getTitle(), 15, '...'), array('class' => 'page_admin', 'title' => $admin->getTitle())); ?>
                <?php if (!$this->page->isOwner($admin) && ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity())) : ?>
                <div class="page_admin_option">
                  <?php echo $this->htmlLink(
                  $this->url(
                    array(
                      'action' => 'remove',
                      'admin_id' => $admin->getIdentity()
                    ), 'admin_specific')."?page_id=".$this->page->getIdentity(),
                  '<img title="remove" src="application/modules/Page/externals/images/remove.png">',
                  array('class' => 'smoothbox page-admin-remove-button')
                );?>
                </div>
                <?php endif; ?>
                <div class="team_box">
                  <span class="team_title" id="admin_title_<?php echo $admin->getIdentity(); ?>"><?php echo $admin->title ? $admin->title : 'Employer'; ?></span>
                  <?php if ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity()) : ?>
                  <a class="team_title_edit" id="admin_title_edit_<?php echo $admin->getIdentity(); ?>" href="javascript:page.edit_admin_title(<?php echo $admin->getIdentity(); ?>);">&nbsp;</a>
                  <?php endif; ?>
                  <div class="team_title_input_box hidden" id="admin_title_input_box_<?php echo $admin->getIdentity(); ?>">
                    <input class="team_title_input" type="text" value="<?php echo $admin->title ? $admin->title : 'Employer'; ?>" id="admin_title_input_<?php echo $admin->getIdentity(); ?>" />
                  </div>
                  <?php if (!$this->page->isOwner($admin) && ($this->is_super_admin || $this->viewer->getIdentity() == $admin->getIdentity())) : ?>
                  <div class="page_admin_option">
                    <?php echo $this->htmlLink(
                    $this->url(
                      array(
                        'action' => 'change',
                        'admin_id' => $admin->getIdentity(),
                      ), 'admin_specific')."?page_id=".$this->page->getIdentity().'&type=ADMIN',
                    '<img title="'.$this->translate("change to admin").'" src="application/modules/Core/externals/images/move_up.png">',
                    array('class' => 'smoothbox page-admin-change-button')
                  );?>
                  </div>
                  <?php endif; ?>
                  <div class="clr"></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="clr"></div>
        </div>
      </div>
    </div>
  </div>
</div>