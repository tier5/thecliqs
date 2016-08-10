<?php
class Ynjobposting_Model_DbTable_Sponsors extends Engine_Db_Table {
    protected $_rowClass = 'Ynjobposting_Model_Sponsor';
	
	public function getSponsorRowByCompanyId($company_id)
	{
		$select = $this-> select() -> where('company_id = ?', $company_id) -> limit(1);
		return $this->fetchRow($select);
	}
	
	public function getSponsorCompanyIds()
	{
		$select = $this->select()->where("active = 1");
		$sponsors = $this->fetchAll($select);
		$companyIds = array();
		foreach ($sponsors as $sponsor){
			$companyIds[] = $sponsor->company_id;
		}
		return $companyIds;
	}
	
}