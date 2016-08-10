<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       14.08.12
 * @time       15:38
 */?>
<span class="author">
  <a href="<?php echo $this->fundraise->getOwner()->getHref();?>" title="<?php echo $this->fundraise->getOwner()->getTitle(); ?>"><?php echo $this->fundraise->getOwner()->getTitle(); ?></a> <?php echo $this->translate('is raising funds for');?>
</span>
<br />

<div class="ffor">
  <div class="photo">
    <?php echo $this->htmlLink($this->donation->getHref(), $this->itemPhoto($this->donation, 'thumb.normal'))?>
  </div>

  <?php echo $this->htmlLink($this->donation->getHref(), $this->donation->getTitle()) ?>

  <div class="desc">
    <?php echo $this->donation->getDescription(false,false) ?>
  </div>
</div>
