<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _adsJS.tpl  30.11.12 17:04 TeaJay $
 * @author     Taalay
 */
?>

window.addEvent('load', function() {
  var element = new Element('div', {'html':<?php echo Zend_Json::encode($this->partial('ad/_ads.tpl', 'page'))?>});
  var parent = ($$('.layout_left')[0]) ? $$('.layout_left')[0] : (($$('.layout_right')[0]) ? $$('.layout_right')[0] : false);
  if (parent) {
    element.inject(parent);
  }
});