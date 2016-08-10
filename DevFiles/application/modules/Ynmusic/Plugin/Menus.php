<?php
class Ynmusic_Plugin_Menus {
	protected $_viewer;
	protected $_level_id = 5;
	protected $_canViewAlbum = false;
	protected $_canViewPlaylist = false;
	protected $_canViewSong = false;
	protected $_canCreateAlbum = false;
	protected $_canUploadSong = false;
	
	public function init() {
		$this->_viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity()) $this->_level_id = $viewer->level_id;
		$level_id = $this->_level_id;
		$this->_canViewAlbum = Engine_Api::_()->authorization()->getPermission($level_id, 'ynmusic_album', 'view');
		$this->_canViewPlaylist = Engine_Api::_()->authorization()->getPermission($level_id, 'ynmusic_playlist', 'view');
		$this->_canViewSong = Engine_Api::_()->authorization()->getPermission($level_id, 'ynmusic_song', 'view');
		$this->_canUploadSong = Engine_Api::_()->ynmusic()->canUploadSong();
	}
	
	public function onMenuInitialize_YnmusicMainAlbums() {
		$this->init();
		return ($this->_canViewAlbum) ? true : false;
	}
	
	public function onMenuInitialize_YnmusicMainSongs() {
		$this->init();
		return ($this->_canViewSong) ? true : false;
	}
	
	public function onMenuInitialize_YnmusicMainPlaylists() {
		$this->init();
		return ($this->_canViewPlaylist) ? true : false;
	}
	
	public function onMenuInitialize_YnmusicMainManageAlbums() {
		$this->init();
		return ($this->_viewer->getIdentity()) ? true : false;
	}
	
	public function onMenuInitialize_YnmusicMainUpload() {
		$this->init();
		return ($this->_canUploadSong) ? true : false;
	}
	
	public function canMigrate() {
		return (Engine_Api::_()->hasModuleBootstrap('mp3music')) ? true : false;
	}
	public function canMigrateSEMusic() {
		return (Engine_Api::_()->hasModuleBootstrap('music')) ? true : false;
	}
}