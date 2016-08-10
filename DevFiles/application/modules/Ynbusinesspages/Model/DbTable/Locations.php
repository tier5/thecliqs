<?php
class Ynbusinesspages_Model_DbTable_Locations extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Location';
    
	public function getLocationsByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		return $this -> fetchAll($select);
	}
	
	public function getMainLocationByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id) -> where('main = ?', '1') -> limit(1);
		return $this -> fetchRow($select);
	}
	public function getSubLocationsByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id)-> where('main = ?', '0');
		return $this -> fetchAll($select);
	}
	
	public function deleteAllLocationsByBusinessId($business_id)
	{
		$select = $this->select()->where('business_id = ?', $business_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}