<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
	var switchType = function()
	{
    if ($('payment_mode')) {
      if ( $('payment_mode').value == 'client_store' ) {
        $('commission_fixed-wrapper').style.display='none';
        $('commission_percentage-wrapper').style.display='none';
      } else {
        $('commission_fixed-wrapper').style.display='';
        $('commission_percentage-wrapper').style.display='';
      }
    }
	}

  en4.core.runonce.add(function() {
		switchType();
  });
</script>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->render('admin/_settingsMenu.tpl'); ?>

<div class="settings admin_home_middle" style="clear: none;">
  <div class="settings">
    <?php  echo $this->form->render($this); ?>
  </div>
</div>