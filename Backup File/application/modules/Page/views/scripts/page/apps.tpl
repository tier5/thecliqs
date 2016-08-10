<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: apps.tpl  14.11.11 16:32 TeaJay $
 * @author     Taalay
 */
?>
<?php if($this->isAllowInvite && $this->sub_menu == 'invite'): ?>
<style type="text/css">
  #submit_contacts-wrapper{
    clear: both;
  }
</style>

<?php echo $this->render('application/modules/Inviter/views/scripts/_providers_settings.tpl'); ?>
<?php endif; ?>


<?php echo $this->render('_page_options_menu.tpl'); ?>
<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <?php if ($this->isAllowPagecontact && $this->sub_menu == 'contact') : ?>
  <?php echo $this->action('edit', 'index', 'pagecontact', array('page_id' => $this->page->page_id)); ?>
  <?php endif; ?>

  <?php if ($this->isAllowPagefaq  && $this->sub_menu == 'faq') : ?>
  <?php echo $this->action('edit', 'index', 'pagefaq', array('page_id' => $this->page->page_id)); ?>
  <?php endif; ?>

  <?php if ($this->isAllowStore && $this->page->getStorePrivacy() && $this->sub_menu == 'store') : ?>
  <?php echo $this->render('_appMenu.tpl'); ?>
  <?php endif; ?>

  <?php if ($this->isAllowInvite && $this->sub_menu == 'invite'): ?>
  <div id="page-inviter-forms-wrapper" >
    <div class="inviter-forms-conteiner global_form" id="page-inviter-forms">
      <div>
        <div>
          <?php if( $this->count > 0 ) : ?>
          <div class='inviter-form-cont inviter-form-bg' id='inviter-importer-conteiner'>
            <div id='inviter-importer-title' class="inviter-tab-title inviter-import-title"
                 onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('importer');}"
                 onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').addClass('inviter-form-hover')}"
                 onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-importer-conteiner').removeClass('inviter-form-hover')}">
              <h3 style="padding: 8px;"><?php echo $this->translate('PAGE_INVITER_Import Your Contacts')?></h3>
            </div>
            <div class='inviter-form' id='inviter-importer-form' > <?php echo $this->form->render($this)?> </div>
          </div>
          <?php else: ?>
          <div class="tip">
            <span><?php echo $this->translate('INVITER_No providers'); ?></span>
          </div>
          <?php endif; ?>
          <?php  if ($this->viewer->getIdentity()):?>
          <div class='inviter-form-conteiner inviter-form-cont' id='inviter-writer-conteiner'>
            <div id='inviter-writer-title' class='inviter-tab-title inviter-write-title inviter-form-title'
                 onclick="if ($(this).hasClass('inviter-form-title')){tab_slider('writer');}"
                 onmouseover="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').addClass('inviter-form-hover')}"
                 onmouseout="if ($(this).hasClass('inviter-form-title')){$('inviter-writer-conteiner').removeClass('inviter-form-hover')}">
              <h3 style="padding: 8px;"><?php echo $this->translate('PAGE_INVITER_Write Your Contacts')?></h3>
            </div>
            <div class='inviter-form' id='inviter-writer-form'> <?php echo $this->form_write->render($this)?> </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div id='default_provider_list' style='display:none;'></div>
  </div>
  <?php endif; ?>

  <?php if ($this->sub_menu == 'promote') : ?>
  <div class="global_form">
    <div>
      <div>
        <?php echo $this->action('promote', 'club', 'like', array('object_id' => $this->page->page_id, 'object' => $this->page->getType())); ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($this->sub_menu == 'update') : ?>
  <div class="page_edit_update">
    <?php echo $this->action('send-update', 'club', 'like', array('object_id' => $this->page->page_id, 'object' => $this->page->getType())); ?>
  <?php endif; ?>
</div>