<?php
class Ynmusic_Model_DbTable_PlaylistSongs extends Engine_Db_Table {
	protected $_name = 'ynmusic_playlist_songs';
	protected $_rowClass = 'Ynmusic_Model_PlaylistSong';
	
	public function getMapRow($playlist_id, $song_id) {
		$select = $this -> select()
						-> where("song_id = ?", $song_id)
						-> where("playlist_id = ?", $playlist_id)
						-> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getSongs($playlist_id) {
		$select = $this -> select()
						-> from($this->info('name'), 'song_id')
						-> where("playlist_id = ?", $playlist_id)
						-> order("order ASC");
		$songIds = $select->query()->fetchAll(PDO::FETCH_ASSOC, 0);
		$ids_str = implode(',', $songIds);
		if (empty($songIds)) return array();
		$table = Engine_Api::_()->getItemTable('ynmusic_song');
		return $table->fetchAll($table->select()->where('song_id IN (?)', $songIds)->order(new Zend_Db_Expr("FIELD(song_id, $ids_str)")));
	}
	
	public function getSongIds($playlist_id) {
		$select = $this -> select()
						-> from($this->info('name'), 'song_id')
						-> where("playlist_id = ?", $playlist_id)
						-> order("order ASC");
		$songIds = $select->query()->fetchAll(PDO::FETCH_ASSOC, 0);
		return $songIds;
	}
	
	public function updateSongsOrder($playlist_id, $order) {
		foreach ($order as $id => $song_id) {
			if ($song_id) {
				$where = array (
					$this->getAdapter()->quoteInto('playlist_id = ?', $playlist_id),
					$this->getAdapter()->quoteInto('song_id = ?', $song_id)
				);
				$data = array ('order' => $id);
				$this->update($data, $where);
			}
		}
	}
	
	public function deleteSongs($playlist_id, $deleted) {
		foreach ($deleted as $song_id) {
			if ($song_id) {
				$where = array (
					$this->getAdapter()->quoteInto('playlist_id = ?', $playlist_id),
					$this->getAdapter()->quoteInto('song_id = ?', $song_id)
				);
				$this->delete($where);
			}
		}
	}
}
