<?php
class Ynfeed_Model_DbTable_Tags extends Engine_Db_Table
{
    protected $_rowClass = 'Ynfeed_Model_Tag';
	public function getTagsByAction($action_id = 0, $type = "")
	{
		$select = $this -> select() -> where("action_id = ?", $action_id) -> where("item_type = ?", $type);
		return $this -> fetchAll($select);
	}
	
	public function deleteTagsByAction($action_id = 0)
	{
		return $this -> delete(array('action_id' => $action_id));
	}
}
?>
