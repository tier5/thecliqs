<?php
class Ynmusic_Model_DbTable_Genremappings extends Engine_Db_Table {
	protected $_name = 'ynmusic_genremappings';
	
	public function deleteGenresByItem($item) {
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> delete($tableName, array(
		    'item_id = ?' => $item -> getIdentity(),
		    'item_type = ?' => $item -> getType(),
		));
	}
	
	public function getGenresByItem($item){
		$select = $this -> select()
						-> where("item_id = ?", $item -> getIdentity())
						-> where("item_type = ?", $item -> getType());
		return $this -> fetchAll($select);				
	}
	
	public function getGenreIdsByItem($item){
		$select = $this -> select()
						-> from($this->info('name'), 'genre_id')
						-> where("item_id = ?", $item -> getIdentity())
						-> where("item_type = ?", $item -> getType());
		return $select->query()->fetchAll(PDO::FETCH_ASSOC, 0);			
	}
	
	public function getItemsByGenre($genre) {
		$select = $this -> select()
						-> where("genre_id = ?", $genre -> getIdentity());
		return $this -> fetchAll($select);	
	}
	
	public function getItemIds($genreIds, $type) {
		$tableName = $this -> info('name');
		$select = $this -> select() -> distinct()
						-> from("$tableName as map", "map.item_id");
		if (!empty($type))	$select->where("item_type = ?", $type);
		if(count($genreIds)){
			$select -> where('genre_id IN (?)', $genreIds);	
		}				
		$select -> order('genremapping_id DESC');
		return $select;
	}
	
	public function getItemIdsAssoc($genreIds, $type) {
		$select = $this-> getItemIds($genreIds, $type);
		$rows = $this->fetchAll($select);
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row->item_id;
		}
		return $ids;
	}
}
