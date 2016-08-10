<?php
class Ynlistings_Plugin_Task_CheckListing extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		$tableListing = Engine_Api::_() -> getItemTable('ynlistings_listing');
		$select = $tableListing -> select();
		$select -> where("approved_status = 'approved'");
		$select -> where("status = 'open'");
		$listing = $tableListing -> fetchAll($select);
		foreach($listing as $item)
		{
			Engine_Api::_() -> ynlistings() -> checkAndUpdateStatus($item);
		}
	}

}
