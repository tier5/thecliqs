<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _paymentButton.tpl  27.07.12 16:23 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->translate(' or ')?>
<button onclick="window.location.href = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'buy-level', 'action' => 'confirm'), 'default', true)?>'; return false;" name="execute" type="submit">
  <?php echo $this->translate('Pay with %1$s', $this->translate('Credits')); ?>
</button>