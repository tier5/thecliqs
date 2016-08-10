<?php
class Ynjobposting_Model_DbTable_Jobtypes extends Engine_Db_Table {
		
	public function getAllJobTypes() {
		return $this -> fetchAll($this -> select());
	}
	
	public function getJobTypeArray()
	{
		$typeArray = array();
		$select = $this -> select();
		$types = $this -> fetchAll($select);
		foreach($types as $type)
		{
			$typeArray[$type -> jobtype_id] = $type -> title;  
		}
		return $typeArray;
	}
}
