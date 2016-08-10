<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _albums.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>

<?php if ($this->paginator->getCurrentItemCount() > 0): ?>

<?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
    <a class="pagination"
       href="javascript:photos.load_page(<?php echo ($this->paginator->getCurrentPageNumber() - 1); ?>);">
        <?php echo $this->translate("Previous"); ?>
    </a>
    <?php endif; ?>

<?php foreach ($this->paginator as $item): ?>
    <?php
        if (isset($item['album_id'])) {
            $id = $item['album_id'];
            $item_type = 'album';
        } else {
            $id = $item['pagealbum_id'];
            $item_type = 'pagealbum';
        }
    ?>
    <a href="<?php echo $this->url(array(
        'action' => 'photos',
        'id' => $this->subject()->getIdentity(),
        'type' => $this->type,
        'album_id' => $id,
        'album_type' => $item_type,
    ), 'timeline_page_photo', true); ?>" class="item" title="<?php echo $item['title']; ?>">
        <span class='photo'
              style='background-image: url()'><?php echo $this->itemPhoto(Engine_Api::_()->getItem($item_type, $id), 'thumb.normal'); ?></span>

        <div class="clr"></div>
    </a>
    <?php endforeach; ?>

<?php if ($this->paginator->count() > $this->paginator->getCurrentPageNumber()): ?>
    <a class="pagination"
       href="javascript:photos.load_page(<?php echo ($this->paginator->getCurrentPageNumber() + 1); ?>);">
        <?php echo $this->translate("Next"); ?>
    </a>
    <?php endif; ?>

<?php else: ?>
<div class="no_result">
    <?php echo $this->translate('TIMELINE_You have not created any album yet.'); ?>
</div>
<?php endif; ?>