<?php
class Ynjobposting_Model_DbTable_Industrymaps extends Engine_Db_Table {
	protected $_name = 'ynjobposting_industry_company_maps';
	protected $_rowClass = 'Ynjobposting_Model_Industrymap';
	
	public function checkExistIndustryByCompany($industry_id, $company_id)
	{
		$select = $this -> select() -> where('industry_id = ?', $industry_id) -> where('company_id =?', $company_id) -> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getMainIndustryByCompanyId($company_id)
	{
		$select = $this -> select() -> where('company_id = ?', $company_id) -> where('main = 1');
		return $this -> fetchRow($select);
	}
	
	public function getSubIndustryByCompanyId($company_id)
	{
		$select = $this -> select() -> where('company_id = ?', $company_id) -> where('main = 0');
		return $this -> fetchAll($select);
	}
	
	public function getIndustriesByCompanyId($company_id)
	{
		$select = $this -> select() -> where('company_id = ?', $company_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteCompaniesByIndustryId($industry_id)
	{
		$select = $this -> select() -> where('industry_id = ?', $industry_id);
		$rows = $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
	
	public function deleteIndustriesByCompanyId($company_id)
	{
		$select = $this -> select() -> where('company_id = ?', $company_id);
		$rows =  $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
}
