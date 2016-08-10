<?php
class Ynbusinesspages_Model_DbTable_Checkin extends Engine_Db_Table 
{
	protected $_name = 'ynbusinesspages_checkin';
	protected $_rowClass = 'Ynbusinesspages_Model_Checkin';
	
	public function getUsersCheckIn($business)
	{
		$select = $this -> select() -> where ("business_id = ? ", $business->getIdetity());
		return $this->fetchAll($select);
	}
	
	public function isCheckedIn($user, $business)
	{
		$select = $this -> select() 
		-> where ("business_id = ? ", $business->getIdentity())
		-> where ("user_id = ? ", $user->getIdentity())
		-> limit (1)
		;
		$row = $this->fetchRow($select);
		if ($row)
		{
			return true;
		}
		return false;
	}
		
}