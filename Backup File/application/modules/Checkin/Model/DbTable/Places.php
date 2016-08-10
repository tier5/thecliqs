<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Places.php 2012-03-06 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Model_DbTable_Places extends Engine_Db_Table
{
  /**
   * @param $page_ids
   * @return array|Zend_Db_Table_Rowset_Abstract|Engine_Db_Table_Rowset
   */
  public function findByPageIds($page_ids, $return_array = false)
  {
    if (!$page_ids) {
      return array();
    }

    $select = $this->select()
      ->where('object_type = ?', 'page')
      ->where('object_id IN (?)', $page_ids);

    $places = $this->fetchAll($select);

    if (!$return_array) {
      return $places;
    }

    $place_list = array();
    foreach ($places as $place) {
      $place_list[$place->object_id] = $place;
    }

    return $place_list;
  }

  /**
   * @param $google_ids
   * @return array|Zend_Db_Table_Rowset_Abstract|Engine_Db_Table_Rowset
   */
  public function findByGoogleIds($google_ids, $return_array = false)
  {
    if (!$google_ids) {
      return array();
    }

    $select = $this->select()
      ->where('google_id IN (?)', $google_ids);

    $places = $this->fetchAll($select);

    if (!$return_array) {
      return $places;
    }

    $place_list = array();
    foreach ($places as $place) {
      $place_list[$place->google_id] = $place;
    }

    return $place_list;
  }

  public function findByObject($object_type, $object_id)
  {
    $select = $this->select()
      ->where('object_type = ?', $object_type)
      ->where('object_id = ?', $object_id);

    return $this->fetchRow($select);
  }

  public function findByGoogleId($google_id)
  {
    $select = $this->select()
      ->where('google_id = ?', $google_id);

    return $this->fetchRow($select);
  }

  public function findByIds($place_ids)
  {
    $select = $this->select()
      ->where('place_id IN (?)', $place_ids);

    return $this->fetchAll($select);
  }

  public function getUserLastPlace($user_id)
  {
    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $this->info('name')))
      ->joinInner(array('c' => $checksTbl->info('name')), 'p.place_id = c.place_id')
      ->where('c.user_id = ?', $user_id)
      ->order('c.check_id DESC')
      ->limit(1);

    return $this->fetchRow($select);
  }

  public function getDetailedPlaces($place_ids)
  {
    $place_ids = ($place_ids) ? $place_ids : array(0);

    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $this->info('name')))
      ->joinInner(array('c' => $checksTbl->info('name')), 'p.place_id = c.place_id', array('visitors' => new Zend_Db_Expr('COUNT(DISTINCT `c`.`user_id`)')))
      ->where('p.place_id IN (?)', $place_ids)
      ->group('p.place_id');

    return $this->fetchAll($select);
  }
}