<?php

class Ynmobile_Helper_Event extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('event','ynmobile');
    }
    
    function field_id(){
        $this->data['iEventId'] = $this->entry->getIdentity();
    }
    
    function field_as_attachment(){
        parent::field_as_attachment();
        $this ->field_info();
    }
    
    function field_isRspv(){
        $memberInfo = $this->entry -> membership() -> getMemberInfo($this->getViewer());
        
        $iRsvp = -1;
        if ($memberInfo){
            $iRsvp = $memberInfo -> rsvp;
        }
        
        $this->data['iRsvp'] =  $iRsvp;
        $this->data['bIsMember'] = $this->entry->membership()->isMember($this->getViewer(), true);
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_desc();
        $this->field_imgNormal();
        $this->field_timestamp();
        $this->field_imgFull();
        $this->field_info();
        $this->field_user();
        $this->field_isRspv();
        $this->field_canInvite();
    }
    
    function field_canInvite(){
        $this->data['bCanInvite'] =  $this->entry->authorization()->isAllowed($this->getViewer(), 'invite');
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_photos();
        $this->field_actions();
        $this->field_guestStats();
        $this->field_category();
    }
    
    function field_guestStats(){
        $event = $this->entry;
        
        
        $iTotalGoing = $event -> getAttendingCount();
        $iTotalAll = $event -> getAttendingCount() + $event -> getMaybeCount() + $event -> getNotAttendingCount();

        $temp = array(
            'iNumGoing' => $iTotalGoing,
            'iNumMaybe' => $event -> getMaybeCount(),
            'iNumNotAttending' => $event -> getNotAttendingCount(),
            'iNumAll' => $iTotalAll
        );
        
        $this->data['aGuestStatistic'] = $temp;
    }
    
    
    function field_info(){
        $this->data['sLocation'] =  $this->entry->location;
        if(strpos($this->entry->host,'younetco_event_key_') !== FALSE)
        {
            $user_id = substr($this->entry->host, 19, strlen($this->entry->host));
            $user = Engine_Api::_() -> getItem('user', $user_id);
            $this->data['sHost'] = $user->getTitle();
        } 
        else 
        {
            $this->data['sHost'] = $this->entry->host;
        }
		
		if($this-> getWorkingModule() == 'ynevent')
		{
        	$this->data['sCountry'] =  $this->entry->country;
        	$this->data['sCity'] = $this->entry->city;
        	$this->data['sAddress'] =  $this->entry->address;
        	$this->data['sZipCode']  = $this->entry->zip_code;
        	$this->data['fLatitude'] =  $this->entry->latitude;
        	$this->data['fLongitude'] =  $this->entry->longitude;
        	$this->data['bIsFeatured'] = $this->entry->featured?1:0;
		}
		else 
		{
			$this->data['sCountry'] =  '';
        	$this->data['sCity'] = '';
        	$this->data['sAddress'] =  '';
        	$this->data['sZipCode']  = '';
        	$this->data['fLatitude'] =  '';
        	$this->data['fLongitude'] =  '';
        	$this->data['bIsFeatured'] = 0;
		}
        $this->data['bIsResourceApproval'] =  $this->entry->membership()->isResourceApprovalRequired()?1:0;
        
        $event  = $this->entry;
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if($viewer){
            $row   = $row = $event->membership()->getRow($viewer);;
            
            $this->data['bOnRequest'] = (!$row->resource_approved && $row->user_approved) ? 1 : 0;
            $this->data['bIsInvited'] = ($row->resource_approved && !$row->user_approved) ? 1 : 0;
            $this->data['bIsMember'] = $event->membership()->isMember($viewer, true);
        }
        // Convert the dates for the viewer
        $start = strtotime($event -> starttime);
        $end = strtotime($event -> endtime);
        $create = strtotime($event -> creation_date);
        
        $oldTz = date_default_timezone_get();
        
        if ($viewer && $viewer -> getIdentity())
        {
            date_default_timezone_set($viewer -> timezone);
        }
        
        $start_date = date('Y-m-d H:i:s', $start);
        $end_date = date('Y-m-d H:i:s', $end);
        
        date_default_timezone_set($oldTz);
        
        $timeZone = Engine_Api::_()->ynmobile()->getUserTimeZone();
        $timeZone = $timeZone *(-1);
        $iStartTime = strtotime($timeZone.' hours',strtotime($start_date));
        $iEndTime = strtotime($timeZone.' hours',strtotime($end_date));
        
        $oldTz = date_default_timezone_get();
        // Convert the dates for the viewer
        if ($viewer && $viewer -> getIdentity())
        {
            date_default_timezone_set($viewer -> timezone);
        }
        
        $this->data['iStartTime'] = $iStartTime;
        $this->data['sStartTime'] = date('l, F j', $start);
        $this->data['sStartFullTime'] = date('l, F j', $start) . ' at ' . date('g:i a', $start);
        $this->data['sShortStartTime'] = $iStartTime;
        $this->data['iEndTime'] = $iEndTime;
        $this->data['sEndTime'] = date('l, F j', $end);
        $this->data['sEndFullTime'] = date('l, F j', $end) . ' at ' . date('g:i a', $end);
		
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		foreach( $roles as $role )
		{
			if( 1 === $auth->isAllowed($event, $role, 'view') )
			{
				$sViewPrivacy = $role;
			}
			if( 1 === $auth->isAllowed($event, $role, 'comment') )
			{
				$sCommentPrivacy = $role;
			}
		}
		$this->data['sCommentPrivacy'] = $sCommentPrivacy;
		$this->data['sViewPrivacy'] = $sViewPrivacy;
		
		date_default_timezone_set($oldTz);
    }
    
    function field_desc(){
        parent::field_desc();

        if($this-> getWorkingModule() == 'ynevent')
        {
            $this->data['sBriefDescription']= $this->entry->brief_description;
        }
        
    }
    
    function field_actions(){
        
    }
}
