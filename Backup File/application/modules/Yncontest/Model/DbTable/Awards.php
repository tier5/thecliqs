<?php


class Yncontest_Model_DbTable_Awards extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_awards';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Award';
	
	
	public function getAwardByContest($contestId){
		$select = $this->select()->where('contest_id=?', $contestId);
		$result  = $this->fetchAll($select);
		return $result;
	}
	
}
