<?php
class Ynbusinesspages_Model_DbTable_Claimrequests extends Engine_Db_Table {
	protected $_rowClass = 'Ynbusinesspages_Model_Claimrequest';
	
	public function approveClaim($claim)
	{
		$claim -> status = 'approved';
        $now = date("Y-m-d H:i:s");
        
        $claim -> approve_date = $now;
		$claim -> save();
		
		$select = $this -> select() -> where('business_id = ?', $claim -> business_id)
									-> where('user_id <> ?', $claim -> user_id);
		$owner = $claim -> getOwner();
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $claim -> business_id);
		if(!empty($owner) && !empty($business))
		{
			$business -> user_id = $claim -> user_id;
			$business -> status = 'claimed';
			$business -> is_claimed = false;
			$business -> save();
			
			$adminList = $business -> getAdminList();
       		
			// ADD USER TO ADMIN ROLE
			if (!$adminList->has($owner))
			{
				$adminList -> add($owner);
				if (!$business -> membership() -> isMember($owner))
				{
					$business -> membership() -> addMember($owner) -> setUserApproved($owner) -> setResourceApproved($owner);	
				}
				$business -> membership() -> getMemberInfo($owner) -> setFromArray(array('list_id' => $adminList->getIdentity())) -> save();
			}
			
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($owner, $business, $business, 'ynbusinesspages_claim_approved');
		}
		//set others to denied						
		$rows = $this -> fetchAll($select);
		if(isset($rows))
		{
			foreach($rows as $row)
			{
				$row -> status = 'denied';
				$row -> save();
			}
		}
	}
	
	public function getClaimRequestByUser($uid){
		$select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
	}
	
	public function getClaimRequestsPending($user_id)
	{
		$arrIds = array();
		$select = $this -> select() -> where('user_id = ?', $user_id) -> where('status = ?', 'pending');
		$rows =  $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$arrIds[] = $row -> business_id;
		}
		if(empty($arrIds))
		{
			$arrIds[] = 0;
		}
		return $arrIds;
	}
	
	public function getClaimRequest($user_id, $business_id)
	{
		$select = $this -> select() -> where('user_id = ?', $user_id) -> where('business_id = ?', $business_id) -> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function denyAllClaims($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		//set others to denied						
		$rows = $this -> fetchAll($select);
		if(isset($rows))
		{
			foreach($rows as $row)
			{
				$row -> status = 'denied';
				$row -> save();
			}
		}
	}
}
