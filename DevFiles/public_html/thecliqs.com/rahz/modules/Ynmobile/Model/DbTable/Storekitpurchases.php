<?php
class Ynmobile_Model_DbTable_Storekitpurchases extends Engine_Db_Table
{
	protected $_name = 'ynmobile_storekitpurchases';
	protected $_rowClass = 'Ynmobile_Model_Storekitpurchase';

	public function getProduct($appType, $packageId)
	{
		$select = $this
			-> select()
			-> where("storekitpurchase_type = ?", $appType)
			-> where("storekitpurchase_item_id = ?", $packageId)
			-> limit(1);
		
		return $this->fetchRow($select);
	}
}
