<?php
class Ynbusinesspages_Model_DbTable_Founders extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Founder';
    
	public function getFoundersByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteAllFoundersByBusinessId($business_id)
	{
		$select = $this->select()->where('business_id = ?', $business_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}	