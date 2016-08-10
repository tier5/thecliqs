<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Taxes.php 11.04.12 15:50 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Taxes extends Engine_Db_Table
{
  public function getTaxes()
  {
    return $this->fetchAll();
  }

  public function getRow($tax_id)
  {
    $select = $this->select()
      ->where('tax_id = ?', $tax_id);
    ;

    return $this->fetchRow($select);
  }

  public function getTaxesArray()
  {
    $taxes = $this->getTaxes();
    $array = array(0 => ' ');
    foreach ($taxes as $tax) {
      $array[$tax->tax_id] = number_format($tax->percent, 2, '.', '') . '% - ' . $tax->title;
    }
    return $array;
  }
}
