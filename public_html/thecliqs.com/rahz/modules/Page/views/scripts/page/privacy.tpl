<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: permissions.tpl  11.11.11 16:35 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->render('_page_options_menu.tpl'); ?>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class="layout_middle">
  <div class="page_edit_privacy">
    <?php echo $this->form->render($this); ?>
  </div>
</div>
