<?php
class Ynfeed_Model_DbTable_Hashtags extends Engine_Db_Table {
	protected $_rowClass = 'Ynfeed_Model_Hashtag';

	public function getHashtagFeeds($hashtag, $types, $params = array()) {
		$limit = (!empty($params['limit']) ? $params['limit'] : 15) * 2;
		$max_id = $params['max_id'];
		$select = $this -> select();
		if($types)
			$select-> where('action_type IN(?)', (array) $types);
		
		$select -> where('hashtag LIKE ?', $hashtag) 
			-> limit($limit);
		if (null !== $max_id) 
		{
			$select -> where('action_id <= ?', $max_id);
		}
		$data = $select -> query() -> fetchAll();
		$settings = array();
		foreach ($data as $row) {
			$settings[] = $row['action_id'];
		}

		return $settings;
	}
	public function getHashTagsByAction($action_id = 0)
	{
		$select = $this -> select() -> where("action_id = ?", $action_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteHashTagsByAction($action_id = 0)
	{
		return $this -> delete(array('action_id' => $action_id));
	}
}
?>
