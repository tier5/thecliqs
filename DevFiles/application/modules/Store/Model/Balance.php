<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Balance.php 5/8/12 6:38 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Balance extends Core_Model_Item_Abstract
{
  protected $_owner_type = 'page';

  public function increase($amt)
  {
    $this->current_amt += (double)$amt;
    return $this->save();
  }

  public function decrease($amt)
  {
    $this->current_amt -= (double)$amt;
    return $this->save();
  }

  /**
   * @return float
   */
  public function getBalance()
  {
    return (double)$this->current_amt;
  }

  //Transfer Methods
  public function getTransfer()
  {
    return (double)$this->transferred_amt;
  }

  public function increaseTransfer($amt)
  {
    $this->transferred_amt +=(double)$amt;
    $this->transferred_date = new Zend_Db_Expr('NOW()');
    return $this->save();
  }

  //Requested Methods
  public function getRequested()
  {
    return (double)$this->requested_amt;
  }

  public function increaseRequested($amt)
  {
    $this->requested_amt +=(double)$amt;
    $this->requested_date = new Zend_Db_Expr('NOW()');
    return $this->save();
  }

  public function decreaseRequested($amt)
  {
    $this->requested_amt -=(double)$amt;
    return $this->save();
  }

  public function getPending()
  {
    return (double) $this->pending_amt;
  }

  public function increasePending($amt)
  {
    $this->pending_amt +=(double)$amt;
    return $this->save();
  }

  public function decreasePending($amt)
  {
    $this->pending_amt -=(double)$amt;
    return $this->save();
  }
}
