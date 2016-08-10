<?php
class Ynbusinesspages_Model_DbTable_Covers extends Engine_Db_Table 
{
	protected $_rowClass = 'Ynbusinesspages_Model_Cover';
	
	public function getCoverByBusiness($business)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
		}
		if ($businessId)
		{
			$select = $this->select()->where("business_id = ? ", $businessId)->order("order ASC");
			return $this->fetchAll($select);
		}
		else 
		{
			return array();
		}
	}
	
	public function getMaxOrderByBusiness($business)
	{
		$businessId = 0;
		if(is_object($business))
		{
			$businessId = $business -> getIdentity();
		}
		else if(is_numeric($business))
		{
			$businessId = (int)$business;
		}
		$select = $this -> select()
		-> where("business_id = ? ", $businessId)
		-> order("order DESC")
		-> limit(1)	
		;
		$cover = $this -> fetchRow($select);
		if(is_null($cover))
		{
			return 0;
		}
		else 
		{
			return $cover -> order;
		}
	}
}
