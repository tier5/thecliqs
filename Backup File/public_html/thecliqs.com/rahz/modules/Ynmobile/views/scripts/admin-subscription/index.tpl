<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<h2>
  <?php echo $this->translate('Younet Mobile Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<?php if (!count($this->packages)):?>
	<div class="tip">
      <span><?php echo $this->translate("There are currently no subscriptions.") ?></span>
    </div>
<?php else:?>
	<div class='clear'>
	  <div class='settings'>
	    <?php echo $this->form->render($this); ?>
	  </div>
	</div>
<?php endif;?>

<script type="text/javascript">
function chageAppType(select)
{
	window.location  = '<?php echo $this->url()?>' + "?type=" + select.value;
}
</script>
