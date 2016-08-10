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
<div class="store-widget">
	<h4><?php echo $this->translate($this->widget_title)?>:</h4>
  <?php
    echo $this->htmlLink(
      $this->product->getHref(),
      $this->itemPhoto($this->product, 'thumb.profile', '', array('class' => 'img-of-the-day', 'style' => 'display: block'))
    );
  ?>
  <div style="margin-top: 3px; text-align: center; font-weight: bold;">
    <?php
      echo $this->htmlLink(
        $this->product->getHref(),
        $this->product->getTitle()
      );
    ?>
    <?php if($this->product->sponsored): ?>
      <img title="<?php echo $this->translate('STORE_Sponsored'); ?>" class="of-the-day" src="application/modules/Store/externals/images/admin/sponsored1.png">
    <?php endif; ?>
    <?php if($this->product->featured): ?>
      <img title="<?php echo $this->translate('STORE_Featured'); ?>" class="of-the-day" src="application/modules/Store/externals/images/admin/featured1.png">
    <?php endif; ?>
  </div>

</div>