<?php
class Ynfeed_WelcomeController extends Core_Controller_Action_Standard 
{
	public function friendRequestsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$limit = $this->_getParam('limit', 4);
		$this -> view -> friend_requests = Engine_Api::_() -> ynfeed() -> getFriendRequests(array('limit' => $limit));
	}
	
	public function memberSuggestionsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$limit = $this->_getParam('limit', 4);
		$arr_suggestions = array();
		$member_suggestions = Engine_Api::_() -> ynfeed() -> getMemberSuggestions(array('limit' => $limit));
		$this -> view -> member_suggestions = $member_suggestions;
	}

	public function groupSuggestionsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$limit = $this->_getParam('limit', 4);
		$category = $this->_getParam('category', 0);
		$group_suggestions = Engine_Api::_() -> ynfeed() -> getGroupSuggestions(array('limit' => $limit, 'category' => $category));
		$this -> view -> group_suggestions = $group_suggestions;
	}
	
	public function eventSuggestionsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$limit = $this->_getParam('limit', 6);
		$category = $this->_getParam('category', 0);
		$event_suggestions = Engine_Api::_() -> ynfeed() -> getEventSuggestions(array('limit' => $limit, 'category' => $category));
		$this -> view -> event_suggestions = $event_suggestions;
	}
	
	// GROUP ACTIONS
	// user join in group in public group case
	public function joinGroupAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if( null == ($group_id = $this->_getParam('group_id')) ||
	        null == ($subject = Engine_Api::_()->getItem('group', $group_id)) ) {
	      return;
    	}

		// If member is already part of the group
		if ($subject -> membership() -> isMember($viewer, true))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				// Set the request as handled
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'advgroup_invite');
				if ($notification)
				{
					$notification -> mitigated = true;
					$notification -> save();
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			return;
		}
		$activityType = 'group_join';
		$notifiTypeI = 'group_invite';
		if(Engine_Api::_() -> hasModuleBootstrap('advgroup'))
		{
			if ($subject -> is_subgroup)
			{
				$parent_group = $subject -> getParentGroup();
				if (!$parent_group -> membership() -> isMember($viewer, 1))
				{
					return;
				}
			}
			$activityType = 'advgroup_join';
			$notifiTypeI = 'advgroup_invite';
		}

		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			if ($subject -> membership() -> isMember($viewer))
			{
				$subject -> membership() -> setUserApproved($viewer);
			}
			else
			{
				$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
			}
			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, $notifiTypeI);
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($viewer, $subject, $activityType);

			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return;
	}

	// request join group for user in private group case
	public function requestGroupAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if( null == ($group_id = $this->_getParam('group_id')) ||
	        null == ($subject = Engine_Api::_()->getItem('group', $group_id)) ) {
	      return;
    	}
		$notiType = 'group_approve';	
		if(Engine_Api::_() -> hasModuleBootstrap('advgroup'))
		{	
			if ($subject -> is_subgroup)
			{
				$parent_group = $subject -> getParentGroup();
				if (!$parent_group -> membership() -> isMember($viewer, 1))
				{
					return;
				}
			}
			$notiType = 'advgroup_approve';	
		}

		// Process form
		$owner = $subject -> getOwner();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			if ($subject -> membership() -> isMember($viewer))
			{
				$subject -> membership() -> requestAgain($viewer);
			}
			else
			{
				$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
			}

			Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $viewer, $subject, 'advgroup_approve');
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return;
	}
	// EVENT ACTIONS
	public function joinEventAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		// Check resource approval
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if( null == ($event_id = $this->_getParam('event_id')) ||
	        null == ($subject = Engine_Api::_()->getItem('event', $event_id)) ) {
	      return;
    	}
		if ($subject -> membership() -> isResourceApprovalRequired())
		{
			$row = $subject -> membership() -> getReceiver() -> select() -> where('resource_id = ?', $subject -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> query() -> fetch(Zend_Db::FETCH_ASSOC, 0); ;
			if (empty($row))
			{
				// has not yet requested an invite
				return $this -> _helper -> redirector -> gotoRoute(array(
					'action' => 'request'
				));
			}
		}

		// Process
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$membership = $subject -> membership() -> getRow($viewer);
			$membership_status = false;
			if (!empty($membership))
			{
				$membership_status = $membership -> active;
			}

			$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
			$row = $subject -> membership() -> getRow($viewer);
			$row -> rsvp = 2;
			$row -> save();

			// Add activity if membership status was not valid from before
			if (!$membership_status)
			{
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
					$action = $activityApi -> addActivity($viewer, $subject, 'ynevent_join');
				else {
					$action = $activityApi -> addActivity($viewer, $subject, 'event_join');
				}
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		if ($row -> rsvp == 2)
		{
			if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
				$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
			else {
				$table = Engine_Api::_() -> getDbTable('follow', 'event');
			}
			$table -> setOptionFollowEvent($subject -> getIdentity(), $viewer -> getIdentity(), 1);
		}
	}

	public function requestEventAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		if( null == ($event_id = $this->_getParam('event_id')) ||
	        null == ($subject = Engine_Api::_()->getItem('event', $event_id)) ) {
	      return;
    	}

		// Process
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
			// Add notification
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
				$notifyApi -> addNotification($subject -> getOwner(), $viewer, $subject, 'ynevent_approve');
			else {
				$notifyApi -> addNotification($subject -> getOwner(), $viewer, $subject, 'event_approve');
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
	}
}