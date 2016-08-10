<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _paymentButtonJS.tpl  27.07.12 17:01 TeaJay $
 * @author     Taalay
 */
?>

window.addEvent('domready', function() {
  var span = new Element('span', {'html':<?php echo Zend_Json::encode($this->partial('_paymentButton.tpl', 'credit'))?>});
  $('buttons-wrapper').appendChild(span);
});