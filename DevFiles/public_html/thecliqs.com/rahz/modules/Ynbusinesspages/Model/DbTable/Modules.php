<?php
class Ynbusinesspages_Model_DbTable_Modules extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Module';
    
	public function getAllModules(){
		$select = $this -> select();
		return $this -> fetchAll($select);
	}
	
}