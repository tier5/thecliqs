<?php
class Ynjobposting_Model_DbTable_Companyinfos extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Companyinfo';
	
	public function getRowInfoByCompanyId($company_id)
	{
		$select = $this->select()->where('company_id = ?', $company_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteAllInfoByCompanyId($company_id)
	{
		$select = $this->select()->where('company_id = ?', $company_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}
