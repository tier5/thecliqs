<?php

class Yncontest_Model_Announcement extends Core_Model_Item_Abstract
{
  //protected $_parent_type = 'group';

  //protected $_owner_type = 'group';

//   public function getHref($params = array())
//     {
//     $params = array_merge(array(
//       'route' => 'group_profile',
//       'reset' => true,
//       'id' => $this->group_id,
//     ), $params);
//     $route = $params['route'];
//     $reset = $params['reset'];
//     unset($params['route']);
//     unset($params['reset']);
//     return Zend_Controller_Front::getInstance()->getRouter()
//       ->assemble($params, $route, $reset);
//   }

//   public function getDescription()
//   {
//     // Remove bbcode
//     $desc = strip_tags($this->body);
//     return Engine_String::substr($desc, 0, 255);
//   }
  
//   protected function _update()
//   {
//     parent::_update();
//   }
}