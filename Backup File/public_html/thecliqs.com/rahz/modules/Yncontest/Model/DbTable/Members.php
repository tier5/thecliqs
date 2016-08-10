<?php


class Yncontest_Model_DbTable_Members extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_members';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Member';
	
	public function getMemberContest($params = array()){
		$select = $this->select()
		 ->where('contest_id =?', $params['contestId'])
		 ->where('user_id', $params['user_id'])
  		 ->where('member_status = ?', 'approved');
	
		return $this->fetchRow($select);
	}
	public function getMemberContest2($params = array()){
		$select = $this->select()
		->where('contest_id =?', $params['contestId'])
		->where('user_id =?', $params['user_id']);	
		//echo $select;
		return $this->fetchRow($select);
	}
	

}
