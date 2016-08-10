<?php


class Yncontest_Model_DbTable_Contesttypes extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'yncontest_contesttypes';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Yncontest_Model_Contesttype';

	public function getContestType(){
		$select = $this->select();
		$resutls = $this->fetchAll($select);
		$arr = array();
		foreach($resutls as $resutl)
			if($resutl->status == 1)
				$arr[$resutl->contesttype_id] = $resutl->title;
		
		return $arr;
	}
	
}
