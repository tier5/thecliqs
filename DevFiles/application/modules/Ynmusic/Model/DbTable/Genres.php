<?php
class Ynmusic_Model_DbTable_Genres extends Engine_Db_Table {
  	protected $_rowClass = 'Ynmusic_Model_Genre';
  	
	public function getGenreByTitle($title) {
   		$select = $this -> select();
		$select -> where("title = ?", $title) -> limit(1);
		return $this -> fetchRow($select);
   }
	
	public function getIdsByTitle($title) {
   		$select = $this -> getSelect(array('title' => $title));
		$genres = $this -> fetchAll($select);
		$genreIds = array();
		foreach($genres as $genre) {
			$genreIds[] = $genre -> getIdentity();
		}
		return $genreIds;
   }
	
  	public function getPaginator($params = array()) { 
    	$paginator = Zend_Paginator::factory($this->getSelect($params));
    	if( !empty($params['page']) ) {
      		$paginator->setCurrentPageNumber($params['page']);
    	}
    	if( !empty($params['limit']) ) {
      		$paginator->setItemCountPerPage($params['limit']);
    	}   
    	return $paginator;
  	}
	 
  	public function getSelect($params = array()) {
		$select = $this -> select();
		
		if(isset($params['title']) && !empty($params['title'])) {
			$select->where('title LIKE ?', '%'.$params['title'].'%');
		}
		
		if (!empty($params['genre_ids'])) {
			$select->where('genre_id IN (?)', $params['genre_ids']);
		}
		
		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		
		if(isset($params['admin']) && $params['admin']) {
			$select -> where('isAdmin = ?', 1);
		}
		
	    if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('title ASC');
		}
		return $select;
	}
	
	public function checkTitle($title, $id = 0) {
		$select = $this->select()->where('title LIKE ?', $title)->where('isAdmin = ?', 1);
		$row = $this->fetchRow($select);
		if ($row) {
			if ($row->genre_id == $id) return true;
			return false;	
		}
		return true;
	}
}