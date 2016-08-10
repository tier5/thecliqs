<?php
class Ynsocialads_Model_DbTable_Statistics extends Engine_Db_Table {
    protected $_rowClass = 'Ynsocialads_Model_Statistic';
    
	public function checkUniqueViewByUserId($user_id, $ad_id, $type)
	{
		$select = $this->select()
						->where('user_id = ?', $user_id)
						->where('ad_id = ?', $ad_id)
						->where('type = ?', $type)
						->limit(1);
		if($this->fetchRow($select))
			return true;
		return false;				
	}
	
	public function checkUniqueViewByIP($ip, $ad_id, $type)
	{
		$select = $this->select()
						->where('IP = ?', $ip)
						->where('ad_id = ?', $ad_id)
						->where('type = ?', $type)
						->limit(1);
		if($this->fetchRow($select))
			return true;
		return false;				
	}
}