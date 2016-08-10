<?php
class Ynbusinesspages_Plugin_Task_CheckExpiredClaim extends Core_Plugin_Task_Abstract {
	public function execute() {
        $now = date("Y-m-d H:i:s");
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $maxClaimDays = $settings->getSetting('ynbusinesspages_claiming_expire', 3);
        $type = ($maxClaimDays == 1) ? 'day' : 'days';
        $expiredDate = date_sub(date_create($now), date_interval_create_from_date_string($maxClaimDays.' '.$type));
        $requestTbl = Engine_Api::_()->getDbTable('claimrequests', 'ynbusinesspages');
        $select = $requestTbl -> select() 
          -> where("status = ?", 'approved')
          -> where("approve_date <= ?", date_format($expiredDate, "Y-m-d H:i:s"));
        $requests = $requestTbl -> fetchAll($select);
        $ids = array();
        foreach ($requests as $request) {
            array_push($ids, $request->business_id);
        }
        if (count($ids)) {
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
            $select = $table->select()
                ->where('business_id IN (?)', $ids)
                ->where('status = ?', 'claimed')
                ->where('user_id <> ?', 0);
            $businesses = $table->fetchAll($select);
            foreach ($businesses as $business) {
				//remove from list admin, member
				$adminList = $business -> getAdminList();
       			$memberList = $business -> getMemberList();
       			$businessOwner = $business -> getOwner();
				
				// REMOVE USER FROM MEMBER ROLE
				if ($memberList -> has($businessOwner))
				{
					$memberList -> remove($businessOwner);
					$row = $business -> membership() -> getMemberInfo($businessOwner);
					$row -> delete();
				}
							
				// REMOVE OLD ADMIN
	       		if ($adminList -> has($businessOwner))
				{
					$adminList -> remove($businessOwner);
					$row = $business -> membership() -> getMemberInfo($businessOwner);
					$row -> delete();
				}
				
				//delete claim
				$request = $requestTbl -> getClaimRequest($businessOwner -> getIdentity(), $business -> getIdentity());
				if($request)
				{
					$request -> delete();
				}
				
                $commentTbl = Engine_Api::_() -> getDbtable('comments', 'core');
                $where = array();
                $where[] = $commentTbl->getAdapter()->quoteInto('resource_type = ?', 'ynbusinesspages_business');
                $where[] = $commentTbl->getAdapter()->quoteInto('resource_id = ?', $business->getIdentity());
                $commentTbl->delete($where);
                
                $user_id = $business->user_id;
                $business -> status = 'unclaimed';
				$superAdmins = Engine_Api::_() -> user() -> getSuperAdmins();
				foreach($superAdmins as $superAdmin)
				{
					$business -> user_id = $superAdmin -> getIdentity();
					break;
				}
				$business -> is_claimed = true;
                $business -> save();
				
                //send notifications
                $user = Engine_Api::_()->user()->getUser($user_id);
                $notifyApi -> addNotification($user, $user, $business, 'ynbusinesspages_business_unclaimed');
            }
        }
    }
}