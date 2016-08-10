<?php
class Ynjobposting_Model_DbTable_Joblevels extends Engine_Db_Table {
	
	public function getAllJobLevels() {
		return $this -> fetchAll($this -> select());
	}
	
	public function getJobLevelArray()
	{
		$levelArray = array();
		$select = $this -> select();
		$levels = $this -> fetchAll($select);
		foreach($levels as $level)
		{
			$levelArray[$level -> joblevel_id] = $level -> title;  
		}
		return $levelArray;
	}
}
