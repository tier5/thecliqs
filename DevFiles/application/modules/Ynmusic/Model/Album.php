<?php
class Ynmusic_Model_Album extends Core_Model_Item_Abstract
{
	public function getDuration() {
		$songs = $this -> getSongs();
		$duration = 0;
		foreach($songs as $song) {
			$duration += $song -> duration;
		}
		return gmdate("H:i:s", $duration);
	}
	
	public function setPhoto($photo, $fieldName, $save = 1)
  {
    if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
            $name = basename($file);
    }
	else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $name = $photo->name;
    }
    else if (is_array($photo) && !empty($photo['tmp_name'])) {
        $file = $photo['tmp_name'];
        $name = $photo['name'];
    }
    else
    if (is_string($photo) && file_exists($photo)) {
        $file = $photo;
        $name = basename($file);
    }
    else {
        throw new Ynmusic_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
    }
	
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->user_id
    );
    // Save
    $storage = Engine_Api::_()->storage();
    // Resize image (main)
    $image = Engine_Image::factory();
	
	if ($fieldName == 'cover_id') {
		$image->open($file)
	      ->write($path.'/m_'.$name)
	      ->destroy();
	}
	else {
		$image->open($file)
	      ->resize(720, 720)
	      ->write($path.'/m_'.$name)
	      ->destroy();
	}
    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();
    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
          ->resize(100, 100)
          ->write($path.'/in_'.$name)
          ->destroy();
    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x    = ($image->width - $size) / 2;
    $y    = ($image->height - $size) / 2;
    $image->resample($x, $y, $size, $size, 65, 65)
          ->write($path.'/is_'.$name)
          ->destroy();
    // Store
    $iMain       = $storage->create($path.'/m_'.$name,  $params);
    $iProfile    = $storage->create($path.'/p_'.$name,  $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare     = $storage->create($path.'/is_'.$name, $params);
    $iMain->bridge($iProfile,    'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare,     'thumb.icon');
    // Update row
    if ($save) {
	    $this->$fieldName = $iMain->getIdentity();
		if ($fieldName == 'cover_id') {
			$this->cover_top = 0;
		}
	    $this->save();
	}
    return $iMain->getIdentity();
  } 
	
	protected function _delete()
	{
		//delete songs
		foreach ($this->getSongs() as $song) {
			$song -> delete();
		}
		parent::_delete();
	}	

	public function getSlug($str = NULL, $maxstrlen = 64)
	{
		$str = $this -> getTitle();
		if (strlen($str) > 32)
		{
			$str = Engine_String::substr($str, 0, 32) . '...';
		}
		$str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');
		if (!$str)
		{
			$str = '-';
		}
		return $str;
	}
	
	public function getHref($params = array())
	{
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynmusic_album_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

    public function tags() {
	 	return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
    }
	
	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function getParent($recurseType = null)
	{
		return $this -> getOwner();
	}
	
	public function getSongs()
	{
        $songTable   = Engine_Api::_()->getDbTable('songs', 'ynmusic');
        $select  = $songTable -> select();
        $select -> where("album_id = ?", $this -> getIdentity());
		$select -> order('order ASC');
        return $songTable -> fetchAll($select);
	}
	
	public function getAvailableSongs() {
		$avalableSongs = array();
		$songs = $this->getSongs();
		foreach ($songs as $song) {
			if ($song->isViewable())
				$avalableSongs[]= $song;
		}
		return $avalableSongs;
	}
	
	public function getAvailableDownloadSongs() {
		$avalableSongs = array();
		$songs = $this->getAvailableSongs();
		foreach ($songs as $song) {
			if ($song->isDownloadable())
				$avalableSongs[]= $song;
		}
		return $avalableSongs;
	}
	
	public function getFirstSong() {
		$songs = $this->getAvailableSongs();
		if (count($songs))
			return $songs[0];
		return null;
	}
	
	public function getCountAvailableSongs() {
		return count($this->getAvailableSongs());
	}
	
	public function getCountAvailableDownloadSongs() {
		return count($this->getAvailableDownloadSongs());
	}
	
	public function getCountSongs()
	{
		$albumTable = Engine_Api::_() -> getDbTable('albums', 'ynmusic');
		$albumName = $albumTable -> info('name');
        
        $songTable   = Engine_Api::_()->getDbTable('songs', 'ynmusic');
        $songName = $songTable -> info('name');
		
        $select  = $songTable -> select();
        $select->from($songName, array('COUNT(*) AS count'));
        $select -> where("$songName.album_id = ?", $this -> getIdentity());
	   
        return $select->query()->fetchColumn('count');
	}

	function isViewable()
	{
		return $this -> authorization() -> isAllowed(null, 'view');
	}

	function isDownloadable()
	{
		return $this -> authorization() -> isAllowed(null, 'download');
	}

	function isEditable()
	{
		return $this -> authorization() -> isAllowed(null, 'edit');
	}

	function isDeletable()
	{
		return $this -> authorization() -> isAllowed(null, 'delete');
	}
	
	function isCommentable()
	{
		return $this -> authorization() -> isAllowed(null, 'comment');
	}
	
	public function getFirstGenre($params = array()) {
		$params['type'] = 'album';
		$view = Zend_Registry::get('Zend_View');
		$firstGenre = '';
		$genreIds = Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($this);
		if (empty($genreIds)) return $firstGenre;
		foreach ($genreIds as $id) {
			$genre = Engine_Api::_()->getItem('ynmusic_genre', $id);
			if ($genre) {
				$firstGenre = ($genre->isAdmin) ? $view->htmlLink($genre->getHref($params), $genre->getTitle()) : $genre->getTitle();
				break;
			}
		}
		return $firstGenre;
	}
	
	public function getGenres() {
		$view = Zend_Registry::get('Zend_View');
		$genre_arr = array();
		$genreIds = Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($this);
		if (empty($genreIds)) return $genre_arr;
		$genresDbTbl = Engine_Api::_()->getDbTable('genres', 'ynmusic');
		$select = $genresDbTbl->getSelect(array('genre_ids' => $genreIds));
		$genres = $genresDbTbl->fetchAll($select);
		foreach ($genres as $genre) {
			$genre_arr[] = ($genre->isAdmin) ? $view->htmlLink($genre->getHref(), $genre->getTitle()) : $genre->getTitle();
		}
		return $genre_arr;
	}
	
	public function getArtists() {
		$view = Zend_Registry::get('Zend_View');
		$artist_arr = array();
		$artistIds = Engine_Api::_()->getDbTable('artistmappings', 'ynmusic')->getArtistIdsByItem($this);
		if (empty($artistIds)) return $artist_arr;
		$artistsDbTbl = Engine_Api::_()->getDbTable('artists', 'ynmusic');
		$select = $artistsDbTbl->getArtistsSelect(array('artist_ids' => $artistIds));
		$artists = $artistsDbTbl->fetchAll($select);
		foreach ($artists as $artist) {
			$artist_arr[] = ($artist->isAdmin) ? $view->htmlLink($artist->getHref(), $artist->getTitle()) : $artist->getTitle();
		}
		return $artist_arr;
	}
	
	function getCreationDate() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->creation_date));
        $date->setTimezone($timezone);
        return $date;
    }
	
	function getReleasedDate() {
		if(empty($this->released_date)) return null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->released_date));
        $date->setTimezone($timezone);
        return $date;
    }
	
	public function getRichContent($view = false, $params = array()) {
		$zend_View = Zend_Registry::get('Zend_View');
	    // $view == false means that this rich content is requested from the activity feed
	    if($view == false){
			return $zend_View -> partial('_album_feed.tpl', 'ynmusic', array('item' => $this));
	    }
  	}
	
	public function canAddSongs() {
		$user = Engine_Api::_()->user()->getUser($this->user_id);
		if (!$user) return false;
		
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynmusic_album', $user->level_id, 'max_songs');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $user->level_id)
                ->where('type = ?', 'ynmusic_album')
                ->where('name = ?', 'max_songs'));
            if ($row) {
                $max = $row->value;
            }
        }
		
		if (!$max) return true;
		$numOfExists = $this->getCountSongs();
		$remain = $max - $numOfExists;
		return ($remain > 0);
	}
}
