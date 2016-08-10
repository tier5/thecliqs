<?php
class Yncontest_Model_DbTable_Entries extends Engine_Db_Table{
	 
	 
	protected $_name = 'yncontest_entries';
	 
	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Entry';
	 
	public function getEntriesContest($params = array()){
		$select = $this->select()
		->where('contest_id = ?' , $params['contestID'])
		->where('user_id = ?', $params['user_id']);
		$result = $this->fetchAll($select);
		return $result;
	}

	public function getEntriesContest2($params = array()){
		$select = $this->select()	->where('contest_id = ?' , $params['contestID']);
		if(isset($params['approve_status']))
			$select -> where("approve_status= 'approved'");
		$result = $this->fetchAll($select);
		return $result;
	}
	 
	public function getEntryByvote($params = array()){
		$select = $this->select()
		->where('contest_id = ?' , $params['contestID'])
		->where("entry_status = 'published' or entry_status = 'win'")
		->where("approve_status = 'approved'")
		->order('vote_count DESC')
		->limit($params['award_number']);

		$result = $this->fetchAll($select);
		return $result;
	}
	 
	public function getEntryByOwner($params = array()){
		$select = $this->select()
		->where('contest_id = ?' , $params['contestID'])
		->where('waiting_win = 1')
		->where("entry_status = 'published' or entry_status = 'win'")
		->where("approve_status = 'approved'")
		;
	  

		$result = $this->fetchAll($select);
		return $result;
	}
	 

}
