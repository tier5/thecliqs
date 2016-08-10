<?php
/**
 * 
 * @author MinhNC
 */
class Ynpayment_Model_Subscription extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  protected $_user;
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
}