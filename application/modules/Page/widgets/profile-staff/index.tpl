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

$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Page/externals/scripts/pagination.js');
?>



<div class="layout_middle">

  <div class="page_edit_admins">
    <h3>
      <?php echo $this->translate("Team"); ?>
      <span id="staff_loader_id" class="hidden">  <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Page/externals/images/loader.gif', ''); ?></span>
    </h3>
    <div class="global_form">
      <div>
        <div>

          <h3>
            <?php if ($this->is_super_admin) : ?>
            <a class="page_super_admin" href="<?php echo $this->url(array('action' => 'manage-admins', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">
              <img title="<?php echo $this->translate('PAGE_TEAM_MANAGE')?>" class="add-admin" src="application/modules/User/externals/images/friends/accepted.png">
              <?php echo $this->translate('PAGE_TEAM_MANAGE')?>
            </a>
            <?php endif; ?>
          </h3>
          <br class="clr" />
          <?php if ($this->paginator->getTotalItemCount() > 0): ?>
          <div class="page_admin_list">
            <?php foreach ($this->paginator as $admin): ?>
            <div class="page_admin_list_item">
              <div class="l">
                <?php echo $this->htmlLink($admin->getHref(), $this->itemPhoto($admin, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_user')), array('class' => 'admin_profile_thumb')); ?>
              </div>
              <div class="r">
                <?php echo $this->htmlLink($admin->getHref(), $this->string()->truncate($admin->getTitle(), 15, '...'), array('class' => 'page_admin', 'title' => $admin->getTitle())); ?>
                <div class="team_box">
                  <span class="team_title" id="admin_title_<?php echo $admin->getIdentity(); ?>">
                    <?php
                    if($admin->title)
                     echo $admin->title ;
                    elseif( $admin->type == 'ADMIN' )
                      echo 'Admin';
                    else
                      echo 'Employer';
                    ?>
                  </span>
                  <div class="clr"></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="clr"></div>

          <?php if($this->paginator->count() > 1): ?>
          <?php echo $this->paginationControl($this->paginator, null, array("pagination/staff.tpl","page")); ?>
          <?php endif;?>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  window.addEvent('domready', function(){
    var url = '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity, 'page_id' => $this->page->getIdentity()), 'page_widget', true) ?>';
    Pagination.init(url, '.page_edit_admins > div.global_form', 'staff_loader_id')
  });

</script>