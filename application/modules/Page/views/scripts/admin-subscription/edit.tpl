<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-08-11 17:53 taalay $
 * @author     Taalay
 */
?>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <script type="text/javascript">
    parent.Smoothbox.close();
  </script>
<?php endif; ?>