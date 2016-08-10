<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Locations.php 2010-12-17 22:10 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Weather_Model_DbTable_Locations extends Engine_Db_Table
{
  protected $_rowClass = "Weather_Model_Location";

  public function getUserLocation($user_id)
  {
    $select = $this->select()
      ->where('user_id = ?', $user_id);

    return $this->fetchRow($select);
  }

  public function getObjectLocation($object_type, $object_id)
  {
    $select = $this->select()
      ->where('object_type = ?', $object_type)
      ->where('object_id = ?', $object_id);

    return $this->fetchRow($select);
  }
}