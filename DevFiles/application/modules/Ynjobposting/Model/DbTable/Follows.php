<?php
class Ynjobposting_Model_DbTable_Follows extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Follow';
	
	public function getFollowBy($company_id, $user_id)
	{
		$select = $this -> select() 
						-> where('company_id = ?', $company_id)
						-> where('user_id = ?', $user_id)
						-> limit(1);
		return $this -> fetchRow($select);				
	}
	
	public function getFollowByCompanyId($company_id)
	{
		$select = $this -> select() 
						-> where('active = 1')
						-> where('company_id = ?', $company_id);
		return $this -> fetchAll($select);				
	}
	
	public function getFollowByUserIdSelect($user_id)
	{
		$select = $this -> select() 
						-> where('active = 1')
						-> where('user_id = ?', $user_id);
		return $select;				
	}
}
