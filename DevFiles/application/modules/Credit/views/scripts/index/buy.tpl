<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: buy.tpl  19.01.12 14:17 TeaJay $
 * @author     Taalay
 */
?>

<form method="post" action="<?php echo $this->url(array('action' => 'gateway'), 'credit_payment', true)?>" id="choose_price_form" class="" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h2 class="credit_popup_form_title">
        <span><?php echo $this->translate('Buy Credits')?></span>
      </h2>

      <div class="credit_form_current_balance">
        <i class="credit_icon"></i>
        <div class="credit_form_title_content">
          <div><?php echo $this->translate('Buy Credits')?></div>
          <span class="description"><?php echo $this->translate('Current Balance: %s Credits', $this->locale()->toNumber($this->current_balance))?></span>
        </div>
      </div>
      <div class="clr"></div>
      <div class="form-elements">
        <div>
          <p class="credit-form-description">
            <?php echo $this->translate('How many credits would you like to add to your account?')?>
          </p>
        </div>
        <div class="form-wrapper" id="choose-wrapper" style="width:100%">
          <div class="form-element" id="choose-element">
            <ul class="form-options-wrapper">
              <?php foreach ($this->prices as $key => $price) : ?>
                <li>
                  <input type="radio" value="<?php echo $price->payment_id?>" id="choose-<?php echo $price->payment_id?>" name="choose" <?php if (!$key) echo 'checked="checked"'?>>
                  <label for="choose-<?php echo $price->payment_id?>" style="margin: 10px 2px;">
                    <?php $bonus = ((100*$price->credit)/($price->price*$this->credits_for_one_unit) - 100); $bonus = ($bonus) ? '<span class="credit_payment_bonus">'.round($bonus, 2) . $this->translate('% bonus').'</span>' : ''?>
                    <?php echo '<span style="font-weight:bold">'.$this->translate(array("%s Credit for ", "%s Credits for ", $price->credit), $this->locale()->toNumber((int)$price->credit)).'</span>'.$this->locale()->toCurrency($price->price, $this->currency) . ' ' . $bonus;?>
                  </label>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div id="buttons-wrapper" class="form-wrapper credit_form_buttons">
          <fieldset id="fieldset-buttons" style="float: right; ">
            <button type="submit" id="submit" name="submit">
              <?php echo $this->translate('Continue')?>
            </button>
            <?php echo $this->translate(' or '); ?>
            <a onclick="parent.Smoothbox.close();" href="javascript:void(0);" type="button">
              <?php echo $this->translate('cancel')?>
            </a>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</form>