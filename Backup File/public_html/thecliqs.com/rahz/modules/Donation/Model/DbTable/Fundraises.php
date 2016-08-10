<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       08.08.12
 * @time       17:51
 */
class Donation_Model_DbTable_Fundraises extends Engine_Db_Table
{
  protected $_rowClass = "Donation_Model_Fundraise";

  public function getFundraises($id = 0)
  {
    return $this->select()
      ->where('parent_id = ?', $id)
      ->order('raised_sum')
      ->limit(10);
  }
}
