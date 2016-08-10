<?php
class Ynfeed_Plugin_Core extends Zend_Controller_Plugin_Abstract 
{
	public function addActivity($event) 
	{
		$payload = $event -> getPayload();
		if (isset($payload['privacies']) && !empty($payload['privacies'])) 
		{
			$subject = $payload['subject'];
			$object = $payload['object'];
			
			$privacies = array();
			
			if(isset($payload['privacies']))
				$privacies = $payload['privacies'];
			$sGeneral = $sFriendList = $sNetwork = $sGroup = $sFriend = "";
			
			if(isset($privacies['general']))
				$sGeneral = $privacies['general'];
			if(isset($privacies['friend_list']))
				$sFriendList = $privacies['friend_list'];
			if(isset($privacies['network']))
				$sNetwork = $privacies['network'];
			if(isset($privacies['group']))
				$sGroup = $privacies['group'];
			if(isset($privacies['friend']))
				$sFriend = $privacies['friend'];
			
			 // Get object parent
		    $objectParent = null;
		    if( $object instanceof User_Model_User ) {
		      $objectParent = $object;
		    } else {
		      try {
		        $objectParent = $object->getParent('user');
		      } catch( Exception $e ) {}
		    }
			
			// Start general privacy
			$aNetwork = array();
			if($sNetwork)
			{
				$aNetwork = explode(',', $sNetwork);
			}
			$aFriendlist = array();
			if($sFriendList)
			{
				$aFriendlist = explode(',', $sFriendList);
			}
			$aFriend = array();
			if($sFriend)
			{
				$aFriend = explode(',', $sFriend);
			}
			$aGroup = array();
			if($sGroup)
			{
				$aGroup = explode(',', $sGroup);
			}
			
			$aGeneral = array();
			if($sGeneral)
			{
				$aGeneral = explode(',', $sGeneral);
			}
			$general_default = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
			if(!count($aGeneral) && !count($aFriendlist) && !count($aGroup) && !count($aNetwork) && !count($aFriend))
			{
				array_push($aGeneral, $general_default);
			}
			$general = Engine_Api::_() -> ynfeed() -> getMaxGeneralPrivacy($aGeneral, $object -> getType());

		    // Network & Everyone
		    if( in_array($general, array('everyone', 'network')) ) 
		    {
		      if ($object instanceof User_Model_User
		          && Engine_Api::_()->authorization()->context->isAllowed($object, 'network', 'view') ) 
		          {
		        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
		        $ids = $networkTable->getMembershipsOfIds($object);
		        $ids = array_unique($ids);
		        foreach( $ids as $id ) 
		        {
		          $event->addResponse(array(
		            'type' => 'network',
		            'identity' => $id,
		          ));
		        }
		      } elseif ($objectParent instanceof User_Model_User
		          && Engine_Api::_()->authorization()->context->isAllowed($object, 'owner_network', 'view') ) {
		        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
		        $ids = $networkTable->getMembershipsOfIds($objectParent);
		        $ids = array_unique($ids);
		        foreach( $ids as $id ) {
		          $event->addResponse(array(
		            'type' => 'network',
		            'identity' => $id,
		          ));
		        }
		      }
		    }
		
			// Everyone - Registered
		    if( $general == 'everyone' &&
		        Engine_Api::_()->authorization()->context->isAllowed($object, 'registered', 'view') ) {
		      $event->addResponse(array(
		        'type' => 'registered',
		        'identity' => 0
		      ));
		    }
		    
		    // Everyone
		    if( $general == 'everyone' &&
		        Engine_Api::_()->authorization()->context->isAllowed($object, 'everyone', 'view') ) {
		      $event->addResponse(array(
		        'type' => 'everyone',
		        'identity' => 0
		      ));
		    }
				
			if ($general == 'officer' && ($object instanceof Advgroup_Model_Group || $object instanceof Group_Model_Group) && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
			{
				$event -> addResponse(array(
					'type' => 'officer',
					'identity' => $object -> getIdentity()
				));
			}
			
			if ($general == 'admin' && $object instanceof Ynbusinesspages_Model_Business && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
			{
				$event -> addResponse(array(
					'type' => 'admin',
					'identity' => $object -> getIdentity()
				));
			}
			// End general privacy
			
			// Start network list privacy
			if(count($aNetwork))
			{
				if ($object instanceof User_Model_User
			          && Engine_Api::_()->authorization()->context->isAllowed($object, 'network', 'view') ) 
			     {
			        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
			        $ids = $networkTable->getMembershipsOfIds($object);
			        $ids = array_unique($ids);
			        foreach( $ids as $id ) 
			        {
			        	if(in_array($id, $aNetwork))
						{
				        	$event->addResponse(array(
					            'type' => 'network_list',
					            'identity' => $id,
					          ));
						}
			        }
			      }
			}
			// End network privacy
			
			// Start members list
			if (count($aFriendlist)) 
			{
				$owner_member_view = false;
				if ($object instanceof User_Model_User) {

					$owner_member_view = Engine_Api::_() -> authorization() -> isAllowed($object, 'member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view');
				} else if ($objectParent instanceof User_Model_User) {

					$owner_member_view = Engine_Api::_() -> authorization() -> isAllowed($object, 'owner_member', 'view') || Engine_Api::_() -> authorization() -> isAllowed($object, 'parent_member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'owner_member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'parent_member', 'view');
				}
				if ($owner_member_view) 
				{
					foreach ($aFriendlist as $id) {
						$event -> addResponse(array('type' => 'members_list', 'identity' => $id));
					}
				}
			}
			// End member list
			
			// Start friend
			if (count($aFriend)) 
			{
				$owner_member_view = false;
				if ($object instanceof User_Model_User) {

					$owner_member_view = Engine_Api::_() -> authorization() -> isAllowed($object, 'member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view');
				} else if ($objectParent instanceof User_Model_User) {

					$owner_member_view = Engine_Api::_() -> authorization() -> isAllowed($object, 'owner_member', 'view') || Engine_Api::_() -> authorization() -> isAllowed($object, 'parent_member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'owner_member', 'view') || Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'parent_member', 'view');
				}
				if ($owner_member_view) 
				{
					foreach ($aFriend as $id) {
						$event -> addResponse(array('type' => 'friend', 'identity' => $id));
					}
				}
			}
			// End friend
			
			// Start group
			if (count($aGroup)) 
			{
				foreach ($aGroup as $id) {
					$event -> addResponse(array('type' => 'group', 'identity' => $id));
				}
			}
			// End group
			
			// Only for object=event
			if (($object instanceof Ynevent_Model_Event || $object instanceof Event_Model_Event) && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
			{
				$event -> addResponse(array(
					'type' => 'event',
					'identity' => $object -> getIdentity()
				));
			}
		}
	}

	public function getActivity($event) 
	{
		$payload = $event -> getPayload();
		$user = null;
		$subject = null;
		if ($payload instanceof User_Model_User) {
			$user = $payload;
		} else if (is_array($payload)) {
			if (isset($payload['for']) && $payload['for'] instanceof User_Model_User) {
				$user = $payload['for'];
			}
			if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract) {
				$subject = $payload['about'];
			}
		}
		if (null === $user) {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if ($viewer -> getIdentity()) {
				$user = $viewer;
			}
		}
		if (null === $subject && Engine_Api::_() -> core() -> hasSubject()) {
			$subject = Engine_Api::_() -> core() -> getSubject();
		}

		// Get feed settings
		// Start Friends List
		if ($user->getIdentity()) 
		{
			$data = array();
			$data = Engine_Api::_() -> ynfeed() -> getMemberBelongFriendList();
			if (!empty($data)) {
				$event -> addResponse(array('type' => 'members_list', 'data' => $data));
			}
		}
		// End Friends List
		
		// Start Friend
		if ($user->getIdentity()) 
		{
			$event->addResponse(array(
		        'type' => 'friend',
		        'data' => $user->getIdentity()
		      ));
		}
		// End Friend
		
		// Start Group List
		if ($user) 
		{
			$data = array();
			$data = Engine_Api::_() -> ynfeed() -> getMemberBelongGroup();
			if (!empty($data)) {
				$event -> addResponse(array('type' => 'group', 'data' => $data));
			}
		}
		// End Group List
		
		// Get group memberships
		if ($user)
		{
			$data =  Engine_Api::_() -> ynfeed() ->getMemberBelongGroupOfficer();
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'officer',
					'data' => $data,
				));
			}
		}
		
		// Get business memberships
		if ($user)
		{
			$data =  Engine_Api::_() -> ynfeed() ->getMemberBelongBusinessAdmin();
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'admin',
					'data' => $data,
				));
			}
		}
		
		// Get event memberships
		if ($user)
		{
			$data =  Engine_Api::_() -> ynfeed() ->getMemberBelongEvent();
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'event',
					'data' => $data,
				));
			}
		}
	}
	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		if (!is_object($payload))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$optionFeedTable =  Engine_Api::_() -> getDbTable('optionFeeds', 'ynfeed');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		if ($payload -> getType() == 'activity_comment') // get notification when add comment
		{
			$action_id = $payload -> resource_id;
			$subject = Engine_Api::_() -> getItem('activity_action', $action_id);
			if(!$subject)
			{
				return;
			}
			$options = $optionFeedTable -> getActiveNotification($action_id);
			foreach ($options as $option) 
			{
				$user = Engine_Api::_() -> getItem('user', $option -> user_id);
				if($user && $user -> getIdentity() != $viewer -> getIdentity())
				{
			        $notifyApi->addNotification($user, $viewer, $subject, 'follow_commented', array(
			          'label' => $subject->getShortType()
			        ));
				}
			}
		}
		else if($payload -> getType() == 'activity_like') // get notification when add like
		{
			$action_id = $payload -> resource_id;
			$subject = Engine_Api::_() -> getItem('activity_action', $action_id);
			if(!$subject)
			{
				return;
			}
			$options = $optionFeedTable -> getActiveNotification($action_id);
			foreach ($options as $option) 
			{
				$user = Engine_Api::_() -> getItem('user', $option -> user_id);
				if($user && $user -> getIdentity() != $viewer -> getIdentity())
				{
			        $notifyApi->addNotification($user, $viewer, $subject, 'follow_liked', array(
			          'label' => $subject->getShortType()
			        ));
				}
			}
		}
		else if($payload -> getType() == 'activity_notification' && $payload -> object_type == 'activity_action' && in_array($payload -> type, array('liked', 'commented'))) // stop get notification
		{
			$action_id = $payload -> object_id;
			$subject = Engine_Api::_() -> getItem('activity_action', $action_id);
			if(!$subject)
			{
				return;
			}
			if($optionFeedTable -> getDeactiveNotification($subject -> getOwner(), $action_id))
			{
				$payload -> delete();
			}
		}
		
	}
}
