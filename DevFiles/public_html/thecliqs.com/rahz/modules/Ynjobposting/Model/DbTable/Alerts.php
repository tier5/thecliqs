<?php
class Ynjobposting_Model_DbTable_Alerts extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Alert';
	
    public function getLatestRowByEmail($email)
    {
        $select = $this -> select() -> where('email = ?', $email)-> order('alert_id DESC');
        return $this -> fetchRow($select);
    }
    
	public function getEmails()
	{
		$tableName = $this->info('name');
		$select = $this -> select()-> distinct() -> from("$tableName", "$tableName.email");
		return $this -> fetchAll($select);
	}
	
	public function getRowsByEmail($email)
	{
		$select = $this -> select() -> where('email = ?', $email);
		return $this -> fetchAll($select);
	}
	
	public function getRowsByIP($ip)
	{
		return $this -> select() -> distinct()-> from($this, 'email') -> where('ip = ?', $ip) -> query()
		->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function deleteRowsByEmail($email)
	{
		$rows = $this -> getRowsByEmail($email);
		foreach($rows as $delete_row)
		{
			$delete_row -> delete();
		}
	}
}
