<?php

class Ynfeed_Model_DbTable_Maps extends Engine_Db_Table
{
	protected $_name = 'ynfeed_maps';
	protected $_rowClass = 'Ynfeed_Model_Map';
	
	public function getMapByAction($action_id = 0)
	{
		$select = $this -> select() -> where("action_id = ?", $action_id) -> limit(1);
		return $this -> fetchRow($select);
	}
}
