<?php
class Ynauction_Model_Album extends Core_Model_Item_Collection
{
    protected $_searchTriggers = false;
  protected $_parent_type = 'product';

  protected $_owner_type = 'product';

  protected $_children_types = array('ynauction_photo');

  protected $_collectible_type = 'ynauction_photo';

  public function getHref($params = array())
  {
    return $this->getDeal()->getHref($params);
  }

  public function getDeal()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('product');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('ynauction_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $dealPhoto ) {
      $dealPhoto->delete();
    }

    parent::_delete();
  }
}