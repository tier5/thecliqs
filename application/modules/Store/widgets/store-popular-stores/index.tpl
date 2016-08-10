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
<div class="store-widget-list">
  <ul>
    <?php foreach($this->pages as $page): ?>
      <li>
        <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.icon', '', array('class' => 'thumb_icon')), array('class' => 'page_profile_thumb item_thumb')); ?>

        <div class="item_info">
          <div class="item_name">
            <?php echo $this->htmlLink($page->getHref(), $page->getTitle(), array('class' => 'store_profile_title')); ?><br />
          </div>

          <div class="item_description">
            <?php echo $page->getDescription(true, true, false, 30); ?>
          </div>
          <div class="clr"></div>

          <div class="item_date">
            <?php echo $this->translate("Published by"); ?> <?php echo $this->htmlLink($page->owner->getHref(), $page->owner->getTitle()); ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>