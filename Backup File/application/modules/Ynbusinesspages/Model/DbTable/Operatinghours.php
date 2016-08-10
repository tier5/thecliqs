<?php
class Ynbusinesspages_Model_DbTable_Operatinghours extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Operatinghour';
    
	public function getHoursByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteAllHoursByBusinessId($business_id)
	{
		$select = $this->select()->where('business_id = ?', $business_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}