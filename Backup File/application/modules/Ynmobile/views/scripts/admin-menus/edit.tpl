<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: edit.tpl minhnc $
 * @author     MinhNC
 */
?>

<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php elseif( $this->status ): ?>

  <div style="width: auto;"><b><?php echo $this->translate("Changes saved!") ?></b></div>

  <script type="text/javascript">
    var name = '<?php echo $this->name ?>';
    var label = '<?php echo $this->escape($this->menuItem->label) ?>';
    setTimeout(function() {
      parent.$('admin_menus_item_' + name).getElement('.item_label').set('html', label);
      parent.window.location.replace( '<?php echo $this->url(array('action' => 'index')) ?>' )
    }, 500);
  </script>

<?php endif; ?>