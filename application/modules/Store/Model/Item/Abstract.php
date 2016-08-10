<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Abstract.php 9339 2011-09-29 23:03:01Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Store_Model_Item_Abstract extends Core_Model_Item_Abstract
{

  /**
   * Item's active status
   *
   * @var $_active Boolean
   */
  protected $_active;

  /**
   * The price of the item
   *
   * @var $_price Float
   */
  protected $_price;

  public function init()
  {
    parent::init();
    if (isset($this->price)) {
      $this->setPrice($this->price);
    } elseif (isset($this->amt)) {
      $this->setPrice($this->amt);
    }
    if (isset($this->active)) {
      $this->setActive($this->active);
    }
  }


  /**
   * @param boolean $active
   *
   * @param string  $status
   *
   * @return void
   */
  public function setActive($active, $status = 'success')
  {
    $this->_active = (bool)$active;

    if (property_exists($this, 'active') && $this->active != $active) {
      $this->active = (bool)$active;
      $this->save();
    }
  }

  /**
   * @return boolean
   */
  public function getActive()
  {
    return $this->_active;
  }

  /**
   * @param Float $price
   *
   * @return void
   */
  public function setPrice($price)
  {
    $this->_price = round($price, 2);
  }

  /**
   * @return Float
   */
  public function getPrice()
  {
    return round($this->_price, 2);
  }
}
