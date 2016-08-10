<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _boxJs.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/core.js');
?>

<?php if($this->params['show_cart']): ?>
store_cart.html = <?php echo Zend_Json::encode($this->partial('cart/_box.tpl', 'store', $this->params)); ?>;
<?php else: ?>
store_cart.show_cart = 0;
<?php endif; ?>
store_cart.addCartUrl = '<?php echo $this->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'add'), 'default', true); ?>';
store_cart.removeCartUrl = '<?php echo $this->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'remove'), 'default', true); ?>';
store_cart.wishUrl = '<?php echo $this->url(array('module' => 'store', 'controller' => 'product', 'action'=>'wish'), 'default', true); ?>';
store_cart.checkoutUrl = '<?php echo $this->url(array('action'=>'cart'), 'store_general'); ?>';