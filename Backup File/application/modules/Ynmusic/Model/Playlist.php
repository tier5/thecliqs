<?php
class Ynmusic_Model_Playlist extends Core_Model_Item_Abstract {
    function isViewable() {
        return $this -> authorization() -> isAllowed(null, 'view');
    }

    function isEditable() {
        return $this -> authorization() -> isAllowed(null, 'edit');
    }

    function isDeletable() {
        return $this -> authorization() -> isAllowed(null, 'delete');
    }
	
	function isCommentable() {
		return $this -> authorization() -> isAllowed(null, 'comment');
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
	
	public function getCountSongs() {
        return count($this->getSongs());
    }
	
	public function getCountAvailableSongs() {
		return count($this->getAvailableSongs());
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
	
	public function getSongs() {
		return Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic')->getSongs($this->getIdentity());
	}
	
	
	public function getFirstSong() {
		$songs = $this->getAvailableSongs();
		if (count($songs))
			return $songs[0];
		return null;
	}
	
	function getModifiedDate() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->modified_date));
        $date->setTimezone($timezone);
        return $date;
    }
	
	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynmusic_playlist',
			'action' => 'view',
			'id' => $this -> getIdentity(),
			'reset' => true,
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	
	public function getSlug($str = NULL, $maxstrlen = 64) {
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
	
	public function getFirstGenre($params = array()) {
		$params['type'] = 'playlist';
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
	
	public function getRichContent($view = false, $params = array()) {
		$zend_View = Zend_Registry::get('Zend_View');
	    // $view == false means that this rich content is requested from the activity feed
	    if($view == false){
			return $zend_View -> partial('_playlist_feed.tpl', 'ynmusic', array('item' => $this));
	    }
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
			$image -> open($file) -> write($path.'/m_'.$name) -> destroy();
		}
		else {
			$image -> open($file) -> resize(720, 720) -> write($path.'/m_'.$name) -> destroy();
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

	public function getDuration() {
		$songs = $this -> getSongs();
		$duration = 0;
		foreach($songs as $song) {
			$duration += $song -> duration;
		}
		return gmdate("H:i:s", $duration);
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
	
	public function updateSongsOrder($order) {
		Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic')->updateSongsOrder($this->getIdentity(), $order);
	}
	
	public function deleteSongs($deleted) {
		Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic')->deleteSongs($this->getIdentity(), $deleted);
	}
	
	public function canAddSongs() {
		$user = Engine_Api::_()->user()->getUser($this->user_id);
		if (!$user) return false;
		
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynmusic_playlist', $user->level_id, 'max_songs');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $user->level_id)
                ->where('type = ?', 'ynmusic_playlist')
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
	
	public function addSong($song) {
		if (!$this->canAddSongs()) return false;
		$table = Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic');
		$row = $table->getMapRow($this->getIdentity(), $song->getIdentity());
		if (!$row) {
			$mapRow = $table -> createRow();
			$mapRow -> playlist_id = $this->getIdentity();
			$mapRow -> song_id = $song->getIdentity();
			$mapRow -> save();
			return true;
		}
		return false;
	}
}
