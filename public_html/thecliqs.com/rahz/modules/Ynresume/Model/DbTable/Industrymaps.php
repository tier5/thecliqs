<?php
class Ynresume_Model_DbTable_Industrymaps extends Engine_Db_Table {
	protected $_name = 'ynresume_industrymaps';
	
	public function getRowByIndustryId($industry_id)
	{
		$select = $this -> select() -> where('industry_id = ?', $industry_id) -> limit(1);
		return $this -> fetchRow($select);
	}
}
