<?php

class Ynbusinesspages_Model_Announcement extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'ynbusinesspages_business';

  protected $_owner_type = 'ynbusinesspages_business';
  
  protected $_searchTriggers = false;
  
  public function getHref($params = array())
    {
    $params = array_merge(array(
      'route' => 'ynbusinesspages_profile',
      'reset' => true,
      'id' => $this->$business_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getDescription()
  {
    // Remove bbcode
    $desc = strip_tags($this->body);
    return Engine_String::substr($desc, 0, 255);
  }
  
  protected function _update()
  {
    parent::_update();
  }
}