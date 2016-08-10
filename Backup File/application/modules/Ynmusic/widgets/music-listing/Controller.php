<?php
class Ynmusic_Widget_MusicListingController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
		unset($params['format']);
        $mode_list = $mode_grid = 1;
        $mode_enabled = array();
        $view_mode = 'list';

        if(isset($params['mode_list'])) {
            $mode_list = $params['mode_list'];
        }
        if($mode_list) {
            $mode_enabled[] = 'list';
        }
        if(isset($params['mode_grid'])) {
            $mode_grid = $params['mode_grid'];
        }
        if($mode_grid) {
            $mode_enabled[] = 'grid';
        }
        
        if(isset($params['view_mode'])) {
            $view_mode = $params['view_mode'];
        }

        if($mode_enabled && !in_array($view_mode, $mode_enabled)) {
            $view_mode = $mode_enabled[0];
        }

        $this -> view -> mode_enabled = $mode_enabled;

        $class_mode = "ynmusic_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynmusic_grid-view";
                break;
            default:
                $class_mode = "ynmusic_list-view";
                break;
        }
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;

        $page = (!empty($params['page'])) ? $params['page'] : 1;
		$searchType = (!empty($params['type'])) ? $params['type'] : 'all';
		
		$artists_select = ($searchType == 'all' || $searchType == 'artist') ? Engine_Api::_()->getDbTable('artists', 'ynmusic')->getArtistsSelect(array('admin'=>true) + $params): '';
		$albums_select = ($searchType == 'all' || $searchType == 'album') ? Engine_Api::_()->getDbTable('albums', 'ynmusic')->getAlbumsSelect($params): '';
		$playlists_select = ($searchType == 'all' || $searchType == 'playlist') ? Engine_Api::_()->getDbTable('playlists', 'ynmusic')->getSelect($params): '';
		$songs_select = ($searchType == 'all' || $searchType == 'song') ? Engine_Api::_()->getDbTable('songs', 'ynmusic')->getSongsSelect($params): '';
		
		if (!empty($artists_select)) 
			$artistPaginator = Zend_Paginator::factory($artists_select);
		else 
			$artistPaginator = Zend_Paginator::factory(array());
		$artistPaginator->setCurrentPageNumber(1);
		$artistPaginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_artistsPerPage', 8));	
		$this->view->artistPaginator = $artistPaginator;
		
		if (!empty($albums_select)) 
			$albumPaginator = Zend_Paginator::factory($albums_select);
		else 
			$albumPaginator = Zend_Paginator::factory(array());
		$albumPaginator->setCurrentPageNumber(1);
		$albumPaginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_albumsPerPage', 8));	
		$this->view->albumPaginator = $albumPaginator;
		
		if (!empty($playlists_select)) 
			$playlistPaginator = Zend_Paginator::factory($playlists_select);
		else 
			$playlistPaginator = Zend_Paginator::factory(array());
		$playlistPaginator->setCurrentPageNumber(1);
		$playlistPaginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_playlistsPerPage', 8));	
		$this->view->playlistPaginator = $playlistPaginator;
		
		if (!empty($songs_select)) 
			$songPaginator = Zend_Paginator::factory($songs_select);
		else 
			$songPaginator = Zend_Paginator::factory(array());
		$songPaginator->setCurrentPageNumber(1);
		$songPaginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_songsPerPage', 10));	
		$this->view->songPaginator = $songPaginator;
		
		$this->view->formValues = $params;	
	}
}