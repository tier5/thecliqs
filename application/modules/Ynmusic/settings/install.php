<?php
class Ynmusic_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
    	$this -> _addBrowseMusicPage();
		$this -> _addSearchMusicPage();
		$this -> _addBrowsePlaylistPage();
		$this -> _addBrowseArtistPage();
    	$this -> _addManagePlaylistPage();
    	$this -> _addManageSongPage();
    	$this -> _addManageAlbumPage();
		$this -> _addHistoryPage();
		$this -> _addPlaylistDetailPage();
		$this -> _addPlaylistEditPage();
		$this -> _addSongDetailPage();
		$this -> _addAlbumDetailPage();
		$this -> _addArtistDetailPage();
		$this -> _addUploadPage();
		$this -> _addFaqsPage();
		$this -> _addSongListingPage();
		$this -> _addSongEditPage();
		$this -> _addAlbumListingPage();
		$this -> _addAlbumEditPage();
		$this -> _addUserProfileAlbums();
		$this -> _addUserProfileSongs();
		$this -> _addUserProfilePlaylists();
		parent::onInstall();
    }
    
	protected function _addArtistDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_artists_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_artists_view',
                'displayname' => 'YN - Social Music - Artist Detail Page',
                'title' => 'Music Artist Detail Page',
                'description' => 'Music Artist Detail Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			//Insert playlist cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artist-profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
			// Insert tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $main_container_id = $db -> lastInsertId();
			
			//Insert profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artist-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 1,
                'params' => '{"title":"Information"}',
            ));
			
            //Insert profile song widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artist-profile-song',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 2,
                'params' => '{"title":"Songs"}',
            ));
			
			//Insert profile song widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artist-profile-album',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 3,
                'params' => '{"title":"Albums"}',
            ));
			
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artist-profile-related',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Related Artists"}',
            ));    
			
        }
    }  
	
	protected function _addAlbumListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_albums_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_albums_index',
                'displayname' => 'YN - Social Music - Albums Page',
                'title' => 'Music Albums Page',
                'description' => 'Music Albums Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert album listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.albums-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                     
        }
    }

	protected function _addAlbumEditPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_albums_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_albums_edit',
                'displayname' => 'YN - Social Music - Edit Album Page',
                'title' => 'Music Edit Album Page',
                'description' => 'Music Edit Album Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert album listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }
	
	protected function _addSongListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_songs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_songs_index',
                'displayname' => 'YN - Social Music - Songs Page',
                'title' => 'Music Songs Page',
                'description' => 'Music Songs Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert songs listing
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.songs-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                     
        }
    }

	protected function _addSongEditPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_songs_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_songs_edit',
                'displayname' => 'YN - Social Music - Edit Song Page',
                'title' => 'Music Edit Song Page',
                'description' => 'Music Edit Song Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert songs listing
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }
	
	protected function _addAlbumDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_albums_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_albums_view',
                'displayname' => 'YN - Social Music - Album Detail Page',
                'title' => 'Music Album Detail Page',
                'description' => 'Music Album Detail Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			//Insert album cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.album-profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert album information widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.album-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert comment widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.comments',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
			
			//Insert user profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.user-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert more albums from this user widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.album-profile-more',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"More From This User"}',
            ));                     
        }
    }  
	
	protected function _addSongDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_songs_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_songs_view',
                'displayname' => 'YN - Social Music - Song Detail Page',
                'title' => 'Music Song Detail Page',
                'description' => 'Music Song Detail Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			//Insert playlist cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.song-profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert playlist information widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.song-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert comment widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.comments',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
			
			//Insert user profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.user-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert more playlists from this user widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.song-profile-in-playlist',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"In Playlists"}',
            ));                     
        }
    }  
	
	protected function _addBrowseMusicPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_index_index',
                'displayname' => 'YN - Social Music - Browse Page',
                'title' => 'Music Browse Page',
                'description' => 'Music Browse Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert featured album
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.featured-albums',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
                'params' => '{"title":"Featured Albums"}',
            ));
			
			// Insert album tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $album_container_id = $db -> lastInsertId();
            
			//Insert most played albums
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-played-albums',
                'page_id' => $page_id,
                'parent_content_id' => $album_container_id,
                'order' => 1,
                'params' => '{"title":"Most Played Albums"}',
            ));
			
			//Insert most liked albums
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-liked-albums',
                'page_id' => $page_id,
                'parent_content_id' => $album_container_id,
                'order' => 2,
                'params' => '{"title":"Most Liked Albums"}',
            ));
			
			//Insert most discussed albums
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-discussed-albums',
                'page_id' => $page_id,
                'parent_content_id' => $album_container_id,
                'order' => 3,
                'params' => '{"title":"Most Discussed Albums"}',
            ));
			
			// Insert song tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $song_container_id = $db -> lastInsertId();
            
			//Insert most played songs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-played-songs',
                'page_id' => $page_id,
                'parent_content_id' => $song_container_id,
                'order' => 1,
                'params' => '{"title":"Most Played Songs"}',
            ));
			
			//Insert most liked songs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-liked-songs',
                'page_id' => $page_id,
                'parent_content_id' => $song_container_id,
                'order' => 2,
                'params' => '{"title":"Most Liked Songs"}',
            ));
			
			//Insert most discussed songs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-discussed-songs',
                'page_id' => $page_id,
                'parent_content_id' => $song_container_id,
                'order' => 3,
                'params' => '{"title":"Most Discussed Songs"}',
            ));
			
			// Insert playlist tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $playlist_container_id = $db -> lastInsertId();
            
			//Insert most played playlists
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-played-playlists',
                'page_id' => $page_id,
                'parent_content_id' => $playlist_container_id,
                'order' => 1,
                'params' => '{"title":"Most Played Playlists"}',
            ));
			
			//Insert most liked playlists
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-liked-playlists',
                'page_id' => $page_id,
                'parent_content_id' => $playlist_container_id,
                'order' => 2,
                'params' => '{"title":"Most Liked Playlists"}',
            ));
			
			//Insert most discussed playlists
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.most-discussed-playlists',
                'page_id' => $page_id,
                'parent_content_id' => $playlist_container_id,
                'order' => 3,
                'params' => '{"title":"Most Discussed Playlists"}',
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			//Insert recent played widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.recent-played',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"Recently Played"}',
            ));
			
			//Insert songs you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.songs-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Songs You May Like"}',
            ));
			
			//Insert albums you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.albums-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Albums You May Like"}',
            )); 
			
			//Insert albums you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.list-genres',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Genres"}',
            ));                       
        }
    }

	protected function _addSearchMusicPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_index_listing')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_index_listing',
                'displayname' => 'YN - Social Music - Search Page',
                'title' => 'Music Search Page',
                'description' => 'Music Search Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert music listings widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.music-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			//Insert recent played widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.recent-played',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"Recently Played"}',
            ));
			
			//Insert songs you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.songs-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Songs You May Like"}',
            ));
			
			//Insert albums you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.albums-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Albums You May Like"}',
            )); 
			
			//Insert genres widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.list-genres',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Genres"}',
            ));                       
        }
    }

	protected function _addBrowsePlaylistPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_playlists_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_playlists_index',
                'displayname' => 'YN - Social Music - Playlists Page',
                'title' => 'Music Playlists Page',
                'description' => 'Music Playlists Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert playlists listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.playlists-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }

	protected function _addBrowseArtistPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_artists_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_artists_index',
                'displayname' => 'YN - Social Music - Artists Page',
                'title' => 'Music Artists Page',
                'description' => 'Music Artists Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert artists listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.artists-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }

	protected function _addManagePlaylistPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_playlists_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_playlists_manage',
                'displayname' => 'YN - Social Music - Manage Playlists Page',
                'title' => 'Music Manage Playlists Page',
                'description' => 'Music Manage Playlists Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));                     
        }
    }  
	
	protected function _addManageAlbumPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_albums_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_albums_manage',
                'displayname' => 'YN - Social Music - Manage Albums Page',
                'title' => 'Music Manage Albums Page',
                'description' => 'Music Manage Albums Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));                     
        }
    }  
	
	protected function _addManageSongPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_songs_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_songs_manage',
                'displayname' => 'YN - Social Music - Manage Songs Page',
                'title' => 'Music Manage Songs Page',
                'description' => 'Music Manage Songs Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));                     
        }
    }

	protected function _addHistoryPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_history_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_history_index',
                'displayname' => 'YN - Social Music - History Page',
                'title' => 'Music History Page',
                'description' => 'Music History Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert music history widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.music-history',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.search-music',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));                     
        }
    }

	protected function _addPlaylistDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_playlists_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_playlists_view',
                'displayname' => 'YN - Social Music - Playlist Detail Page',
                'title' => 'Music Playlist Detail Page',
                'description' => 'Music Playlist Detail Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
			//Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			$main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			//Insert playlist cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.playlist-profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert playlist information widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.playlist-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert comment widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.comments',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
			
			//Insert user profile info widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.user-profile-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));    
			
			//Insert more playlists from this user widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.playlist-profile-more',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"More From This User"}',
            ));                     
        }
    }

	protected function _addPlaylistEditPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_playlists_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_playlists_edit',
                'displayname' => 'YN - Social Music - Edit Playlist Page',
                'title' => 'Music Edit Playlist Page',
                'description' => 'Music Edit Playlist Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
        }
    }  
	
	protected function _addUploadPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_songs_upload')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_songs_upload',
                'displayname' => 'YN - Social Music - Upload Page',
                'title' => 'Music Upload Page',
                'description' => 'Music Upload Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
        }
    }

	protected function _addFaqsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynmusic_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynmusic_faqs_index',
                'displayname' => 'YN - Social Music - FAQs Page',
                'title' => 'Music FAQs Page',
                'description' => 'This page show the FAQs',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynmusic.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }

	protected function _addUserProfileAlbums() {
    	//
    	// install content areas
    	//
    	$db = $this->getDb();
    	$select = new Zend_Db_Select($db);

    	// profile page
    	$select
        	->from('engine4_core_pages')
        	->where('name = ?', 'user_profile_index')
        	->limit(1);
    	$page_id = $select->query()->fetchObject()->page_id;

    
    	// Check if it's already been placed
    	$select = new Zend_Db_Select($db);
    	$select
        	->from('engine4_core_content')
        	->where('page_id = ?', $page_id)
        	->where('type = ?', 'widget')
        	->where('name = ?', 'ynmusic.user-profile-albums');
   	 	$info = $select->query()->fetch();
    	if( empty($info) ) {
    
        	// container_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('page_id = ?', $page_id)
            	->where('type = ?', 'container')
            	->limit(1);
        	$container_id = $select->query()->fetchObject()->content_id;

        	// middle_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('parent_content_id = ?', $container_id)
            	->where('type = ?', 'container')
            	->where('name = ?', 'middle')
            	->limit(1);
        	$middle_id = $select->query()->fetchObject()->content_id;

        	// tab_id (tab container) may not always be there
        	$select
            	->reset('where')
            	->where('type = ?', 'widget')
            	->where('name = ?', 'core.container-tabs')
            	->where('page_id = ?', $page_id)
            	->limit(1);
        	$tab_id = $select->query()->fetchObject();
        	if( $tab_id && @$tab_id->content_id ) {
            	$tab_id = $tab_id->content_id;
        	} else {
            	$tab_id = null;
        	}

        	// tab on profile
        	$db->insert('engine4_core_content', array(
            	'page_id' => $page_id,
            	'type'    => 'widget',
            	'name'    => 'ynmusic.user-profile-albums',
            	'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        		'order'   => 999,
        		'params'  => '{"title":"Albums","titleCount":true}',
      		));

    	}
  	}

	protected function _addUserProfileSongs() {
    	//
    	// install content areas
    	//
    	$db = $this->getDb();
    	$select = new Zend_Db_Select($db);

    	// profile page
    	$select
        	->from('engine4_core_pages')
        	->where('name = ?', 'user_profile_index')
        	->limit(1);
    	$page_id = $select->query()->fetchObject()->page_id;

    
    	// Check if it's already been placed
    	$select = new Zend_Db_Select($db);
    	$select
        	->from('engine4_core_content')
        	->where('page_id = ?', $page_id)
        	->where('type = ?', 'widget')
        	->where('name = ?', 'ynmusic.user-profile-songs');
   	 	$info = $select->query()->fetch();
    	if( empty($info) ) {
    
        	// container_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('page_id = ?', $page_id)
            	->where('type = ?', 'container')
            	->limit(1);
        	$container_id = $select->query()->fetchObject()->content_id;

        	// middle_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('parent_content_id = ?', $container_id)
            	->where('type = ?', 'container')
            	->where('name = ?', 'middle')
            	->limit(1);
        	$middle_id = $select->query()->fetchObject()->content_id;

        	// tab_id (tab container) may not always be there
        	$select
            	->reset('where')
            	->where('type = ?', 'widget')
            	->where('name = ?', 'core.container-tabs')
            	->where('page_id = ?', $page_id)
            	->limit(1);
        	$tab_id = $select->query()->fetchObject();
        	if( $tab_id && @$tab_id->content_id ) {
            	$tab_id = $tab_id->content_id;
        	} else {
            	$tab_id = null;
        	}

        	// tab on profile
        	$db->insert('engine4_core_content', array(
            	'page_id' => $page_id,
            	'type'    => 'widget',
            	'name'    => 'ynmusic.user-profile-songs',
            	'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        		'order'   => 999,
        		'params'  => '{"title":"Songs","titleCount":true}',
      		));

    	}
  	}

	protected function _addUserProfilePlaylists() {
    	//
    	// install content areas
    	//
    	$db = $this->getDb();
    	$select = new Zend_Db_Select($db);

    	// profile page
    	$select
        	->from('engine4_core_pages')
        	->where('name = ?', 'user_profile_index')
        	->limit(1);
    	$page_id = $select->query()->fetchObject()->page_id;

    
    	// Check if it's already been placed
    	$select = new Zend_Db_Select($db);
    	$select
        	->from('engine4_core_content')
        	->where('page_id = ?', $page_id)
        	->where('type = ?', 'widget')
        	->where('name = ?', 'ynmusic.user-profile-playlists');
   	 	$info = $select->query()->fetch();
    	if( empty($info) ) {
    
        	// container_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('page_id = ?', $page_id)
            	->where('type = ?', 'container')
            	->limit(1);
        	$container_id = $select->query()->fetchObject()->content_id;

        	// middle_id (will always be there)
        	$select = new Zend_Db_Select($db);
        	$select
            	->from('engine4_core_content')
            	->where('parent_content_id = ?', $container_id)
            	->where('type = ?', 'container')
            	->where('name = ?', 'middle')
            	->limit(1);
        	$middle_id = $select->query()->fetchObject()->content_id;

        	// tab_id (tab container) may not always be there
        	$select
            	->reset('where')
            	->where('type = ?', 'widget')
            	->where('name = ?', 'core.container-tabs')
            	->where('page_id = ?', $page_id)
            	->limit(1);
        	$tab_id = $select->query()->fetchObject();
        	if( $tab_id && @$tab_id->content_id ) {
            	$tab_id = $tab_id->content_id;
        	} else {
            	$tab_id = null;
        	}

        	// tab on profile
        	$db->insert('engine4_core_content', array(
            	'page_id' => $page_id,
            	'type'    => 'widget',
            	'name'    => 'ynmusic.user-profile-playlists',
            	'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        		'order'   => 999,
        		'params'  => '{"title":"Playlists","titleCount":true}',
      		));

    	}
  	}  
}