<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $id: create-audio.tpl  09.09.11 17:39 taalay $
 * @author     Taalay
 */
?>


<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
?>

<script type="text/javascript">
var product_id = <?php echo $this->product->getIdentity() ?>;

if (product_id > 0) {
  $$('#product_id option').each(function(el, index) {
    if (el.value == product_id)
      $('product_id').selectedIndex = index;
  });
}
</script>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->render('admin/_productsMenu.tpl'); ?>


<div class='settings' style="clear: none">
  <?php echo $this->form->render($this) ?>
</div>