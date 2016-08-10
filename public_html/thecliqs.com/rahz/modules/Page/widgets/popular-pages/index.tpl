<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-08 17:53 taalay $
 * @author     Taalay
 */
?>

<div class="page_list">
  <ul>
    <?php foreach($this->pages as $page): ?>
    <li>
      <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_page')), array('class' => 'page_profile_thumb item_thumb')); ?>
      <div class="item_info">
        <div class="item_name">
          <?php echo $this->htmlLink($page->getHref(), $page->getTitle(), array('class' => 'page_profile_title')); ?><br />
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
      <div class="create_own_page">
        <?php echo $this->htmlLink($this->url(array(), 'page_create'), $this->translate('Page_Create_Own_Page'));?>
      </div>
  </ul>
</div>