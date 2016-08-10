<?php
class Mp3music_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $db->query("DELETE FROM `engine4_core_pages` WHERE `name` = 'mp3music_index_browse' LIMIT 1");
    $db->query("DELETE FROM `engine4_core_pages` WHERE `name` = 'mp3music_album_album' LIMIT 1");
    $db->query("DELETE FROM `engine4_core_pages` WHERE `name` = 'mp3music_playlist_playlist' LIMIT 1");
    
     //music home
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mp3music_index_browse')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_index_browse',
        'displayname' => 'Mp3 Music Home',
        'title' => 'Mp3 Music Home',
        'description' => 'This is music home.',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => 6,
        'params' => '',
      ));
       $middle_id = $db->lastInsertId('engine4_core_content');  
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 4,
        'params' => '',
      ));
      $left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.featured-albums',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '{"title":"Featured Albums"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.search-music',
        'parent_content_id' => $middle_id,
        'order' => 2,
        'params' => '{"title":"Search"}',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
      ));
      $tab0_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-albums',
        'parent_content_id' => $tab0_id,
        'order' => 1,
        'params' => '{"title":"Top Albums"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.new-albums',
        'parent_content_id' => $tab0_id,
        'order' => 2,
        'params' => '{"title":"New Albums"}',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.browse-music',
        'parent_content_id' => $middle_id,
        'order' => 4,
        'params' => '',
      ));
      // left column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.categories-music',
        'parent_content_id' => $left_id,
        'order' => 1,
        'params' => '{"title":"Categories"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.singers-music',
        'parent_content_id' => $left_id,
        'order' => 2,
        'params' => '',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.artists-music',
        'parent_content_id' => $left_id,
        'order' => 3,
        'params' => '{"title":"Artists"}',
      ));
      // right column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.new-playlists',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"New Playlists"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-songs',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '{"title":"Top Songs"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-downloads',
        'parent_content_id' => $right_id,
        'order' => 3,
        'params' => '{"title":"Top Downloads"}',
      ));
    }
    //Music player Album
     $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mp3music_album_album')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_album_album',
        'displayname' => 'Mp3 Music - Player Album',
        'title' => 'Mp3 Music - Player Album',
        'description' => 'This is player for album.',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => 6,
        'params' => '',
      ));
       $middle_id = $db->lastInsertId('engine4_core_content');  
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.player-album',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
     
      // right column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.other-albums',
        'parent_content_id' => $right_id,
        'order' => 6,
        'params' => '{"title":"Other Albums"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.related-playlists',
        'parent_content_id' => $right_id,
        'order' => 7,
        'params' => '{"title":"Related Playlists"}',
      ));
    }
    
    //Music popup player Playlist
     $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mp3music_playlist_playlist')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if(empty($info)) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_playlist_playlist',
        'displayname' => 'Mp3 Music - Player Playlist',
        'title' => 'Mp3 Music - Player Playlist',
        'description' => 'This is player for playlist.',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => 6,
        'params' => '',
      ));
       $middle_id = $db->lastInsertId('engine4_core_content');  
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.player-playlist',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
     
      // right column
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.other-playlists',
        'parent_content_id' => $right_id,
        'order' => 6,
        'params' => '{"title":"Other Playlists"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.related-albums',
        'parent_content_id' => $right_id,
        'order' => 7,
        'params' => '{"title":"Related Albums"}',
      ));
    }
    //Search albums
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mp3music_index_browsealbums')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_index_browsealbums',
        'displayname' => 'Search Albums',
        'title' => 'Search Albums',
        'description' => 'This is browse albums.',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => 6,
        'params' => '',
      ));
       $middle_id = $db->lastInsertId('engine4_core_content');  
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 4,
        'params' => '',
      ));
      $left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.search-music',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '{"title":"Search"}',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.browse-albums',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      // left column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.categories-music',
        'parent_content_id' => $left_id,
        'order' => 1,
        'params' => '{"title":"Categories"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.singers-music',
        'parent_content_id' => $left_id,
        'order' => 2,
        'params' => '',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.artists-music',
        'parent_content_id' => $left_id,
        'order' => 3,
        'params' => '{"title":"Artists"}',
      ));
      // right column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.new-playlists',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"New Playlists"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-songs',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '{"title":"Top Songs"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-downloads',
        'parent_content_id' => $right_id,
        'order' => 3,
        'params' => '{"title":"Top Downloads"}',
      ));
    }
    //Search playlists
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mp3music_index_browseplaylists')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_index_browseplaylists',
        'displayname' => 'Search Playlists',
        'title' => 'Search Playlists',
        'description' => 'This is browse playlists.',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => 6,
        'params' => '',
      ));
       $middle_id = $db->lastInsertId('engine4_core_content');  
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 4,
        'params' => '',
      ));
      $left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 5,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.search-music',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '{"title":"Search"}',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.browse-playlists',
        'parent_content_id' => $middle_id,
        'order' => 2,
        'params' => '',
      ));
      // left column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.categories-music',
        'parent_content_id' => $left_id,
        'order' => 1,
        'params' => '{"title":"Categories"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.singers-music',
        'parent_content_id' => $left_id,
        'order' => 2,
        'params' => '',
      ));
       $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.artists-music',
        'parent_content_id' => $left_id,
        'order' => 3,
        'params' => '{"title":"Artists"}',
      ));
      // right column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.top-albums-right',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"Top Albums"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mp3music.new-albums-right',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '{"title":"Top Songs"}',
      ));
    }
    
    
    $this->_addMusicCreatePage();
    $this->_addAlbumManagePage();
    $this->_addPlaylistManagePage();
    parent::onInstall();
  }
  
  
  //Add Music Create Page
  protected function _addMusicCreatePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'mp3music_album_create')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_album_create',
        'displayname' => 'Mp3 Music - Create',
        'title' => 'Upload Music',
        'description' => 'This page is the music create page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
  
  //Add Album Manage Page
  protected function _addAlbumManagePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'mp3music_album_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_album_manage',
        'displayname' => 'Mp3 Music - Manage Albums',
        'title' => 'My Albums',
        'description' => 'This page is the album manage page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
  
  
  //Add Playlist Manage Page
  protected function _addPlaylistManagePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'mp3music_playlist_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'mp3music_playlist_manage',
        'displayname' => 'Mp3 Music - Manage Playlists',
        'title' => 'My Playlists',
        'description' => 'This page is the playlist manage page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'mp3music.menu-music',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
  
  
  
}
?>
