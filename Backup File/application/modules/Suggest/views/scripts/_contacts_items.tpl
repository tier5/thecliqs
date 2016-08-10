<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _contact_items.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */

if (is_array($this->items)) {
  $this->potentialItems = !empty($this->items['potential']) ? $this->items['potential'] : array();
  $this->items = !empty($this->items['all']) ? $this->items['all'] : $this->items;
}

if ($this->items instanceof Zend_Paginator):

$this->need_pagination = (isset($this->items->getPages()->next));

?>

<?php if (!empty($this->potentialItems) && $this->potentialItems->getCurrentItemCount() > 0): ?>
<div class="recommended">
  <div class="recommended-title"><?php echo $this->translate("Recommended To Suggest"); ?></div>
  <div class="clr"></div>
<?php foreach ($this->potentialItems as $item): ?>
  <?php $itemDisabled = (bool)in_array($item->getIdentity(), $this->disabledItems); ?>
  <?php $itemChecked = (bool)in_array($item->getIdentity(), $this->checkedItems); ?>
  <a href='javascript:void(0)' <?php if ($itemDisabled && $this->disabled_label): ?>title = "<?php echo $this->disabled_label; ?>"<?php endif; ?>  class="item <?php if ($itemDisabled) echo "disabled" ?> <?php if ($itemChecked) echo "active" ?>" id="contact_<?php echo $item->getIdentity(); ?>">
    <span class='photo' style='background-image: url()'>
      <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
      <span class="inner"></span>
    </span>
    <span class="name"><?php echo $item->getTitle(); ?></span>
    <div class="clr"></div>
  </a>
<?php endforeach; ?>
<div class="clr"></div>
</div>
<?php endif; ?>

<?php if (!empty($this->items) && $this->items->getCurrentItemCount() > 0): ?>
<div class="all">
<?php foreach ($this->items as $item): ?>
  <?php $itemDisabled = in_array($item->getIdentity(), $this->disabledItems); ?>
  <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
  <a href='javascript:void(0)' <?php if ($itemDisabled && $this->disabled_label): ?>title = "<?php echo $this->disabled_label; ?>"<?php endif; ?>  class="item <?php if ($itemDisabled) echo "disabled" ?> <?php if ($itemChecked) echo "active" ?>" id="contact_<?php echo $item->getIdentity(); ?>">
    <span class='photo' style='background-image: url()'>
      <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
      <span class="inner"></span>
    </span>
    <span class="name"><?php echo $item->getTitle(); ?></span>
    <div class="clr"></div>
  </a>
<?php endforeach; ?>
<div class="clr"></div>
</div>
<?php endif; ?>

<?php endif; ?>