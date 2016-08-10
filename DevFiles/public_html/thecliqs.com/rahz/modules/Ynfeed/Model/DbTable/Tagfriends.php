<?php
class Ynfeed_Model_DbTable_Tagfriends extends Engine_Db_Table
{
	public function getWithByAction($action_id = 0)
	{
		$select = $this -> select() -> where("action_id = ?", $action_id);
		return $this -> fetchAll($select);
	}
	public function deleteWithByAction($action_id = 0)
	{
		return $this -> delete(array('action_id' => $action_id));
	}
}
?>
