<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Locations.php 3/22/12 3:27 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Locations extends Engine_Db_Table
{
  protected $_rowClass = 'Store_Model_Location';

  /**
   * Get Store Locations
   * @param int $parent_id
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getLocations($parent_id = 0)
  {
    $select = $this->select()
      ->where('parent_id = ?', $parent_id);

    return $this->fetchAll($select);
  }

  public function getTreeIds($parent_id)
  {
    $parent_id = (int)$parent_id;
    if (!$parent_id) {
      return 0;
    }

    $id_array = array();
    $tmp_ids = $parent_id;
    do {
      $id_array[] = $ids = $tmp_ids;

      $tmp_ids = $this->select()
        ->from($this, new Zend_Db_Expr('GROUP_CONCAT(location_id)'))
        ->where("parent_id IN($ids)")
        ->query()
        ->fetchColumn();
    } while ($tmp_ids);

    return implode(',', $id_array);
  }

  public function getParentId($location_id)
  {
    return (int)$this->select()
      ->from($this, new Zend_Db_Expr('parent_id'))
      ->where('location_id = ?', $location_id)
      ->query()
      ->fetchColumn();
  }
}