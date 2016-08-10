<?php
class Ynmusic_Api_Core extends  Core_Api_Abstract {
	
	protected $_roles = array(
	    'owner' => 'Just Me',
	    'owner_member' => 'Friends Only',
	    'owner_member_member' => 'Friends of Friends',
	    'owner_network'=> 'Friends and Networks',
	    'registered' => 'All Registered Members',
	    'everyone' => 'Everyone',  
	);
	
	  public function createSong($file, $params=array())
	  {
	    // upload to storage system
	    if (is_array($file)) {
	    	$name = $file['name'];
			$extension = substr($file['name'], strrpos($file['name'], '.')+1);
			$parent_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	      	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			
	    }
		elseif ($file instanceof Storage_Model_File){
			$name = $file->name;
			$extension = $file->extension;
			$parent_id = $params['user_id'];
	      	$user_id = $params['user_id'];
			$file = $file->temporary();
		}
		else return null;
		
	    $params = array_merge(array(
	      'type'        => 'song',
	      'name'        => $name,
	      'parent_type' => 'user',
	      'parent_id'   => $parent_id,
	      'user_id'     => $user_id,
	      'extension'   => $extension,
	    ), $params);
		
	    $item = Engine_Api::_()->storage()->create($file, $params);
	    return $item;
	  }
	  
	  public function saveMappingsForItem($item, $artist_ids, $genre_ids) {
	  	
	  		$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
			$genreTable = $genreTable = Engine_Api::_() -> getItemTable('ynmusic_genre');
			
	  		$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
	  		$artistTable = Engine_Api::_() -> getItemTable('ynmusic_artist');
	  		
	  		//save mapping artists and genres
			foreach($genre_ids as $genre_id) {
				if(trim($genre_id)) {
					if(!is_numeric($genre_id)) {
						$row = $genreTable -> getGenreByTitle($genre_id);
						if($row) {
							$genre_id = $row -> getIdentity();
						} else {
							//insert new to genre
							$genre = $genreTable -> createRow();
							$genre -> title = $genre_id;
							$genre -> isAdmin = false;
							$genre -> save();
							$genre_id = $genre -> getIdentity();
						}
					}
					$genre = Engine_Api::_() -> getItem('ynmusic_genre', $genre_id);
					if($genre) {
						$genreMappingsRow = $genreMappingsTable -> createRow();
						$genreMappingsRow -> genre_id = $genre -> getIdentity();
					    $genreMappingsRow -> item_type = $item -> getType();
						$genreMappingsRow -> item_id = $item -> getIdentity();
						$genreMappingsRow -> save();
					}
				}
			}
			
			//save mapping artists 
			foreach($artist_ids as $artist_id) {
				if(trim($artist_id)) {
					if(!is_numeric($artist_id)) {
						$row = $artistTable -> getArtistByTitle($artist_id);
						if($row) {
							$artist_id = $row -> getIdentity();
						} else {
							//insert new to artist
							$artist = $artistTable -> createRow();
							$artist -> title = $artist_id;
							$artist -> isAdmin = false;
							$artist -> save();
							$artist_id = $artist -> getIdentity();
						}
					}
					$artist = Engine_Api::_() -> getItem('ynmusic_artist', $artist_id);
					if($artist) {
						$artistMappingsRow = $artistMappingsTable -> createRow();
						$artistMappingsRow -> artist_id = $artist -> getIdentity();
					    $artistMappingsRow -> item_type = $item -> getType();
						$artistMappingsRow -> item_id = $item -> getIdentity();
						$artistMappingsRow -> save();
					}
				}
			}
	  }

