<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:58
 */
?>

<div class="he-item-photo">
  <?php if ($this->donation->type != 'fundraise') :?>
    <?php echo $this->htmlLink($this->donation->getHref(), $this->itemPhoto($this->donation, 'thumb.profile')); ?>
  <?php else: ?>
    <?php echo $this->htmlLink($this->donation->getOwner()->getHref(), $this->itemPhoto($this->donation->getOwner(), 'thumb.icon')) ?>
  <?php endif; ?>
</div>