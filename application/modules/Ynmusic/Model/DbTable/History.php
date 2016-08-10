<?php
class Ynmusic_Model_DbTable_History extends Engine_Db_Table {
	protected $_name = 'ynmusic_history';
	protected $_rowClass = 'Ynmusic_Model_History';
	
	public function getHistoryPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getHistorySelect($params));
	}
	
	public function getHistorySelect($params = array()) {
		$songTbl = Engine_Api::_()->getItemTable('ynmusic_song');
		$songTblName = $songTbl->info('name');
		$playlistTbl = Engine_Api::_()->getItemTable('ynmusic_playlist');
		$playlistTblName = $playlistTbl->info('name');
		$albumTbl = Engine_Api::_()->getItemTable('ynmusic_album');
		$albumTblName = $albumTbl->info('name');
		$user_id = $params['user_id'];
		$tblName = $this->info('name');
		$songSelect = $songTbl->select()
			->from($songTblName, array(
				'item_id' => 'song_id',
				'title',
				'like_count',
				'view_count',
				'play_count',
				'item_creation_date' => 'creation_date',
				new Zend_Db_Expr ("'song' AS item_type")
			))
			->setIntegrityCheck(false);
		if ($user_id) {
			$songSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = song_id AND $tblName.item_type = 'ynmusic_song'", array("$tblName.creation_date", "$tblName.history_id"));
		}
		else {
			$songSelect->join($tblName, "$tblName.item_id = song_id AND $tblName.item_type = 'ynmusic_song'", array("$tblName.creation_date", "$tblName.history_id"));
		}
			
			
		$albumSelect = $albumTbl->select()
			->from($albumTblName, array(
				'item_id' => 'album_id',
				'title',
				'like_count',
				'view_count',
				'play_count',
				'item_creation_date' => 'creation_date',
				new Zend_Db_Expr ("'album' AS item_type")
			))
			->setIntegrityCheck(false);
		
		if ($user_id) {
			$albumSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = album_id AND $tblName.item_type = 'ynmusic_album'", array("$tblName.creation_date", "$tblName.history_id"));
		}
		else {
			$albumSelect->join($tblName, "$tblName.item_id = album_id AND $tblName.item_type = 'ynmusic_album'", array("$tblName.creation_date", "$tblName.history_id"));
		}
			
		$playlistSelect = $playlistTbl->select()
			->from($playlistTblName, array(
				'item_id' => 'playlist_id',
				'title',
				'like_count',
				'view_count',
				'play_count',
				'item_creation_date' => 'creation_date',
				new Zend_Db_Expr ("'playlist' AS item_type")
			))
			->setIntegrityCheck(false);
		
		if ($user_id) {
			$playlistSelect->join($tblName, "$tblName.user_id = $user_id AND $tblName.item_id = playlist_id AND $tblName.item_type = 'ynmusic_playlist'", array("$tblName.creation_date", "$tblName.history_id"));
		}
		else {
			$playlistSelect->join($tblName, "$tblName.item_id = playlist_id AND $tblName.item_type = 'ynmusic_playlist'", array("$tblName.creation_date", "$tblName.history_id"));	}
		
		$selects = array();
		if (empty($params['type']) || $params['type'] == 'all' || $params['type'] == 'song') {
			$selects[] = $songSelect;
		}
		if (empty($params['type']) || $params['type'] == 'all' || $params['type'] == 'album') {
			$selects[] = $albumSelect;
		}
		if (empty($params['type']) || $params['type'] == 'all' || $params['type'] == 'playlist') {
			$selects[] = $playlistSelect;
		}
		
		foreach ($selects as $select) {
			if(isset($params['keyword']) && !empty($params['keyword'])) {
				$select->where('title LIKE ?', '%'.$params['keyword'].'%');
			}
			
			if (isset($params['genre']) && !empty($params['genre'])) {
	    		$genreTable = Engine_Api::_() -> getItemTable('ynmusic_genre');
				$genreIds = $genreTable -> getIdsByTitle($params['genre']);
				if($genreIds) {
					$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
					$genreMappings = $genreMappingsTable -> fetchAll($genreMappingsTable -> getItemIds($genreIds, ''));
					$ids = array();
					foreach($genreMappings as $genreMapping) {
				        $ids[]= $genreMapping -> item_id;
					}
					if(count($ids)) {
						$select -> where('item_id IN (?)', $ids);
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
					$select -> where("item_creation_date >= ?", $date);
				}
			}
			
			if (isset($params['created_to']) && !empty($params['created_to'])) {
				$date = Engine_Api::_() -> ynmusic() -> getFromDaySearch($params['created_to']);
				if ($date) {
					$select -> where("item_creation_date <= ?", $date);
				}
			}
			
		}
		
		$db = $this->getAdapter();
		$select = $db->select()->union($selects);
		
		if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = 'creation_date';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = 'like_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_viewed':
					$params['order'] = 'view_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = 'play_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = 'title';
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = 'title';
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
			$select -> order('creation_date DESC');
		}
 		return $select;
	}

	public function updateItem($user, $item) {
		$select = $this->select()
			->where('user_id = ?', $user->getIdentity())
			->where('item_id = ?', $item->getIdentity())
			->where('item_type = ?', $item->getType());
		$row = $this->fetchRow($select);
		if (!$row) {
			$row = $this->createRow();
			$row->user_id = $user->getIdentity();
			$row->item_type = $item->getType();
			$row->item_id = $item->getIdentity();
		}
		$row->creation_date = date('Y-m-d H:i:s');
		$row->modified_date = date('Y-m-d H:i:s');
		$row->save();
	}

	public function removeHistory($user, $ids = array()) {
		$where = array();
        $where[] = $this->getAdapter()->quoteInto('user_id = ?', $user->getIdentity());
		if (!empty($ids)) {
			$where[] = $this->getAdapter()->quoteInto('history_id IN (?)', $ids);
		}
		$this->delete($where);
	}
}
