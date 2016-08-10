<?php
class Ynjobposting_Model_DbTable_Features extends Engine_Db_Table {
    protected $_rowClass = 'Ynjobposting_Model_Feature';
	public function getFeatureRowByJobId($job_id)
	{
		$select = $this-> select() -> where('job_id = ?', $job_id) -> limit(1);
		return $this->fetchRow($select);
	}
}