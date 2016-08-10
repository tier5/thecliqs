<?php
class Ynlistings_Model_DbTable_Follows extends Engine_Db_Table
{
  	protected $_name = 'ynlistings_follows';
	public function getRow($user_id, $owner_id)
	{
		$select = $this-> select();
		$select -> where('user_id = ?', $user_id);
		$select -> where('owner_id = ?', $owner_id);
		$select -> limit(1);
		return $this->fetchRow($select);
	}
}