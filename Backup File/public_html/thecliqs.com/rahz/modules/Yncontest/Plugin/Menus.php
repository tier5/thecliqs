<?php

class Yncontest_Plugin_Menus
{
	public function canMyContests($row)
    { 
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        if(!is_object($viewer)) {
        	return false;
        }
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('contest', $viewer, 'view'))
        {
            return false;
        }
        return true;
    }
    public function canFriendsContests($row)
    {    
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	
    	if(!is_object($viewer)) {
    		return false;
    	}
    	if (!$viewer -> getIdentity())
    	{
    		return false;
    	}
    	if (!Engine_Api::_() -> authorization() -> isAllowed('contest', $viewer, 'view'))
    	{
    		return false;
    	}
    	 return array(  
	        'route' => 'yncontest_general',
	        'params' => array(	        
	          'action' => 'listing',
	          'contestsocial' => 'friend_contest',
	        )
	      );
    }
    
	public function canMyEntries($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if(!is_object($viewer)) {
        	return false;
        }
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('contest', $viewer, 'view'))
        {
            return false;
        }
        return true;
    }	
    public function canFavContest($row)
    {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	if(!is_object($viewer)) {
    		return false;
    	}
    	if (!$viewer -> getIdentity())
    	{
    		return false;
    	}
    	return true;
    }
    public function canFavEntries($row)
    {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	if(!is_object($viewer)) {
    		return false;
    	}
    	if (!$viewer -> getIdentity())
    	{
    		return false;
    	}
    	return true;
    }
	
	public function canCreateContest($row)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if(!is_object($viewer)) {
        	return false;
        }
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('contest', $viewer, 'createcontests'))
        {
            return false;
        }
		if (!Engine_Api::_() -> authorization() -> isAllowed('contest', $viewer, 'createcontests'))
        {
            return false;
        }
        return true;
    }	
    
    public function canAdminCategory() {
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$viewer || !$viewer->getIdentity() ) {
    		return false;
    	}
    	return true;
    }
	
}