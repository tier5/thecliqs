<?php

class Ynbusinesspages_Model_DbTable_Contacts extends Engine_Db_Table
{
	protected $_rowClass = 'Ynbusinesspages_Model_Contact';
	protected $_name = 'ynbusinesspages_contacts';
	
	public function getContactByBusiness($business = null)
	{
		if (is_null($business))
		{
			return null;
		}
		$select = $this -> select() -> where("business_id = ?", $business -> getIdentity()) -> limit(1);
		return $this -> fetchRow($select);
	}
	
}
