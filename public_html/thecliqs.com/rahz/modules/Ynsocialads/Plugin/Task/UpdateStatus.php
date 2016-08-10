<?php
class Ynsocialads_Plugin_Task_UpdateStatus extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		// Check and update status of Ads
		$array = array("deleted", "completed", "denied", "draft");
		$tableAd = Engine_Api::_() -> getItemTable('ynsocialads_ad');
		$select = $tableAd -> select();
		$select -> where('status NOT IN (?)', $array);
		$ads = $tableAd -> fetchAll($select);
		foreach($ads as $ad)
		{
			Engine_Api::_() -> ynsocialads() -> checkAndUpdateStatus($ad);
		}
	}

}
