<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: create.tpl minhnc $
 * @author     MinhNC
 */
?>
<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Your changes have been saved.") ?></div>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.replace( '<?php echo $this->url(array('action' => 'index')) ?>' )
    }, 500);
  </script>

<?php endif; ?>