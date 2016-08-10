<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php echo $this->render('_page_options_menu.tpl'); ?>
<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <?php echo $this->action('index', 'page', 'hebadge');?>
</div>