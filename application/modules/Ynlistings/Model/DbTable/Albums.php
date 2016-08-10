<?php
class Ynlistings_Model_DbTable_Albums extends Engine_Db_Table
{
  protected $_name = 'ynlistings_albums';
  protected $_rowClass = 'Ynlistings_Model_Album';

  public function getAlbumsPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getAlbumsSelect($params));
  }

  public function getAlbumsSelect($params = array()){

    //Get album table
    $table = Engine_Api::_()->getItemTable('ynlistings_album');
    $tableName = $table->info('name');

    $select = $table
      ->select()
      ->from($tableName);

    //Search
    if(!empty($params['search'])){
      $select->where('title LIKE ? OR Description LIKE ?','%'.$params['search'].'%');
    }

     // User
    if( !empty($params['user_id']) ) {
      $select
        ->where('user_id = ?', $params['user_id']);
    }
	
	$select -> where("title <> 'Listing Profile'");
	
    if(isset ($params['listing_id'])){
        $select
        ->where('listing_id = ?', $params['listing_id']);
    }

    // Order
    switch( $params['order'] ) {
      case 'view':
          $select -> order('view_count DESC');
     break;
      case 'comment':
          $select -> order ('comment_count DESC');
      break;
      case 'modified_date':
          $select -> order ('modified_date DESC');
      break;
      case 'recent':
      default:
          $select -> order('creation_date DESC');
      break;
    }

    return $select;
  }
}