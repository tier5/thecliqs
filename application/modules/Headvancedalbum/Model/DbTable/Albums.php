<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Albums.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Headvancedalbum_Model_DbTable_Albums extends Album_Model_DbTable_Albums
{
    protected $_rowClass = 'Headvancedalbum_Model_Album';

  public function selectAlbums($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();

    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');
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
    $select = $albumsTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('albums' => $albumsTbl->info('name')), new Zend_Db_Expr('albums.album_id AS id, "album" AS type, albums.view_count AS view_count, albums.creation_date AS creation_date'))
      ->joinLeft(array('auth' => $authallowTbl->info('name')), "auth.resource_type = 'album' AND auth.resource_id = albums.album_id AND auth.action = 'view'", array())
      ->where("auth.role = 'everyone' OR (auth.role = 'registered' AND $user_id > 0) OR (auth.role = 'owner_member' AND albums.owner_type = 'user' AND albums.owner_id IN (?)) OR (albums.owner_type = 'user' AND albums.owner_id = $user_id)", $friend_ids);


    // Page albums select

    $pageSelect = new Zend_Db_Select($db);

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum')){

      $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
      $pageAlbumTable = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
      $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

      $pageSelect = $pageAlbumTable->select()
        ->setIntegrityCheck(false)
        ->from(array('albums' => $pageAlbumTable->info('name')), new Zend_Db_Expr('albums.pagealbum_id AS id, "pagealbum" AS type, albums.view_count AS view_count, albums.creation_date AS creation_date'))
        ->join(array('pages' => $pageTable->info('name')), 'pages.page_id = albums.page_id', array())
        ->joinLeft(array('auth' => $authallowTbl->info('name')), "auth.resource_type = 'page' AND auth.resource_id = albums.page_id AND auth.action = 'view'", array())
        ->joinLeft(array('li' => $listitemTbl->info('name')), 'auth.role_id = li.list_id', array())
        ->where("auth.role = 'everyone' OR (auth.role = 'registered' AND $user_id > 0) OR (li.child_id = $user_id)");
    }


    // Filter by keyword
    if (isset($params['search_albums']) && $params['search_albums'] !== '') {

      $select->where("(albums.title LIKE '%{$params['search_albums']}%' OR albums.description LIKE '%{$params['search_albums']}%')");
      $pageSelect->where("(albums.title LIKE '%{$params['search_albums']}%' OR albums.description LIKE '%{$params['search_albums']}%')");

    }

    // Filter by featured
    if (!empty($params['featured'])) {

      $select->where('albums.he_featured = ?', $params['featured']);
      $pageSelect->where('albums.pagealbum_id = 0'); // nothing from page albums

    }
    // Filter by owner
    if (isset($params['owner'])) {
      $select->where('albums.owner_type = "user" AND albums.owner_id = ?', $params['owner']->getIdentity());
      $pageSelect->where('albums.owner_id = ?', $params['owner']->getIdentity());
    }

    // Filter by category
    if (isset($params['category']) && $params['category'] > 0) {

      $select->where('albums.category_id = ?', $params['category']);
      $pageSelect->where('albums.album_id = 0'); // nothing from page albums

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
      $union->group('albums.album_id');

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


    public function getAlbumPaginator($options = array())
    {
        return Zend_Paginator::factory($this->selectAlbums($options));
    }

  public function getAlbumsByPaginator(Zend_Paginator $paginator)
  {
    $page_photo_ids = array();
    $photo_ids = array();
    foreach ($paginator as $item){
      if ($item['type'] == 'pagealbum'){
        $page_photo_ids[] = $item['id'];
      } else if ($item['type'] == 'album'){
        $photo_ids[] = $item['id'];
      }
    }

    $_page_albums = array();
    if (Engine_Api::_()->hasItemType('pagealbum')){
      $_page_albums = Engine_Api::_()->getItemMulti('pagealbum', $page_photo_ids);
    }
    $_albums = Engine_Api::_()->getItemMulti('album', $photo_ids);

    $page_albums = array();
    foreach ($_page_albums as $item){
      $page_albums[$item->getGuid()] = $item;
    }
    $albums = array();
    foreach ($_albums as $item){
      $albums[$item->getGuid()] = $item;
    }

    $new_paginator = array();
    foreach ($paginator as $item){
      $model = false;
      if (!empty($albums[$item['type'] . '_' . $item['id']])){
        $model = $albums[$item['type'] . '_' . $item['id']];
      } else if (!empty($page_albums[$item['type'] . '_' . $item['id']])){
        $model = $page_albums[$item['type'] . '_' . $item['id']];
      }
      $new_paginator[] = $model;
    }

    return $new_paginator;

  }


}