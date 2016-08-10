<?php
class Ynjobposting_Model_DbTable_Jobinfos extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Jobinfo';
	
	public function getRowInfoByJobId($job_id) {
		$select = $this->select()->where('job_id = ?', $job_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteAllInfoByJobId($job_id)
	{
		$select = $this->select()->where('job_id = ?', $job_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}
