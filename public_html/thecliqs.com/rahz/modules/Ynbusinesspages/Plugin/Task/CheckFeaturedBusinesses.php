<?php
class Ynbusinesspages_Plugin_Task_CheckFeaturedBusinesses extends Core_Plugin_Task_Abstract {
	public function execute() {
		$now = date("Y-m-d H:i:s");
		$featureTbl = Engine_Api::_()->getDbTable('features', 'ynbusinesspages');
		$select = $featureTbl -> select() 
		-> where("active = ? ", '1')
		-> where("expiration_date < '$now'")
		;
		$features = $featureTbl -> fetchAll($select);
		if (count($features))
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($features as $feature)
			{
				$business = Engine_Api::_()->getItem('ynbusinesspages_business', $feature->business_id);
				if (!is_null($business))
				{
					$business -> featured = 0;
					$business -> save();
					
					//send notifications
					$owner = $business -> getOwner();
					$notifyApi -> addNotification($owner, $owner, $business, 'ynbusinesspages_business_unfeatured');
				}
				$feature -> active = 0;
                $feature -> expiration_date = null;
				$feature -> save();
			}
		}
	}
}