<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       03.09.12
 * @time       11:51
 */?>
<?php if ($this->paginator->getTotalItemCount() > 0 ):?>
<div class="donors_results" id="donors_results">
    <h3><?php echo $this->translate("Top Donors List"); ?></h3>
  <ul>
    <?php $i = 1; foreach( $this->paginator as $donation ): ?>
    <li>
      <div class="race">
        <h2 style="font-size: 35px"><?php echo $i ++; ?></h2>
      </div>
      <?php echo $this->htmlLink($donation->getOwner()->getHref(), $this->itemPhoto($donation->getOwner(), 'thumb.icon'), array('class' => 'donors_thumb')) ?>
      <div class='donors_info'>
        <div class='donors_name'>
          <?php echo $this->htmlLink($donation->getOwner()->getHref(), $donation->getOwner()->getTitle()) ?>
        </div>
        <div class="amount">
          <div class="coins_icon">
            <span style="font-weight: bold">
              <?php echo $this->locale()->toCurrency((double)$donation->amounted, $this->currency) ?>
              <?php echo $this->translate('donated')?>
            </span>
          </div>
        </div>
      </div>
    </li>
    <?php endforeach; ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  </ul>
</div>
<?php else :?>
 <div class="tip">
   <span><?php echo $this->translate("DONATION_Nobody has donated yet");?></span>
 </div>
<?php endif;?>