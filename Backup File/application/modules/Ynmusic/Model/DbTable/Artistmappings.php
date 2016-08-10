<?php
class Ynmusic_Model_DbTable_Artistmappings extends Engine_Db_Table {
	protected $_name = 'ynmusic_artistmappings';
	
	public function deleteArtistsByItem($item) {
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> delete($tableName, array(
		    'item_id = ?' => $item -> getIdentity(),
		    'item_type = ?' => $item -> getType(),
		));
	}
	
	public function getArtistsByItem($item){
		$select = $this -> select()
						-> where("item_id = ?", $item -> getIdentity())
						-> where("item_type = ?", $item -> getType());
		return $this -> fetchAll($select);				
	}
	
	public function getArtistIdsByItem($item){
		$select = $this -> select()
						-> from($this->info('name'), 'artist_id')
						-> where("item_id = ?", $item -> getIdentity())
						-> where("item_type = ?", $item -> getType());
		return $select->query()->fetchAll(PDO::FETCH_ASSOC, 0);			
	}
	
	public function getItemsByArtist($artist) {
		$select = $this -> select()
						-> where("artist_id = ?", $artist -> getIdentity());
		return $this -> fetchAll($select);	
	}
	
	public function getItemIds($artistIds, $type) {
		$tableName = $this -> info('name');
		$select = $this -> select() -> distinct()
						-> from("$tableName as map", "map.item_id")
						-> where("item_type = ?", $type);
		if(count($artistIds)){
			$select -> where('artist_id IN (?)', $artistIds);	
		}				
		$select -> order('artistmapping_id DESC');
		return $select;
	}
	
	public function getItemIdsAssoc($artistIds, $type) {
		$select = $this-> getItemIds($artistIds, $type);
		$rows = $this->fetchAll($select);
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row->item_id;
		}
		return $ids;
	}
}
