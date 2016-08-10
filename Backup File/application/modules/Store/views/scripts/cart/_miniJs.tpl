<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: miniJs.tpl  5/21/12 3:33 PM mt.uulu $
 * @author     Mirlan
 */
?>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/mini.js');
?>
mini_cart.html = <?php echo Zend_Json::encode($this->partial('cart/_mini.tpl', 'store', $this->params)); ?>;