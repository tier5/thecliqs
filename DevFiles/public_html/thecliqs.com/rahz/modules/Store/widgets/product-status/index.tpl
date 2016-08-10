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
  window.addEvent('load', function(){
    var $tabs = $('main_tabs');
    if ($tabs != undefined) {
      var $li = $tabs.getElementsByTagName('li')[0];
      var $a = $li.getElementsByTagName('a')[0];
      tabContainerSwitch($a);
    }
  });
</script>

<h2>
  <?php echo ( '' != trim($this->product->getTitle()) ? $this->product->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'); ?>
  <?php if ($this->product->sponsored) : ?>
    <img class="icon" src="application/modules/Store/externals/images/sponsoredBig.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>">
  <?php endif; ?>
  <?php if ($this->product->featured) : ?>
    <img class="icon" src="application/modules/Store/externals/images/featuredBig.png" title="<?php echo $this->translate('STORE_Featured'); ?>">
  <?php endif; ?>
</h2>