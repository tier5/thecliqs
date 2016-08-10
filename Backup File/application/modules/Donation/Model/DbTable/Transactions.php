<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 07.08.12
 * Time: 17:33
 * To change this template use File | Settings | File Templates.
 */
class Donation_Model_DbTable_Transactions extends Engine_Db_Table
{
  protected $_rowClass = "Donation_Model_Transaction";

  public function getDonations($params = array()){
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    return $this->select()
      ->where('item_id = ?', $params['donation_id'])
      ->where('state = ?', 'completed')
      ->where('user_id > 0')
      ->order('amount DESC')
      ->limit($settings->getSetting('donations.per.page', 10));
  }

}
