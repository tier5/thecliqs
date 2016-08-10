<?php
class Ynbusinesspages_Plugin_Task_CheckExpiredBusinesses extends Core_Plugin_Task_Abstract {
	public function execute() {
		$now = date("Y-m-d H:i:s");
		$businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
		$select = $businessTbl -> select() 
		  -> where("status IN (?)", array('published', 'closed'))
		  -> where("never_expire = ?", '0')
		  -> where("expiration_date < '$now'");
		$businesses = $businessTbl -> fetchAll($select);
		if (count($businesses)) {
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($businesses as $business) {
				$business -> status = 'expired';
				$business -> save();
				//send notifications
				if ($business->status == 'published') {
				    $owner = $business -> getOwner();
				    $notifyApi -> addNotification($owner, $owner, $business, 'ynbusinesspages_business_expired');
			
                }
            }
		}
	}
}