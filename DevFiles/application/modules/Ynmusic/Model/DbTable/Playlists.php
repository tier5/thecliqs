<?php
class Ynmusic_Model_DbTable_Playlists extends Engine_Db_Table {
	protected $_rowClass = 'Ynmusic_Model_Playlist';
	
	public function getUserPlaylist($user) {
		$select = $this -> select() -> where('user_id =?', $user -> getIdentity());
		return $this -> fetchAll($select);
	}
	
	public function getOtherByUser($user, $playlist, $limit = null) {
		$select = $this -> select() -> where('user_id =?', $user -> getIdentity());
		$select -> where('playlist_id <> ?', $playlist -> getIdentity());
		if(isset($limit)){
			$select -> limit($limit);
		}
		$select -> order(' RAND() ');
		return $this -> fetchAll($select);
   	}
   
	public function getPaginator($params = array()) {
		$paginator = Zend_Paginator::factory($this -> getSelect($params));
		if (!empty($params['page'])) {
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit'])) {
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getSelect($params = array()) {
		$p_name = $this -> info('name');
		$select = $this -> select();
		
		$tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagsTblName = $tagsTbl -> info('name');
		
		if (!empty($params['keyword']))
			$select -> where("$p_name.title LIKE ?", "%{$params['keyword']}%");
		if (!empty($params['title']))
			$select -> where("$p_name.title LIKE ?", "%{$params['title']}%");
		if (!empty($params['owner'])) {
			$select -> joinLeft('engine4_users as u', "u.user_id = $p_name.user_id", '')
					-> where("u.displayname LIKE ?", "%{$params['owner']}%");
		}
		if (isset($params['user_id']) && !empty($params['user_id'])) {
    		$select->where("$p_name.user_id = ?", $params['user_id']);
    	}
		
		if (!empty($params['genre'])) {
    		$genreTable = Engine_Api::_() -> getItemTable('ynmusic_genre');
			$genreIds = $genreTable -> getIdsByTitle($params['genre']);
			if($genreIds) {
				$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
				$genreMappings = $genreMappingsTable -> fetchAll($genreMappingsTable -> getItemIds($genreIds, 'ynmusic_playlist'));
				$playlistIds = array();
				foreach($genreMappings as $genreMapping) {
			        $playlistIds[]= $genreMapping -> item_id;
				}
				if(count($playlistIds)) {
					$select -> where("$p_name.playlist_id IN (?)", $playlistIds);
				} else {
					$select->where("1 = 0");
				}
			} else {
				$select -> where("1 = 0");
			}
    	}

		if (isset($params['created_from']) && !empty($params['created_from'])) {
			$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_from']);
			if ($date) {
				$select -> where("$p_name.creation_date >= ?", $date);
			}
		}
		
		if (isset($params['created_to']) && !empty($params['created_to'])) {
			$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_to']);
			if ($date) {
				$select -> where("$p_name.creation_date <= ?", $date);
			}
		}
		
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tagsTblName, "$tagsTblName.resource_id = $p_name.playlist_id", "") -> where($tagsTblName . '.resource_type = ?', 'ynmusic_playlist') -> where($tagsTblName . '.tag_id = ?', $params['tag']);
		}
		
		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = "$p_name.creation_date";
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = "$p_name.like_count";
					$params['direction'] = 'DESC';
					break;
					
				case 'most_viewed':
					$params['order'] = "$p_name.view_count";
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = "$p_name.play_count";
					$params['direction'] = 'DESC';
					break;
				
				case 'most_discussed':
					$params['order'] = "$p_name.comment_count";
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = "$p_name.title";
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = "$p_name.title";
					$params['direction'] = 'DESC';
					break;
				
				default:
					break;
			}
		}
		
		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		
	    if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order("$p_name.creation_date DESC");
		}
		return $select;
	}
	
	public function getSearchPlaylists($params = array()) {
		$select = $this->getSelect($params);
		return $this->fetchAll($select);
	}
	
	public function importItem($item) 
	{
		if (!in_array($item->getType(), array('mp3music_playlist', 'music_playlist'))) return false;
		$update = false;
		if (!$this -> hasImportedItem($item)) 
			$update = true;
		$playlist = $this->getImportedItem($item);
		//import songs of playlists	
		$songs = $item->getSongs();
		
		// SE music check file duplicated
		$aFiles = array();
		$table = Engine_Api::_()->getDbTable('playlistSongs', 'ynmusic');
		foreach ($songs as $song) 
		{
			if (!Engine_Api::_()->ynmusic()->hasImported($song)) 
			{
				// SE Music: import songs to ynmusic_song
				if($item->getType() == 'music_playlist')
				{
					Engine_Api::_()->getItemTable('ynmusic_song') ->importItem($song, null, $playlist);
				}
				else 
				{
					continue;
				}
			}
			$importedSong = Engine_Api::_()->getItemTable('ynmusic_song')->getImportedItem($song);
			if(!$importedSong)
			{
				continue;
			}
			$row = $table->getMapRow($playlist->getIdentity(), $importedSong->getIdentity());
			if (!$row) {
				if (!$playlist->canAddSongs()) {
					break;
				}
				$mapRow = $table -> createRow();
				$mapRow -> playlist_id = $playlist->getIdentity();
				$mapRow -> song_id = $importedSong->getIdentity();
				$mapRow -> save();
				$update = true;
			}
		}
		if ($update) Engine_Api::_()->getDbTable('imports', 'ynmusic')->updateItem($playlist, $item, 'imported');
	}
	public function hasImportedItem($item)
	{
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType()) -> limit(1);
		$playlist = $this -> fetchRow($select);
		return ($playlist)?true:false;
	}
	public function getImportedItem($item) {
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType()) -> limit(1);
		$playlist = $this -> fetchRow($select);
		if (!$playlist) {
			//import playlist info
			if($item -> getType() == 'music_playlist')
			{
				$user_id = $item -> owner_id;
			}
			else
			{
				$user_id = $item->user_id;
			}
			$values	= array(
				'user_id' => $user_id,
				'title' => $item->title,
				'description' => $item->description,
				'import_id' => $item->getIdentity(),
				'import_type' => $item -> getType()
			);
			$playlist = $this->createRow();
			$playlist->setFromArray($values);
			$playlist->save();
			$playlist->creation_date = $item->creation_date;
			$playlist->modified_date = $item->modified_date;
			$playlist->save();
			
			//clone playlist photo
			if ($item->photo_id) {
				$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id);
				if ($photo) {
					$playlist->setPhoto($photo, 'photo_id');
				}
			}
			
			//clone authorization
			Engine_Api::_()->ynmusic()->cloneAuth($item, $playlist);
		}
		
		return $playlist;
	}
}
