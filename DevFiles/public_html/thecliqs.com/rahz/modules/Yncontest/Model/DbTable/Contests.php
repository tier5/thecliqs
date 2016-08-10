<?php

class Yncontest_Model_DbTable_Contests extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_contests';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Contest';
	
	public function getTranByContest($contest_id){
		$model = new Yncontest_Model_DbTable_Transactions();
		$select  = $model->select()->where('contest_id',$contest_id)
									->where("transaction_status = 'success'")
									->where("approve_status = 'pending'");
		$results = $model->fetchAll($select);
		return $results;		
	}
	
}

