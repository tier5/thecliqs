<?php
class Ynmusic_Model_DbTable_Songs extends Engine_Db_Table
{
  protected $_rowClass = 'Ynmusic_Model_Song';
  
  public function getSongsPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getSongsSelect($params));
	}

	public function getSongsSelect($params = array()) {

		$songTableName = $this -> info('name');

    	$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
    	$userTblName = $userTbl -> info('name');
		
		$tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagsTblName = $tagsTbl -> info('name');

		$select = $this -> select();
		$select -> from("$songTableName as song", "song.*");
	   	
		if(isset($params['title']) && !empty($params['title'])) {
			$select->where('song.title LIKE ?', '%'.$params['title'].'%');
		}
		
		if(isset($params['keyword']) && !empty($params['keyword'])) {
			$select->where('song.title LIKE ?', '%'.$params['keyword'].'%');
		}
		
		if (isset($params['owner']) && !empty($params['owner'])) 
    	{
			$select -> setIntegrityCheck(false)
					-> joinLeft("$userTblName as user", "user.user_id = song.user_id", "")
    				-> where('user.displayname LIKE ?', '%'.$params['owner'].'%');
    	}
		
		if (isset($params['user_id']) && !empty($params['user_id'])) 
    	{
    		$select->where('song.user_id = ?', $params['user_id']);
    	}
		
		if (isset($params['artist']) && !empty($params['artist'])) 
    	{
    		
    		$artistTable = Engine_Api::_() -> getItemTable('ynmusic_artist');
			$artistIds = $artistTable -> getIdsByTitle($params['artist']);
			if($artistIds) {
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappings = $artistMappingsTable -> fetchAll($artistMappingsTable -> getItemIds($artistIds, 'ynmusic_song'));
				$songIds = array();
				foreach($artistMappings as $artistMapping) {
			        $songIds[]= $artistMapping -> item_id;
				}
				if(count($songIds)) {
					$select -> where('song.song_id IN (?)', $songIds);
				} else {
					$select->where("1 = 0");
				}
			} else {
				$select -> where("1 = 0");
			}
    	}
		
		if (!empty($params['artist_id'])) {
			$artistIds = array($params['artist_id']);
			if(!empty($artistIds)) {
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappings = $artistMappingsTable -> fetchAll($artistMappingsTable -> getItemIds($artistIds, 'ynmusic_song'));
				$songIds = array();
				foreach($artistMappings as $artistMapping) {
			        $songIds[]= $artistMapping -> item_id;
				}
				if(count($songIds)) {
					$select -> where('song.song_id IN (?)', $songIds);
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
				$genreMappings = $genreMappingsTable -> fetchAll($genreMappingsTable -> getItemIds($genreIds, 'ynmusic_song'));
				$songIds = array();
				foreach($genreMappings as $genreMapping) {
			        $songIds[]= $genreMapping -> item_id;
				}
				if(count($songIds)) {
					$select -> where('song.song_id IN (?)', $songIds);
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
				$select -> where("song.creation_date >= ?", $date);
			}
		}
		
		if (isset($params['created_to']) && !empty($params['created_to'])) {
			$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_to']);
			if ($date) {
				$select -> where("song.creation_date <= ?", $date);
			}
		}
		
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tagsTblName, "$tagsTblName.resource_id = song.song_id", "") -> where($tagsTblName . '.resource_type = ?', 'ynmusic_song') -> where($tagsTblName . '.tag_id = ?', $params['tag']);
		}
		
		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = 'song.creation_date';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = 'song.like_count';
					$params['direction'] = 'DESC';
					break;
				
				case 'most_discussed':
					$params['order'] = 'song.comment_count';
					$params['direction'] = 'DESC';
					break;	
					
				case 'most_viewed':
					$params['order'] = 'song.view_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = 'song.play_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = 'song.title';
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = 'song.title';
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
			$select -> order('song.creation_date DESC');
		}
		return $select;
	}
	
	public function getSearchSongs($params = array()) {
		$select = $this->getSongsSelect($params);
		return $this->fetchAll($select);
	}
	
	public function getAvalableSongs($ids) {
		$ids_str = implode(',', $ids);
		$select = $this->select()->where('song_id IN (?)', $ids);
		$select->order((new Zend_Db_Expr("FIELD(song_id, $ids_str)")));
		$rows = $this->fetchAll($select);
		$songs = array();
		foreach ($rows as $row) {
			if ($row->isViewable()) {
				$songs[] = $row;
			}
		}
		return $songs;
	}
	
	public function importItem($item, $album = null, $playlist = null) {
		//import song from mp3music and se music
		if (!in_array($item->getType(), array('mp3music_album_song', 'music_playlist_song'))) return false;
		// check duplicate file_id
		if($item->getType() == 'music_playlist_song')
		{
			$playlistSongTable = Engine_Api::_() -> getItemTable($item->getType());
			$select = $playlistSongTable -> select();
			$select -> where("file_id = ?", $item -> file_id) 
					-> where("song_id <> ?", $item -> song_id) 
					-> order("order ASC") 
					-> limit(1);
			if($row = $this -> fetchRow($select))
			{
				if(Engine_Api::_()->getItemTable('ynmusic_song')->getImportedItem($row))
					return false;
			}
		}
		if (Engine_Api::_()->ynmusic()->hasImported($item)) return false;
		$oldAlbum = $item->getParent();
		if (!$oldAlbum) return false;
		$oldFile = Engine_Api::_()->getItemTable('storage_file')->getFile($item->file_id);
		if (!$oldFile) return false;
		
		if($oldAlbum -> getType() == 'music_playlist')
		{
			$user_id = $oldAlbum -> owner_id;
			$downloadable = 1;
			$album_id = 0;
		}
		else 
		{
			$user_id = $oldAlbum->user_id;
			$downloadable = $oldAlbum->is_download;
			$album_id = $album->getIdentity();
		}
		$values	= array(
			'user_id' => $user_id,
			'title' => $item->title,
			'downloadable' => $downloadable,
			'album_id' => $album_id,
			'order' => $item->order,
			'play_count' => $item->play_count,
			'import_id' => $item->getIdentity(),
			'import_type' => $item->getType()
		);
		
		$song = $this->createRow();
		$song->setFromArray($values);
		$song->save();
		$song->creation_date = $oldAlbum->creation_date;
		$song->modified_date = $oldAlbum->modified_date;
		$song->save();
		
		//clone authorization
		Engine_Api::_()->ynmusic()->cloneAuth($oldAlbum, $song);
		
		//clone album photo
		if ($oldAlbum->photo_id) {
			$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($oldAlbum->photo_id);
			if ($photo) {
				$song->setPhoto($photo, 'photo_id');
			}
		}
		
		//clone song file
		$file = Engine_Api::_()->ynmusic()->createSong($oldFile, array('user_id' => $user_id));
		if ($file && $file instanceof Storage_Model_File) {
			$song->file_id = $file->getIdentity();
			//add duration
			require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/getid3/getid3.php';
			$tempFile = $file->temporary();
			$getID3 = new getID3;
			$ThisFileInfo = $getID3->analyze($tempFile);
			$song->duration = floor(@$ThisFileInfo['playtime_seconds']);
			$song->save();
		}
		
		$output = array();
		$result = -1;
		exec('`/usr/bin/which lame` --help 2>&1', $output, $result);
		
		if ($result != 0) {
			$random = rand(1, 10);
			$song->wave_play = -1*$random;
			$song->wave_noplay = -1*$random;
			$song->update_wave = 0;
			$song->save();
		}
		else {
			//add wave photo
			require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/SongWave.php';
			$waveParh = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.'waves.png';
			$play_top = '#00d6ff';
			$play_bot = '#80ebff';
			$noplay_top = '#666666';
			$noplay_bot = '#b3b3b3';
			$params = array(
	            'parent_type' => 'ynmusic_song',
	            'parent_id' => $song->getIdentity(),
	            'user_id' => $user_id
	        );
			$waveApi = new SongWave();
			$storage = Engine_Api::_() -> storage();
			$waveApi->createWavePhoto($song->getFilePath(), $play_top, $play_bot);
	        $aMain = $storage -> create($waveParh, $params);
	        $song->wave_play = $aMain -> file_id;
			$waveApi->createWavePhoto($song->getFilePath(), $noplay_top, $noplay_bot);
	        $aMain = $storage -> create($waveParh, $params);
	        $song->wave_noplay = $aMain -> file_id;
			$song->update_wave = 1;
			$song->save();
		}
		if($oldAlbum -> getType() == 'mp3music_album')
		{
			//update playlist songs
			$playlistSongsTbl = Engine_Api::_()->getDbTable('playlistSongs', 'mp3music');
			$select = $playlistSongsTbl->select()->where('album_song_id = ?', $item->getIdentity());
			$rows = $playlistSongsTbl->fetchAll($select);
			foreach ($rows as $row) {
				$playlist = Engine_Api::_()->getItem('mp3music_playlist', $row->playlist_id);
				if ($playlist) {
					$importedPlaylist = (Engine_Api::_()->getItemTable('ynmusic_playlist')->hasImportedItem($playlist)) ? Engine_Api::_()->getItemTable('ynmusic_playlist')->getImportedItem($playlist) : null;
					if ($importedPlaylist) {
						$importedPlaylist->addSong($song);
					}
				}
			}
		}
		elseif($oldAlbum -> getType() == 'music_playlist' && $playlist) 
		{
			$playlist->addSong($song);
		}
	}
	
	public function hasImportedItem($item)
	{
		$result = $this -> getImportedItem($item);
		return ($result)?true:false;
	}
	
	public function getImportedItem($item) 
	{
		$select = $this->select()->where('import_id = ?', $item->getIdentity()) -> where('import_type = ?', $item -> getType());
		return $this->fetchRow($select);
	}  
}