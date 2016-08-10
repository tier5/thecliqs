<?php


class Yncontest_Model_DbTable_Settings extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_settings';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Setting';


	public function getSettingByContest($contestId){
		$select = $this->select()->where('contest_id=?', $contestId)->limit(1);
		$results  = $this->fetchRow($select);
	
		return $results;
	}
	
}
