<?php
class Ynjobposting_Model_DbTable_Sentjobs extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Sentjob';
	public function getJobIdsByEmail($email)
	{
		return $this -> select() -> from($this, 'job_id') -> where('email = ?', $email) -> query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function deleteRowsByEmail($email)
	{
		$rows = $this -> fetchAll($this -> select() -> where('email = ?', $email));
		foreach($rows as $delete_row)
		{
			$delete_row -> delete();
		}
	}
}
