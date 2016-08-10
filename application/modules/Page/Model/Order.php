<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Order.php 27.07.11 15:15 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Order extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

  protected $_user;

  protected $_page;

  protected $_gateway;

  protected $_source;

  /**
   * Get the user attached to this order
   * 
   * @return User_Model_User
   */
  public function getUser()
  {
    if( empty($this->user_id) ) {
      return null;
    }
    if( null === $this->_user ) {
      $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
    }
    return $this->_user;
  }

	/**
   * Get the page attached to this order
   *
   * @return Page_Model_Page
   */
  public function getPage()
  {
    if( empty($this->page_id) ) {
      return null;
    }
    if( null === $this->_page ) {
      $this->_page = Engine_Api::_()->getItem('page', (int)$this->page_id);
    }
    return $this->_page;
  }

  /**
   * Get the gateway attached to this order
   * 
   * @return Payment_Model_Gateway
   */
  public function getGateway()
  {
    if( empty($this->gateway_id) ) {
      return null;
    }
    if( null === $this->_gateway ) {
      $this->_gateway = Engine_Api::_()->getItem('page_gateway', $this->gateway_id);
    }
    return $this->_gateway;
  }

  /**
   * Get the source object for this order (subscription, cart, etc)
   *
   * @return Core_Model_Item_Abstract
   */
  public function getSource()
  {
    if( empty($this->source_type) || empty($this->source_id) ) {
      return null;
    }
    if( null == $this->_source ) {
      $this->_source = Engine_Api::_()->getItem($this->source_type, (int)$this->source_id);
    }
    return $this->_source;
  }



  // Events

  public function onCancel()
  {
    if( $this->state == 'pending' ) {
      $this->state = 'cancelled';
    }
    $this->save();
    return $this;
  }

  public function onFailure()
  {
    if( $this->state == 'pending' ) {
      $this->state = 'failed';
    }
    $this->save();
    return $this;
  }

  public function onIncomplete()
  {
    if( $this->state == 'pending' ) {
      $this->state = 'incomplete';
    }
    $this->save();
    return $this;
  }

  public function onComplete()
  {
    if( $this->state == 'pending' ) {
      $this->state = 'complete';
    }
    $this->save();
    return $this;
  }
}