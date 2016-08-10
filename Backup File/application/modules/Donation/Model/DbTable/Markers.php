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

class Donation_Model_DbTable_Markers extends Engine_Db_Table
{
  protected $_rowClass = 'Donation_Model_Marker';

  public function getByDonationIds($donationIds = array())
  {
    if (!$donationIds) {
      return array();
    }

    $select = $this->select()
      ->where('page_id IN (?)', $donationIds);

    return $this->fetchAll($select);
  }
}