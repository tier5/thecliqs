<?php


class Yncontest_Model_DbTable_Rules extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_rules';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Rule';

	public function getRuleByContest($contestId){
		$select = $this->select()->where('contest_id=?', $contestId);
		$result  = $this->fetchAll($select);
		return $result;
	}
	public function getLastRule($contestId){
		$select = $this->select()->where('contest_id=?', $contestId)->order('rule_id DESC');
		$results  = $this->fetchRow($select);		
		return $results;
	}
	public function getPreRule($params){
		$select = $this->select()->where('contest_id=?', $params['contestId'])->where('rule_id<?',$params['ruleId'])->order('rule_id DESC');
		$results  = $this->fetchRow($select);
		return $results;
	}
	public function getNextRule($params){
		$select = $this->select()->where('contest_id=?', $params['contestId'])->where('rule_id>?',$params['ruleId'])->order('rule_id ASC');
		$results  = $this->fetchRow($select);
		return $results;
	}
	
}
