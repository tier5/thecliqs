<?php
class Ynbusinesspages_Model_DbTable_Follows extends Engine_Db_Table
{
	public function getFollowBusiness($business_id, $uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid)
                ->where('business_id = ?', $business_id);
        return $this->fetchRow($select);
    }
	
	public function getFollowBusinesses($uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
    }

    public function getUsersFollow($business_id) {
        $select = $this->select() -> from($this -> info('name'), 'user_id');
        $select->where('business_id= ?', $business_id);
		$userIds = array();
        foreach($this->fetchAll($select) as $userId)
		{
			$userIds[] = $userId['user_id'];
		}
		if(!$userIds)
		{
			return NULL;
		}
		return Engine_Api::_() -> user() -> getUserMulti($userIds);
    }
}
