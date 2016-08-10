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
  <?php echo $this->levelForm->render($this); ?>
</div>

<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function(){
	$('level_id').addEvent('change', function(){
		  window.location.href = en4.core.baseUrl + 'admin/store/level/index/level_id/'+this.get('value');
	});
});
//]]>
</script>
  