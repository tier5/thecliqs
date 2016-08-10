<?php
class Ynlistings_Model_Topic extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;  
  protected $_parent_type = 'ynlistings_listing';

  protected $_owner_type = 'user';

  protected $_children_types = array('ynlistings_post');
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynlistings_extended',
      'controller' => 'topic',
      'action' => 'view',
      'listing_id' => $this->listing_id,
      'topic_id' => $this->getIdentity(),
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getDescription()
  {
    $firstPost = $this->getFirstPost();
    if(null !== $firstPost)  {
      $content = strip_tags($firstPost->body);
      return Engine_String::substr($content, 0, 255);
    }
    return '';
  }
  
  public function getParentListing()
  {
    return Engine_Api::_()->getItem('ynlistings_listing', $this->listing_id);
  }

  public function getFirstPost()
  {
    $table = Engine_Api::_()->getDbtable('posts', 'ynlistings');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id ASC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPost()
  {
    $table = Engine_Api::_()->getItemTable('ynlistings_post');
    $select = $table->select()
      ->where('topic_id = ?', $this->getIdentity())
      ->order('post_id DESC')
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getLastPoster()
  {
    return Engine_Api::_()->getItem('user', $this->lastposter_id);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('ynlistings_listing');
  }

  // Internal hooks

  protected function _insert()
  {
    if( $this->_disableHooks ) return;
    
    if( !$this->listing_id )
    {
      throw new Exception('Cannot create topic without listing_id');
    }
    parent::_insert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    // Delete all child posts
    $postTable = Engine_Api::_()->getItemTable('ynlistings_post');
    $postSelect = $postTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach( $postTable->fetchAll($postSelect) as $listingPost ) {
      $listingPost->disableHooks()->delete();
    }
	// Delete topicwatch
    $topicWatchTable = Engine_Api::_()->getDbTable('topicWatches','ynlistings');
    $topicWatchSelect = $topicWatchTable->select()->where('topic_id = ?', $this->getIdentity());
    foreach( $topicWatchTable->fetchAll($topicWatchSelect) as $listingTopicWatch ) {
      $listingTopicWatch->delete();
    }
    parent::_delete();
  }

  public function canEdit($user)
  {
    return $this->getParent()->authorization()->isAllowed($user, 'edit') || $this->getParent()->authorization()->isAllowed($user, 'topic.edit') || $this->isOwner($user);
  }
}