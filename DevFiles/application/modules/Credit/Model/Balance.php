<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Balance.php 13.03.12 10:23 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_Balance extends Core_Model_Item_Abstract
{
  public function returnCredits($credit)
  {
    if ($credit < 0) {
      $credit = (-1)*$credit;
    }

    $this->current_credit += $credit;
    $this->save();
  }


  public function temporaryPay($credit)
  {
    if ($credit > 0) {
      $credit = (-1)*$credit;
    }
    $this->current_credit += $credit;
    $this->save();
  }

  public function setCredits($credit)
  {
    $this->current_credit += $credit;
    if ($credit > 0) {
      $this->earned_credit += $credit;
    } else {
      $this->spent_credit -= $credit;
    }

    $this->save();
  }

  public function settingCredits($credits)
  {
    $this->current_credit = $credits;
    $this->save();
  }
}
