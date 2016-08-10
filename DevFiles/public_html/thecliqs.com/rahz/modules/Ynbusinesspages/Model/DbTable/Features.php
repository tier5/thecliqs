<?php
class Ynbusinesspages_Model_DbTable_Features extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Feature';
	public function getFeatureRowByBusinessId($business_id)
	{
		$select = $this-> select() -> where('business_id = ?', $business_id) -> limit(1);
		return $this->fetchRow($select);
	}
}