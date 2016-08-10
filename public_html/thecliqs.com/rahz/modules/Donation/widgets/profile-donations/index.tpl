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
 * @time       13:39
 */?>

<h3> <?php echo $this->translate('Donations (%s)', $this->count)?> </h3>
<ul class="members_donations">
  <?php foreach( $this->paginator as $transaction ): ?>
    <li>
      <?php echo $this->htmlLink($transaction->getOwner()->getHref(), $this->itemPhoto($transaction->getOwner(), 'thumb.icon'),
      array('class' => 'member_donation_members_icon')) ?>
      <div class="member_donation_details">
        <div class="desc">
          <?php echo $transaction->description;?>
        </div>
        <span>
          <?php echo $this->translate('Donation by ');?>
          <?php echo $this->htmlLink($transaction->getOwner()->getHref(), $transaction->getOwner()->getTitle(),
          array('class' => 'member_donation_members_icon')) ?>
          <?php echo $this->timestamp($transaction->creation_date); ?>
        </span>
      </div>
      <div class="member_donation_amount"><?php echo $this->locale()->toCurrency((double)$transaction->amount, $transaction->currency); ?></div>
      <br/>
    </li>
  <?php endforeach;?>
</ul>