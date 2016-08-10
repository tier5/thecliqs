<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photos.php 08-02-13 17:32 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Model_DbTable_Photos extends Album_Model_DbTable_Photos
{

  public function selectPhotos($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();

    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $photosTbl = Engine_Api::_()->getDbTable('photos', 'album');
    $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');
    $tagmapTbl = Engine_Api::_()->getDbtable('tagMaps', 'core');


    $user_id = $viewer->getIdentity();
    $friend_ids = array();

    // if a user is logged
    if ($viewer->getIdentity()) {
      $friend_ids = $viewer->membership()->getMembershipsOfIds();
    }
    // add 0 in order to don't break the query
    if (empty($friend_ids)) {
      $friend_ids = array(0);
    }



    // The albums select
    $select = $photosTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('photos' => $photosTbl->info('name')), new Zend_Db_Expr('photos.photo_id AS id, "album_photo" AS type, photos.view_count AS view_count, photos.creation_date AS creation_date'))
      ->joinLeft(array('auth' => $authallowTbl->info('name')), "auth.resource_type = 'album' AND auth.resource_id = photos.album_id AND auth.action = 'view'", array())
      ->joinLeft(array('albums' => $albumsTbl->info('name')), 'albums.album_id = photos.album_id', array())
      ->where("auth.role = 'everyone' OR (auth.role = 'registered' AND $user_id > 0) OR (auth.role = 'owner_member' AND photos.owner_type = 'user' AND photos.owner_id IN (?)) OR (photos.owner_type = 'user' AND photos.owner_id = $user_id)", $friend_ids);


    // Page albums select

    $pageSelect = new Zend_Db_Select($db);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')){

      $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
      $pagePhotosTable = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
      $pageAlbumTable = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
      $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

      $pageSelect = $pagePhotosTable->select()
        ->setIntegrityCheck(false)
        ->from(array('photos' => $pagePhotosTable->info('name')), new Zend_Db_Expr('photos.pagealbumphoto_id AS id, "pagealbumphoto" AS type, albums.view_count AS view_count, photos.creation_date AS creation_date'))
        ->join(array('albums' => $pageAlbumTable->info('name')), 'albums.pagealbum_id = photos.collection_id', array())
        ->join(array('pages' => $pageTable->info('name')), 'pages.page_id = albums.page_id', array())
        ->joinLeft(array('auth' => $authallowTbl->info('name')), "auth.resource_type = 'page' AND auth.resource_id = albums.page_id AND auth.action = 'view'", array())
        ->joinLeft(array('li' => $listitemTbl->info('name')), 'auth.role_id = li.list_id', array())
        ->where("auth.role = 'everyone' OR (auth.role = 'registered' AND $user_id > 0) OR (li.child_id = $user_id)");
    }


    // Filter by keyword
    if (isset($params['search_photos']) && $params['search_photos'] !== '') {

      $select->where("(photos.title LIKE '%{$params['search_photos']}%' OR photos.description LIKE '%{$params['search_photos']}%')");
      $pageSelect->where("(photos.title LIKE '%{$params['search_photos']}%' OR photos.description LIKE '%{$params['search_photos']}%')");

    }

    // Filter by featured
    if (!empty($params['featured'])) {

      $select->where('photos.he_featured = ?', $params['featured']);
      $pageSelect->where('photos.pagealbumphoto_id = 0'); // nothing from page photos

    }

    // Filter only tagged
    if (isset($params['type']) && $params['type'] == 'tagged') {

      $select
        ->joinLeft(array('tm' => $tagmapTbl->info('name')), "tm.resource_type = 'album_photo' AND tm.tag_type='user'", array())
        ->where('tm.tag_id=?', $params['owner']->getIdentity())
        ->where('tm.resource_id=photos.photo_id');

      $pageSelect->where('albums.album_id = 0'); // nothing from page photos

    }

    // Filter by owner
    if (isset($params['owner'])) {
      $select->where('photos.owner_type = "user" AND photos.owner_id = ?', $params['owner']->getIdentity());
      $pageSelect->where('photos.owner_id = ?', $params['owner']->getIdentity());
    }

    // Filter by category
    if (isset($params['category']) && $params['category'] > 0) {

      $select->where('albums.category_id = ?', $params['category']);
      $pageSelect->where('albums.album_id = 0'); // nothing from page photos

    }

    // Filter Search (bool)
    if (isset($params['search']) && $params['search']) {
      $select->where("albums.search = ?", $params['search']);
    }


    // The final manipulations

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')){

      // Build two question in the query
      $union = new Zend_Db_Select($db);
      $union->union(array('(' . $select->__toString() .')'));
      $union->union(array('(' . $pageSelect->__toString().')'));
      $union->group('id');

    } else {

      // The one query
      $union = $select;
      $union->group('photos.photo_id');

    }

    // Order
    if (isset($params['category']) && $params['category'] == 'recent') {

      $union->order('creation_date DESC');

    } elseif (isset($params['category']) && $params['category'] == 'popular') {

      $union->order('view_count DESC');

    } elseif (isset($params['category']) && $params['category'] == 0) {

      $union->order('creation_date DESC');

    }
    if (!empty($params['featured'])) {
      $union->order('RAND()');
    }




    return $union;
  }


  public function getPhotosPaginator(array $params)
  {
    return Zend_Paginator::factory($this->selectPhotos($params));
  }

  public function getPhotosByPaginator(Zend_Paginator $paginator)
  {
    $page_photo_ids = array();
    $photo_ids = array();
    foreach ($paginator as $item){
      if ($item['type'] == 'pagealbumphoto'){
        $page_photo_ids[] = $item['id'];
      } else if ($item['type'] == 'album_photo'){
        $photo_ids[] = $item['id'];
      }
    }

    $_page_photos = array();
    if (Engine_Api::_()->hasItemType('pagealbumphoto')){
      $_page_photos = Engine_Api::_()->getItemMulti('pagealbumphoto', $page_photo_ids);
    }
    $_photos = Engine_Api::_()->getItemMulti('album_photo', $photo_ids);

    $page_photos = array();
    foreach ($_page_photos as $item){
      $page_photos[$item->getGuid()] = $item;
    }
    $photos = array();
    foreach ($_photos as $item){
      $photos[$item->getGuid()] = $item;
    }

    $new_paginator = array();
    foreach ($paginator as $item){
      $model = false;
      if (!empty($photos[$item['type'] . '_' . $item['id']])){
        $model = $photos[$item['type'] . '_' . $item['id']];
      } else if (!empty($page_photos[$item['type'] . '_' . $item['id']])){
        $model = $page_photos[$item['type'] . '_' . $item['id']];
      }
      $new_paginator[] = $model;
    }

    return $new_paginator;

  }

  public function getPhotoSelect(array $params)
  {
    $tbl = new Album_Model_DbTable_Photos();
    $select = $tbl->select();

    if (!empty($params['featured'])) {
      $select->where('he_featured = ?', $params['featured']);
    }

    return $select;
  }

  public function getPhotoPaginator(array $params)
  {
    return Zend_Paginator::factory($this->selectPhotos($params));
  }

  public function getPhoto($photo_id = null)
  {
    if (!$photo_id)
      return false;

    $select = $this->getPhotoSelect(array());
    $select->where('photo_id = ?', $photo_id);

    $photosTable = Engine_Api::_()->getDbTable('photos', 'album');

    $photo = $photosTable->fetchRow($select);
    if (!$photo)
      return false;
    return $photo;
  }

//    public function getTaggedPhotosPaginator()
//    {
//        $viewer = Engine_Api::_()->user()->getViewer();
//        $tagApi = Engine_Api::_()->getDbtable('tags', 'core');
//        $tags = Zend_Paginator::factory($tagApi->getResourcesByTagSelect($viewer, array(
//            'resource_types' => array('album_photo'),
//        )));
//        $tags->setItemCountPerPage($tags->getTotalItemCount());
//        $tags->setCurrentPageNumber(1);
//
//        $data = array();
//        foreach ($tags as $tag) {
//            $photo = $this->getPhoto($tag->resource_id);
//            if (!$photo)
//                continue;
//            $data[] = $photo;
//        }
//
//        $paginator = Zend_Paginator::factory($data);
//
//        return $paginator;
//    }
}
