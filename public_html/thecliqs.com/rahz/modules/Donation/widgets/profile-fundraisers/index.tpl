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
 * @time       13:41
 */?>
<h3> <?php echo $this->translate('Fundraisers (%s)', $this->count)?> </h3>
<ul class="members_fundraisers">
  <?php foreach( $this->paginator as $fundraise ): ?>
    <li>
      <?php echo $this->htmlLink($fundraise->getOwner()->getHref(), $this->itemPhoto($fundraise->getOwner(), 'thumb.icon'),
            array('class' => 'member_fundraiser_members_icon')) ?>
      <div class="member_fundraiser_details">
        <?php echo $this->htmlLink($fundraise->getOwner()->getHref(), $fundraise->getOwner()->getTitle(),array('class' => 'member_donation_members_icon')) ?>
        <span class="member_fundraiser_amount"><?php echo $this->translate('%1$s raised so far', $this->locale()->toCurrency((double)$fundraise->raised_sum, $this->currency)); ?></span>
      </div>
      <a class="see_fundraiser_details" href="<?php echo $fundraise->getHref()?>"><?php echo $this->translate('See fundraiser page')?></a>
      <br/>
    </li>
  <?php endforeach; ?>
</ul>

