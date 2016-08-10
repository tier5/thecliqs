<?php
class Ynmusic_Model_DbTable_Albums extends Engine_Db_Table
{
   protected $_rowClass = 'Ynmusic_Model_Album';
  
   public function getAblumsByUser($user)
   {
		$select = $this -> getAlbumsSelect(array('user_id' => $user -> getIdentity()));
		$albums = $this -> fetchAll($select);
		return $albums;
   }
  
   public function getOtherAblumsByUser($user, $album, $limit = null)
   {
		$select = $this -> getAlbumsSelect(array('user_id' => $user -> getIdentity()));
		$select -> where('album_id <> ?', $album -> getIdentity());
		if(isset($limit)){
			$select -> limit($limit);
		}
		$albums = $this -> fetchAll($select);
		return $albums;
   }
   
   public function getAlbumsPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getAlbumsSelect($params));
	}

	public function getAlbumsSelect($params = array()) {
    	$albumTableName = $this -> info('name');

    	$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
    	$userTblName = $userTbl -> info('name');
		
		$tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagsTblName = $tagsTbl -> info('name');

		$select = $this -> select();
		$select -> from("$albumTableName as album", "album.*");

		if(isset($params['title']) && !empty($params['title'])) {
			$select->where('album.title LIKE ?', '%'.$params['title'].'%');
		}
		
		if(isset($params['keyword']) && !empty($params['keyword'])) {
			$select->where('album.title LIKE ?', '%'.$params['keyword'].'%');
		}
		
		if (isset($params['owner']) && !empty($params['owner'])) 
    	{
			$select -> setIntegrityCheck(false)
					-> joinLeft("$userTblName as user", "user.user_id = album.user_id", "")
    				-> where('user.displayname LIKE ?', '%'.$params['owner'].'%');
    	}
		
		if (isset($params['user_id']) && !empty($params['user_id'])) 
    	{
    		$select->where('album.user_id = ?', $params['user_id']);
    	}
		
		if (isset($params['artist']) && !empty($params['artist'])) 
    	{
    		$artistTable = Engine_Api::_() -> getItemTable('ynmusic_artist');
			$artistIds = $artistTable -> getIdsByTitle($params['artist']);
			if($artistIds) {
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappings = $artistMappingsTable -> fetchAll($artistMappingsTable -> getItemIds($artistIds, 'ynmusic_album'));
				$albumIds = array();
				foreach($artistMappings as $artistMapping) {
			        $albumIds[]= $artistMapping -> item_id;
				}
				if(count($albumIds)) {
					$select -> where('album.album_id IN (?)', $albumIds);
				} else {
					$select->where("1 = 0");
				}
			} else {
				$select -> where("1 = 0");
			}
    	}
		
		if (isset($params['artist_id']) && !empty($params['artist_id'])) {
    		
			$artistIds = array($params['artist_id']);
			if(!empty($artistIds)) {
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappings = $artistMappingsTable -> fetchAll($artistMappingsTable -> getItemIds($artistIds, 'ynmusic_album'));
				$albumIds = array();
				foreach($artistMappings as $artistMapping) {
			        $albumIds[]= $artistMapping -> item_id;
				}
				if(count($albumIds)) {
					$select -> where('album.album_id IN (?)', $albumIds);
				} else {
					$select->where("1 = 0");
				}
			} else {
				$select -> where("1 = 0");
			}
    	}
		
		if (isset($params['genre']) && !empty($params['genre'])) 
    	{
    		$genreTable = Engine_Api::_() -> getItemTable('ynmusic_genre');
			$genreIds = $genreTable -> getIdsByTitle($params['genre']);
			if($genreIds) {
				$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
				$genreMappings = $genreMappingsTable -> fetchAll($genreMappingsTable -> getItemIds($genreIds, 'ynmusic_album'));
				$albumIds = array();
				foreach($genreMappings as $genreMapping) {
			        $albumIds[]= $genreMapping -> item_id;
				}
				if(count($albumIds)) {
					$select -> where('album.album_id IN (?)', $albumIds);
				} else {
					$select->where("1 = 0");
				}
			} else {
				$select -> where("1 = 0");
			}
    	}
		
		if (isset($params['featured']) && $params['featured'] != '' && $params['featured'] != 'all') 
    	{
    		$select->where('featured = ?', $params['featured']);
    	}
		
		if (isset($params['created_from']) && !empty($params['created_from'])) {
			$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_from']);
			if ($date) {
				$select -> where("album.creation_date >= ?", $date);
			}
		}
		
		if (isset($params['created_to']) && !empty($params['created_to'])) {
			$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_to']);
			if ($date) {
				$select -> where("album.creation_date <= ?", $date);
			}
		}
		
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tagsTblName, "$tagsTblName.resource_id = album.album_id", "") -> where($tagsTblName . '.resource_type = ?', 'ynmusic_album') -> where($tagsTblName . '.tag_id = ?', $params['tag']);
		}

		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = 'album.creation_date';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = 'album.like_count';
					$params['direction'] = 'DESC';
					break;
				
				case 'most_discussed':
					$params['order'] = 'album.comment_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_viewed':
					$params['order'] = 'album.view_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = 'album.play_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = 'album.title';
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = 'album.title';
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
			$select -> order('album.creation_date DESC');
		}
		return $select;
	}

	public function getSearchAlbums($params = array()) {
		$select = $this->getAlbumsSelect($params);
		return $this->fetchAll($select);
	}
	
	/**
	 * function for get featured albums
	 */
	 public function getFeaturedAlbums() {
	 	$select = $this->select()->where('featured = ?', 1) -> limit(10);
		return $this->fetchAll($select);
	 }
	 
	public function importItem($item) {
		if (!in_array($item->getType(), array('mp3music_album'))) return false;
		if ($this->getImportedItem($item)) {
			$album = $this->getImportedItem($item);
		}
		else {
			//import album info
			$values	= array(
				'user_id' => $item->user_id,
				'title' => $item->title,
				'description' => ($item->description) ? $item->description : '',
				'play_count' => $item->play_count,
				'featured' => $item->is_featured,
				'import_id' => $item->getIdentity(),
				'import_type' => $item -> getType()
			);
			$album = $this->createRow();
			$album->setFromArray($values);
			$album->save();
			$album->creation_date = $item->creation_date;
			$album->modified_date = $item->modified_date;
			$album->save();
			
			//clone album photo
			if ($item->photo_id) {
				$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id);
				if ($photo) {
					$album->setPhoto($photo, 'photo_id');
				}
			}
			
			//clone authorization
			Engine_Api::_()->ynmusic()->cloneAuth($item, $album);
		}
		
		//import songs of albums	
		$songs = $item->getSongs();
		foreach ($songs as $song) {
			if (!$album->canAddSongs()) break;
			if (!Engine_Api::_()->ynmusic()->hasImported($song)) {
				Engine_Api::_()->getItemTable('ynmusic_song')->importItem($song, $album);
			}
		}
		
		Engine_Api::_()->getDbTable('imports', 'ynmusic')->updateItem($album, $item, 'imported');
	}

	public function hasImportedItem($item)
	{
		$result = $this -> getImportedItem($item);
		return ($result)?true:false;
	}

	public function getImportedItem($item) {
		$select = $this->select()->where('import_id = ?', $item->getIdentity())->where('import_type = ?', $item->getType());
		return $this->fetchRow($select);
	}
}