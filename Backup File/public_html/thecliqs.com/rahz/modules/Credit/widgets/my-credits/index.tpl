<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  06.01.12 18:46 TeaJay $
 * @author     Taalay
 */
?>

<div class="credit_my_credits_widget">
  <p class="credit_description"><?php echo $this->translate('CREDIT_My Credits Description')?></p>
  <ul class="credit_lists">
    <li>
      <div class="place_icon">
        <img src="application/modules/Credit/externals/images/stars/star_<?php echo $this->icon ?>.png" title="<?php echo $this->translate('Your Place')?>"/>
      </div>
      <div style="margin-top: 10px"><?php echo $this->translate('Your Place %s', ($this->place) ? $this->locale()->toNumber($this->place) : $this->translate('unknown'))?></div>
    </li>
    <li>
      <div class="my_credits_icon">
        <img src="application/modules/Credit/externals/images/current.png" title="<?php echo $this->translate('Current Balance')?>"/>
      </div>
      <div class="my_credits_desc">
        <b><?php echo $this->locale()->toNumber($this->credits->current_credit)?></b>
        <p><?php echo $this->translate('Current Balance')?></p>
      </div>
    </li>
    <li>
      <div class="my_credits_icon">
        <img src="application/modules/Credit/externals/images/earned.png" title="<?php echo $this->translate('Earned Credits')?>"/>
      </div>
      <div class="my_credits_desc">
        <b><?php echo $this->locale()->toNumber($this->credits->earned_credit)?></b>
        <p><?php echo $this->translate('Earned Credits')?></p>
      </div>
    </li>
    <li>
      <div class="my_credits_icon">
        <img src="application/modules/Credit/externals/images/spent.png" title="<?php echo $this->translate('Spent Credits')?>"/>
      </div>
      <div class="my_credits_desc">
        <b><?php echo $this->locale()->toNumber($this->credits->spent_credit)?></b>
        <p><?php echo $this->translate('Spent Credits')?></p>
      </div>
    </li>
  </ul>
</div>