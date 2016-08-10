<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Payments.php 18.01.12 13:30 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_DbTable_Payments extends Engine_Db_Table
{
  protected $_rowClass = "Credit_Model_Payment";

  public function getPrices()
  {
    return $this->fetchAll(
      $this->select()
        ->order('credit ASC')
    );
  }

  public function checkPrice($values)
  {
    $select = $this->select()
      ->where('price = ?', $values['price'])
      ->orWhere('credit = ?', $values['credit'])
      ->limit(1);
    if (null !== $this->fetchRow($select)) {
      return true;
    } else {
      return false;
    }
  }

  public function getPrice($payment_id = null)
  {
    if ($payment_id !== null) {
      $select = $this->select()
        ->where('payment_id = ?', $payment_id);
    } else {
      $select = $this->select();
    }
    return $this->fetchRow($select);
  }

  public function setPrice()
  {
    if (!$this->getPrice()) {
      $this->insert(array(
        'credit' => 100,
        'price'  => 1
      ));
    }
  }

  public function deletePrice($payment_id)
  {
    $this->delete("payment_id = {$payment_id}");
  }
}
