<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Product.php 4/6/12 2:42 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Productships extends Engine_Db_Table
{
  public function getLocation($location_id, $product_id)
  {
    /**
     * @var $table Store_Model_DbTable_Locations
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $this->info('name')))
      ->joinInner(array('l' => $table->info('name')), 'l.location_id = p.location_id', array('l.location'))
      ->where('p.location_id = ?', $location_id)
      ->where('p.product_id = ?', $product_id);

    return $this->fetchRow($select);
  }
}
