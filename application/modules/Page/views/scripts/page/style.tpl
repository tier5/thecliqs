<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Page
 * @copyright  Copyright 2009-2012 Hire-Experts
 * @license    http://hire-experts.com
 * @version    $Id: style.tpl 9747 2012-11-28 02:08:08Z teajay $
 * @author     TJ
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