	public function getFromDaySearch($day) {
        $day = $day . " 00:00:00";
        $user_tz = date_default_timezone_get();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer -> getIdentity())
        {
            $user_tz = $viewer -> timezone;

        }
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($user_tz);
        $start = strtotime($day);
        date_default_timezone_set($oldTz);
        $fromdate = date('Y-m-d H:i:s', $start);
        return $fromdate;
    }
	
	public function canAddToPlaylist($item = null, $user = null) {
		$itemPermission = ($item) ? $item->isViewable() : true;
		if (is_null($user)) {
			$user = Engine_Api::_()->user()->getViewer();
		}
		$level_id = ($user->getIdentity()) ? $user->level_id : 5;
		$editPlaylistPermission = Engine_Api::_()->authorization()->getPermission($level_id, 'ynmusic_playlist', 'edit');
		if ($editPlaylistPermission) {
			$editPlaylistPermission = false;
			$playlists = Engine_Api::_()->getItemTable('ynmusic_playlist')->getUserPlaylist($user);
			foreach ($playlists as $playlist) {
				if ($playlist->isEditable() && $playlist->canAddSongs()) {
					$editPlaylistPermission = true;
					break;
				}
			}
		}
		return ($itemPermission && $editPlaylistPermission);
	}
	
	public function canCreatePlaylist() {
		$user = Engine_Api::_()->user()->getViewer();
		$level_id = ($user->getIdentity()) ? $user->level_id : 5;
		$createPlaylistPermission = Engine_Api::_()->authorization()->getPermission($level_id, 'ynmusic_playlist', 'create');
		return $createPlaylistPermission;
	}
	
	public function getUserMusicCount($type = 'song', $user = null) {
		if (!$user) {
			$user = Engine_Api::_()->user()->getViewer();
		}
		$item = 'ynmusic_'.$type;
		$table = Engine_Api::_()->getItemTable($item);
		$select = $table->select()->where('user_id = ?', $user->getIdentity());
		return count($table->fetchAll($select));
	}
	
	public function getItemViewPrivacy($item) {
		$view = Zend_Registry::get('Zend_View');
    	$auth = Engine_Api::_()->authorization()->context;
		$roles = array_keys($this->_roles);
		$item_role = '';
		foreach ($roles as $role) {
    		if(1 === $auth->isAllowed($item, $role, 'view'))
    			$item_role = $role;

		}
		
		if ($item_role == '') $item_role = 'owner';
		return array(
			'role' => $item_role,
			'label' => $view->translate($this->_roles[$item_role])
		);
	}
	
	public function getTagArray($item) {
		if (!$item) return array();
		$results = array();
		foreach ($item->tags()->getTagMaps() as $tagMap) {
       		$tag = $tagMap -> getTag();
    		if (empty($tag -> text))
            	continue;
        	$results[] = $tag;
		}
		return $results;
	}
	
	public function canUploadSong() {
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity()) return false;
		
		$canCreateAlbum = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmusic_album', null, 'create')->checkRequire();
		$canEditAlbum = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmusic_album', null, 'edit')->checkRequire();
		if ($canEditAlbum) {
			$canEditAlbum = false;
			$albumTable = Engine_Api::_() -> getDbTable('albums', 'ynmusic');
			$albums = $albumTable -> getAblumsByUser($viewer);
			foreach ($albums as $album) {
				if ($album->isEditable() && $album->canAddSongs()) {
					$canEditAlbum = true;
					break;
				}
			}
		}
		$canAddSong = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynmusic_song', null, 'create')->checkRequire();
		return ($canCreateAlbum || $canEditAlbum || $canAddSong);
	}
	
	public function getStorage($user) {
		$table = Engine_Api::_()->getDbTable('songs', 'ynmusic');
		$params = array();
		$params['user_id'] = $user -> getIdentity();
		$select = $table->getSongsSelect($params);
		$songs = $table->fetchAll($select);
		$sumFileSize = 0;
		foreach ($songs as $song) {
			if ($song->file_id) {
				$head = array_change_key_case(get_headers($song->getFilePath(), TRUE));
				$filesize = $head['content-length'];
				$sumFileSize += $filesize;
			}
		}
		return $sumFileSize;
	}
	
	public function hasImported($from) {
		$itemMatch = array(
			'mp3music_album' => 'ynmusic_album',
			'mp3music_playlist' => 'ynmusic_playlist',
			'mp3music_album_song' => 'ynmusic_song',
			'music_playlist' => 'ynmusic_playlist',
			'music_playlist_song' => 'ynmusic_song'
		);
		if (!in_array($from->getType(), array_keys($itemMatch))) {
			return true;
		}
		
		return  Engine_Api::_()->getItemTable($itemMatch[$from->getType()])->hasImportedItem($from);
	}
	
	public function cloneAuth($from, $to) {
		if (!$from || !$to) return false;
		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone',
		);
		$auth_arr = array('view', 'comment');
		$auth = Engine_Api::_() -> authorization() -> context;
        foreach ($auth_arr as $elem) {
        	$from_role = 'owner';
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($from, $role, $elem)) {
                	$from_role = $role;
                }
            }
			$authMax = array_search($from_role, $roles);
			foreach( $roles as $i => $role ) {
		        $auth->setAllowed($to, $role, $elem, ($i <= $authMax));
	      	}
        }
	}
	
	public function canUpdateImport($to, $from) {
		if (!$to || !$from) {
			return false;
		}
		if (!$to->canAddSongs()) return false;
		$songs = $from->getSongs();
		if ($to->getType() == 'ynmusic_album') {
			foreach ($songs as $song) {
				if (!$this->hasImported($song)) {
					return true;
				}
			}
			return false;
		}
		foreach ($songs as $song) {
			if ($this->hasImported($song)) {
				$importSong = Engine_Api::_()->getItemTable('ynmusic_song')->getImportedItem($song);
				$table = Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic');
				$row = $table->getMapRow($to->getIdentity(), $importSong->getIdentity());
				if (!$row) return true;
			}
		}
		return false;
	}
}