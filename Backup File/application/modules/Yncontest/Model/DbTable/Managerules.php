<?php


class Yncontest_Model_DbTable_Managerules extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_managerules';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Managerule';
	
	
	public function getRulesByOwner($user_id){
		$select = $this->select()->where('user_id=?', $user_id);
		
		$result  = $this->fetchAll($select);
		return $result;
	}
	
}
