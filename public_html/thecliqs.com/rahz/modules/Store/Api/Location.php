<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Location.php 3/22/12 6:21 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Api_Location extends Core_Api_Abstract
{
  /**
   * @var $_table Store_Model_DbTable_Locations
   */
  protected $_table;

  /**
   * @var $_tableShips Store_Model_DbTable_Locationships
   */
  protected $_tableShips;

  public function __construct()
  {
    $this->_table = Engine_Api::_()->getDbTable('locations', 'store');
    $this->_tableShips = Engine_Api::_()->getDbTable('locationships', 'store');
  }

  public function getPaginator($page_id, $page = 1, $parent_id = 0, $type = 'supported', $product_id = 0)
  {
    switch ($type) {

      case 'supported':
        $selectB = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->joinInner(array('c' => $this->_tableShips->info('name')), 'c.location_id = b.location_id', array())
          ->where('c.page_id = ?', $page_id)
          ->where('b.parent_id = a.location_id')
          ->group('b.parent_id');

        $select = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), array('a.location', 'sub_locations' => new Zend_Db_Expr('(' . $selectB . ')'), 'supported' => 'IF(l.locationship_id > 0, 1, 0)'))
          ->joinInner(array('l' => $this->_tableShips->info('name')), 'l.location_id = a.location_id', array('l.*'))
          ->where('l.page_id = ?', $page_id)
          ->where('a.parent_id = ?', $parent_id)
          ->order('a.location ASC');
        break;


      case 'supported-add':
        //he@todo Should we return all available locations OR just supported ones?
        $selectB = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->joinLeft(array('p' => $this->_tableShips->info('name')), 'p.location_id = b.location_id && p.page_id = ' . $page_id, array())
          ->where('b.parent_id = a.location_id')
          ->where('IF(p.location_id > 0, 0, 1)')
          ->group('b.parent_id');

        $select = $this->_tableShips->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), new Zend_Db_Expr('DISTINCT a.*, (' . $selectB . ') as sub_locations'))
          ->joinLeft(array('p' => $this->_tableShips->info('name')), 'p.location_id = a.location_id && p.page_id = ' . $page_id, array())
          ->where('a.parent_id = ?', $parent_id)
          ->order('a.location ASC');
        if ($parent_id) {
          $select->where('IF(p.location_id > 0, 0, 1) || (' . $selectB . ') > 0');
        } else {
          $select->where('IF(p.location_id > 0, 0, 1) && (' . $selectB . ') > 0');
        }
        break;


      case 'product':
        /**
         * @var $productshipTable Store_Model_DbTable_Productships
         */
        $productshipTable = Engine_Api::_()->getDbTable('productships', 'store');

        //he@todo Should we return all available locations OR just supported ones?
        $selectB = $this->_table->select()
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->joinInner(array('s' => $this->_tableShips->info('name')), 's.location_id = b.location_id', array())
          ->joinInner(array('c' => $productshipTable->info('name')), 'c.location_id = s.location_id', array())
          ->where('s.page_id = ?', $page_id)
          ->where('b.parent_id = a.location_id')
          ->where('c.product_id = ?', $product_id)
          ->group('b.parent_id');

        $select = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), new Zend_Db_Expr('a.location, (' . $selectB . ') as sub_locations '))
          ->joinInner(array('s2' => $this->_tableShips->info('name')), 's2.location_id = a.location_id', array())
          ->joinInner(array('l' => $productshipTable->info('name')), 'l.location_id = s2.location_id')
          ->where('s2.page_id = ?', $page_id)
          ->where('a.parent_id = ?', $parent_id)
          ->where('l.product_id = ?', $product_id)
          ->order('a.location ASC');

        break;


      case 'product-add':
        /**
         * @var $productshipTable Store_Model_DbTable_Productships
         */
        $productshipTable = Engine_Api::_()->getDbTable('productships', 'store');

        //he@todo Should we return all available locations OR just supported ones?
        $selectB = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->joinLeft(array('s' => $this->_tableShips->info('name')), 's.location_id = b.location_id', array())
          ->joinLeft(array('p' => $productshipTable->info('name')), 'p.location_id = s.location_id && p.product_id = ' . $product_id, array())
          ->where('s.page_id = ?', $page_id)
          ->where('b.parent_id = a.location_id')
          ->where('IF(p.location_id > 0, 0, 1)')
          ->group('b.parent_id');

        $select = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), new Zend_Db_Expr('DISTINCT a.*, (' . $selectB . ') as sub_locations'))
          ->joinLeft(array('s2' => $this->_tableShips->info('name')), 's2.location_id = a.location_id', array())
          ->joinLeft(array('p' => $productshipTable->info('name')), 'p.location_id = s2.location_id && p.product_id = ' . $product_id, array())
          ->where('s2.page_id = ?', $page_id)
          ->where('a.parent_id = ?', $parent_id)
          ->where('IF(p.location_id > 0, 0, 1) || (' . $selectB . ') > 0')
          ->order('a.location ASC');
        break;

      case 'store-default':
        $selectB = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->joinInner(array('c' => $this->_tableShips->info('name')), 'c.location_id = b.location_id', array())
          ->where('b.parent_id = a.location_id')
          ->group('b.parent_id');

        $select = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), array('a.location', 'sub_locations' => new Zend_Db_Expr('(' . $selectB . ')'), 'supported' => 'IF(l.locationship_id > 0, 1, 0)'))
          ->joinInner(array('l' => $this->_tableShips->info('name')), 'l.location_id = a.location_id', array('l.*'))
          ->where('a.parent_id = ?', $parent_id)
          ->order('l.creation_date DESC');

        break;


      default:
        $selectB = $this->_table->select()
          ->from(array('b' => $this->_table->info('name')), new Zend_Db_Expr('COUNT(b.location_id)'))
          ->where('b.parent_id = a.location_id')
          ->group('b.parent_id');

        $select = $this->_table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $this->_table->info('name')), new Zend_Db_Expr('DISTINCT a.*, (' . $selectB . ') as sub_locations '))
          ->joinLeft(array('l' => $this->_tableShips->info('name')), 'l.location_id = a.location_id and l.page_id = 0', array('supported' => 'IF((l.locationship_id > 0), 1, 0)'))
          ->where('a.parent_id = ?', $parent_id)
          ->order('sub_locations DESC')
          ->order('a.location ASC');

        break;
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);

    return $paginator;
  }

  public function isLocationSupported($location_id, $page_id)
  {
    return (bool)$this->_tableShips
      ->select()
      ->from($this->_tableShips, new Zend_Db_Expr("IF(location_id > 0, 1, 0)"))
      ->where('location_id = ?', (int)$location_id)
      ->where('page_id = ?', (int)$page_id)
      ->query()
      ->fetchColumn();
  }
}
