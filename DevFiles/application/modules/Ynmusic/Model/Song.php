<?php
class Ynmusic_Model_Song extends Core_Model_Item_Abstract {
	protected $_parent_type = 'user';
	
	public function getPlaylistIds($limit = null){
		$playlistSong = Engine_Api::_() -> getDbTable('playlistSongs', 'ynmusic');
		$select = $playlistSong -> select() 
						-> from($playlistSong -> info('name'), 'playlist_id')
						-> where('song_id = ?', $this -> song_id);
		if(isset($limit)){
			$select -> limit($limit);
		}
		return $select -> query() -> fetchAll(PDO::FETCH_ASSOC, 0);
	}
	
	public function setPhoto($photo, $fieldName, $save = 1) {
		
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
		$params = array('parent_type' => $this -> getType(), 'parent_id' => $this -> getIdentity(), 'user_id' => $this->user_id);
		// Save
		$storage = Engine_Api::_() -> storage();
		$image = Engine_Image::factory();
		if ($fieldName == 'cover_id') {
			$image -> open($file) -> write($path.'/m_'.$name) -> destroy();
		}
		else {
			$image -> open($file) -> resize(720, 720) -> write($path.'/m_'.$name) -> destroy();
		}
		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();
		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(100, 100) -> write($path . '/in_' . $name) -> destroy();
		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;
		$image -> resample($x, $y, $size, $size, 65, 65) -> write($path . '/is_' . $name) -> destroy();
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);
		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');
		// Update row
		if ($save) {
			$this -> $fieldName = $iMain -> getIdentity();
			if ($fieldName == 'cover_id') {
				$this->cover_top = 0;
			}
			$this -> save();
		}
		return $iMain -> getIdentity();
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
			'route' => 'ynmusic_song_profile',
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
	
	public function getAlbum() {
		if ($this -> album_id != 0) {
			return Engine_Api::_() -> getItem('ynmusic_album', $this -> album_id);
		}
		return null;
	}

	public function getShortType($inflect = false) {
		return 'song';
	}
	
	
	public function getFilePath() {
		if(isset($this -> permalink) && !empty($this -> permalink)) {
			require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/Soundcloud.php';	
			
			$setting = Engine_Api::_()->getApi('settings', 'core');
			$cliendId = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientid', "");
			$cliendSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientsecret', "");
			
			try {
				$client = new Services_Soundcloud($cliendId, $cliendSecret);
				$track = json_decode($client->get("tracks/".$this -> permalink));
			} catch (Exception $e) {
		      	return false;
		    }
			
			if($track) {
				return $track->stream_url."?consumer_key=".$cliendId;
			} 
			return false;
		} else {
			$file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
			if ($file) {
				if ($file -> service_id == 1) {
					return rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') .$file -> map();
				}
				else {
					return $file -> map();
				}
			} 
		}
	}

	public function getMediaType() {
		return 'song';
	}

	public function getRichContent($view = false, $params = array()) {
		$zend_View = Zend_Registry::get('Zend_View');
	    // $view == false means that this rich content is requested from the activity feed
	    if($view == false){
			return $zend_View -> partial('_song_feed.tpl', 'ynmusic', array('item' => $this));
	    }
  	}

	/**
	 * Returns languagified play count
	 */
	public function playCountLanguagified() {
		return vsprintf(Zend_Registry::get('Zend_Translate') -> _(array('%s play', '%s plays', $this -> play_count)), Zend_Locale_Format::toNumber($this -> play_count));
	}

	protected function _delete()
	{
		$file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
		if ($file) {
			$file -> delete();
		}
		parent::_delete();
	}

	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function getArtists() {
		$view = Zend_Registry::get('Zend_View');
		$artist_arr = array();
		$artistIds = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic') -> getArtistIdsByItem($this);
		if (empty($artistIds))
			return $artist_arr;
		$artistsDbTbl = Engine_Api::_() -> getDbTable('artists', 'ynmusic');
		$select = $artistsDbTbl -> getArtistsSelect(array('artist_ids' => $artistIds));
		$artists = $artistsDbTbl -> fetchAll($select);
		foreach ($artists as $artist) {
			$artist_arr[] = ($artist -> isAdmin) ? $view -> htmlLink($artist -> getHref(), $artist -> getTitle()) : $artist -> getTitle();
		}
		return $artist_arr;
	}

	function isViewable() {
		$album = $this->getAlbum();
		if ($album) return ($album->isViewable() && $this -> authorization() -> isAllowed(null, 'view'));
        return $this -> authorization() -> isAllowed(null, 'view');
    }

    function isEditable() {
    	$album = $this->getAlbum();
		if ($album) return ($album->isEditable() && $this -> authorization() -> isAllowed(null, 'edit'));
        return $this -> authorization() -> isAllowed(null, 'edit');
    }

    function isDeletable() {
    	$album = $this->getAlbum();
		if ($album) return ($album->isEditable() && $this -> authorization() -> isAllowed(null, 'delete'));
        return $this -> authorization() -> isAllowed(null, 'delete');
    }
	
	function isCommentable() {
		$album = $this->getAlbum();
		if ($album) return ($album->isCommentable() && $this -> authorization() -> isAllowed(null, 'comment'));
		return $this -> authorization() -> isAllowed(null, 'comment');
	}
	
	function isDownloadable() {
		return $this -> authorization()->isAllowed(null, 'download');
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
	public function getFirstGenre($params = array()) {
		$params['type'] = 'song';
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
	public function getDuration() {
		return gmdate("H:i:s", $this -> duration);
	}
	
	public function getNoPlayImage() {
		if (!$this->wave_noplay) return null;
		$file = Engine_Api::_()->getDbtable('files', 'storage')->find($this->wave_noplay)->current();
		if($file){
			return $file->map();
		}
		else {
			$id = $this->wave_noplay*-1;
			return 'application/modules/Ynmusic/externals/images/wave_noplay_'.$id.'.png';
		}
	}
	
	public function getPlayImage() {
		if (!$this->wave_play) return null;
		$file = Engine_Api::_()->getDbtable('files', 'storage')->find($this->wave_play)->current();
		if($file){
			return $file->map();
		}
		else {
			$id = $this->wave_noplay*-1;
			return 'application/modules/Ynmusic/externals/images/wave_play_'.$id.'.png';
		}
	}
}
