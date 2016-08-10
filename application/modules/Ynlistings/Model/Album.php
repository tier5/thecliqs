<?php
class Ynlistings_Model_Album extends Core_Model_Item_Collection
{
  protected $_searchTriggers = false;	
  protected $_parent_type = 'ynlistings_listing';

  protected $_owner_type = 'user';

  protected $_children_types = array('ynlistings_photo');

  protected $_collectible_type = 'ynlistings_photo';

  public function getHref($params = array())
    {
    $params = array_merge(array(
      'route' => 'ynlistings_extended',
      'reset' => true,
      'controller' => 'album',
      'action' => 'view',
      'listing_id' => $this->listing_id,
      'album_id' => $this->album_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getAlbumHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynlistings_extended',
      'reset' => true,
      'controller' => 'album',
      'action' => 'view',
      'listing_id' => $this->listing_id,
      'album_id' => $this->album_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getListing()
  {
//    return $this->getOwner();
      return Engine_Api::_()->getItem('ynlistings_listing', $this->listing_id);
  }

  public function getMemberOwner(){
    return Engine_Api::_()->user()->getUser($this->user_id);
  }
  
  public function getAuthorizationItem()
  {
    return $this->getParent('ynlistings_listing');
  }

   public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getPhotoCount($user_id){
    $table = Engine_Api::_()->getItemTable('ynlistings_photo');
    $name = $table->info('name');
    $select = $table->select()
                    ->from($name, 'COUNT(*) AS count')
                    ->where("album_id = $this->album_id")
                    ->where("user_id = $user_id");
    return $select->query()->fetchColumn(0);
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('ynlistings_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $listingPhoto ) {
      $listingPhoto->delete();
    }

    parent::_delete();
  }

 //overwrite function getCollectiblesPaginator
  public function getCollectiblesPaginator($params = null)
  {
  	$tbl_photos = Engine_Api::_()->getItemTable('ynlistings_photo');
	$photoSelect = $tbl_photos->select()->where('album_id = ?', $this->getIdentity());
	
	if(isset($params['is_featured']))
	{
		 $photoSelect-> where("is_featured = ?", $params['is_featured'] );
	}
	
	return Zend_Paginator::factory($photoSelect);
  }
}