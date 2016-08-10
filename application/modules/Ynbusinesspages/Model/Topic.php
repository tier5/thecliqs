<?php

class Ynbusinesspages_Model_Topic extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'ynbusinesspages_business';
  
  protected $_type = 'ynbusinesspages_topic';
  
  protected $_owner_type = 'user';

  protected $_children_types = array('ynbusinesspages_post');
  
  public function isSearchable()
  {
    $business = $this->getParentBusiness();
    if( !($business instanceof Core_Model_Item_Abstract) ) {
      return false;
    }
    return $business->isSearchable();
  }
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynbusinesspages_extended',
      'controller' => 'topic',
      'action' => 'view',
      'business_id' => $this->business_id,
      'topic_id' => $this->getIdentity(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    $firstPost = $this->getFirstPost();
    return ( null !== $firstPost ? Engine_String::substr($firstPost->body, 0, 255) : '' );
  }
  
  public function getParentBusiness()
  {
    return Engine_Api::_()->getItem('ynbusinesspages_business', $this->business_id);
  }

  public function getFirstPost()
  {
    $table = Engine_Api::_()->getDbtable('posts', 'ynbusinesspages');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id ASC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPost()
  {
    $table = Engine_Api::_()->getItemTable('ynbusinesspages_post');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id DESC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('ynbusinesspages_business');
  }

  // Internal hooks
  protected function _insert()
  {
    if( !$this->business_id )
    {
      throw new Exception('Cannot create topic without business_id');
    }
    parent::_insert();
  }
  
}