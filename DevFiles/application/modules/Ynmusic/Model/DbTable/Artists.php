<?php
class Ynmusic_Model_DbTable_Artists extends Engine_Db_Table
{
   protected $_rowClass = 'Ynmusic_Model_Artist';
   
   public function getArtistByTitle($title) {
   		$select = $this -> select();
		$select -> where("title = ?", $title) -> limit(1);
		return $this -> fetchRow($select);
   }
   
   public function getIdsByTitle($title) {
   		$select = $this -> getArtistsSelect(array('title' => $title));
		$artists = $this -> fetchAll($select);
		$artistIds = array();
		foreach($artists as $artist) {
			$artistIds[] = $artist -> getIdentity();
		}
		return $artistIds;
   }
   
   public function getArtistsPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getArtistsSelect($params));
	}

	public function getArtistsSelect($params = array()) {
		$select = $this -> select();
		
		if(isset($params['title']) && !empty($params['title'])) {
			$select->where('title LIKE ?', '%'.$params['title'].'%');
		}
		
		if(isset($params['keyword']) && !empty($params['keyword'])) {
			$select->where('title LIKE ?', '%'.$params['keyword'].'%');
		}
		
		if(isset($params['country']) && !empty($params['country']) && $params['country'] != 'all') {
			$select->where('country = ?', $params['country']);
		}
		
		if(isset($params['genre']) && !empty($params['genre'])) {
			//get table genre & query for genreIds
			$genreTable = Engine_Api::_() -> getItemTable('ynmusic_genre');
			$genres = $genreTable -> fetchAll($genreTable -> getSelect(array('title' => $params['genre'])));
			$genreIds = array();
			foreach($genres as $genre) {
		        $genreIds[]= $genre -> getIdentity();
			}
			//get table mapping & query for artistIds
			$artistIds = array();
			$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
			if(count($genreIds)){
				$genreMappings = $genreMappingsTable -> fetchAll($genreMappingsTable -> getItemIds($genreIds, 'ynmusic_artist'));
				foreach($genreMappings as $genreMapping) {
			        $artistIds[]= $genreMapping -> item_id;
				}
				//query in artist table
				if(count($artistIds)){
					$select -> where('artist_id IN (?)', $artistIds);
				} else {
					$select->where("1 = 0");
				}
			}
			else {
				$select->where("1 = 0");
			}
		}
		
		if(isset($params['admin']) && $params['admin']) {
			$select -> where('isAdmin = ?', true);
		}
		
		if (!empty($params['artist_ids'])) {
			$select->where('artist_id IN (?)', $params['artist_ids']);
		}
		
		if (!empty($params['browse_by'])) {
			$params['order'] = 'title';
			$params['direction'] = ($params['browse_by'] == 'z_a') ? 'DESC' : 'ASC';
		}
		
		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		
	    if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('artist_id DESC');
		}
		return $select;
	}
	
	public function getSearchArtists($params = array()) {
		$select = $this->getArtistsSelect($params);
		return $this->fetchAll($select);
	}
}