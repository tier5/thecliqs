<?php

class Ynjobposting_Model_DbTable_Submissions extends Engine_Db_Table
{
	protected $_rowClass = 'Ynjobposting_Model_Submission';
	protected $_name = 'ynjobposting_submissions';
	
	public function getSubmissionByCompany($company = null)
	{
		if (is_null($company))
		{
			return null;
		}
		$select = $this -> select() -> where("company_id = ?", $company -> getIdentity()) -> limit(1);
		return $this -> fetchRow($select);
	}
	
}
