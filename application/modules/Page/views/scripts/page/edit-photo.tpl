<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-photo.tpl  11.11.11 18:05 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->render('_page_options_menu.tpl'); ?>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class="layout_middle">
  <div class="page_edit_photo">
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('current-label').setStyle('display', 'none');
  });
</script>