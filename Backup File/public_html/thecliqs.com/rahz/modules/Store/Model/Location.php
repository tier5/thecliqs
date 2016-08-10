<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Location.php 3/22/12 3:27 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Location extends Core_Model_Item_Abstract
{
  protected $_type = 'store_location';

  public $title;
  /**
   * @var $_parent Store_Model_Location
   */
  protected $_parent;

  public function init()
  {
    $this->title = $this->location;
  }

  public function delete()
  {
    $locations = $this->getTable()->getLocations($this->getIdentity());

    foreach ($locations as $location) {
      $location->delete();
    }

    return parent::delete();
  }

  /**
   * @return Core_Model_Item_Abstract|Store_Model_Location
   */
  public function getParent()
  {
    if ($this->_parent == null) {
      $select = $this->getTable()->select()
        ->where('location_id = ?', $this->parent_id);
      $this->_parent = $this->getTable()->fetchRow($select);
    }

    $this->__toString();
    return $this->_parent;
  }

  /**
   * @param \Store_Model_Location $parent
   * @return void
   */
  public function setParent($parent)
  {
    $this->_parent = $parent;
  }

  public function isSubLocationExists()
  {
    if ($this->parent_id === 0) {
      return (boolean)Engine_Api::_()->getDbTable('locations', 'store')->fetchRow(array('parent_id = ?' => $this->getIdentity()));
    } else {
      return true;
    }
  }
}
