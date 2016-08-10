<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Shippings.php 4/3/12 4:46 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Locationships extends Engine_Db_Table
{
  public function hasShippingLocations($page_id = 0)
  {
    return $this->fetchRow(array('page_id = ?' => $page_id)) ? true : false;
  }
}