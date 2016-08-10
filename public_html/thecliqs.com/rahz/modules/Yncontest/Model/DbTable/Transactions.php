<?php

class Yncontest_Model_DbTable_Transactions extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_transactions';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Transaction';
	
	public function getTranBySec($security){
		$select = $this->select()->where('security = ?', $security);
		$results = $this->fetchAll($select);
		return $results;
	}
	
	public function getTranByContest($contest_id){
		$select  = $this->select()->where('contest_id = ?',$contest_id)
									->where("transaction_status = 'success'")
									->where("approve_status = 'pending'");
		$results = $this->fetchAll($select);
		
		return $results;		
	}
}

