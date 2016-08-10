<?php
class Ynbusinesspages_Model_DbTable_Packagemodules extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Packagemodule';
    
	public function getModuleByPackageId($package_id)
	{
		$select = $this -> select() -> where('package_id = ?', $package_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteRowsByPackageId($package_id)
	{
		$select = $this -> select() -> where('package_id = ?', $package_id);
		$rows =  $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
}