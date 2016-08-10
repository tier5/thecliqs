<?php
class Ynbusinesspages_Model_DbTable_Renewals extends Engine_Db_Table {
	public function getRowByBusinessId($business_id)
	{
		$select = $this->select()->where('business_id = ?', $business_id) -> limit(1);
		return $this -> fetchRow($select);
	}
}
