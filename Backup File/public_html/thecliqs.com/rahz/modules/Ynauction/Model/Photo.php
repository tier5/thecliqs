<?php
class Ynauction_Model_Photo extends Core_Model_Item_Collectible
{
    protected $_searchTriggers = false;
  protected $_parent_type = 'ynauction_album';

  protected $_owner_type = 'user';

  protected $_collection_type = 'ynauction_album';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'ynauction_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'product_id' => $this->getCollection()->getOwner()->getIdentity(),
      'photo_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) ) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getDeal()
  {
    return Engine_Api::_()->getItem('ynauction_product', $this->product_id);
  }

  public function isSearchable()
  {
    $collection = $this->getCollection();
    if( !$collection instanceof Core_Model_Item_Abstract )
    {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('product');
  }


  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _postDelete()
  {
    if( $this->_disableHooks ) return;

    // This is dangerous, what if something throws an exception in postDelete
    // after the files are deleted?
    try
    {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
	  if(is_object($file)){
      	$file->remove();
	  }
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
      if(is_object($file)){
      	$file->remove();
	  }

      $album = $this->getCollection();

      if( (int) $album->photo_id == (int) $this->getIdentity() )
      {
		$album->photo_id = $this->getNextCollectible()->getIdentity();
		$album->save();
      }
    }
    catch( Exception $e )
    {
      // @todo completely silencing them probably isn't good enough
      //throw $e;
    }
  }
}