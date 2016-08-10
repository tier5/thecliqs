<?php
class Ynfeed_Model_DbTable_Actions extends Activity_Model_DbTable_Actions {
	protected $_name = 'activity_actions';
    protected $_rowClass = 'Ynfeed_Model_Action';

	public function addActivity(Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type, $body = null, array $params = null) {
		// Disabled or missing type
		$typeInfo = $this -> getActionType($type);
		if (!$typeInfo || !$typeInfo -> enabled) {
			return;
		}

		// User disabled publishing of this type
		$actionSettingsTable = Engine_Api::_() -> getDbtable('actionSettings', 'activity');
		if (!$actionSettingsTable -> checkEnabledAction($subject, $type)) {
			return;
		}

		// Create action
		$action = $this -> createRow();
		$action -> setFromArray(array('type' => $type, 'subject_type' => $subject -> getType(), 'subject_id' => $subject -> getIdentity(), 'object_type' => $object -> getType(), 'object_id' => $object -> getIdentity(), 'body' => (string)$body, 'params' => (array)$params, 'date' => date('Y-m-d H:i:s')));
		$action -> save();

		// Add bindings
		$this -> addActivityBindings($action, $type, $subject, $object);

		// We want to update the subject
		if (isset($subject -> modified_date)) {
			$subject -> modified_date = date('Y-m-d H:i:s');
			$subject -> save();
		}

		return $action;
	}

	public function editActivity(Core_Model_Item_Abstract $action, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type, $body = null, array $params = null) 
	{
		// Disabled or missing type
		$typeInfo = $this -> getActionType($type);
		if (!$typeInfo || !$typeInfo -> enabled) {
			return;
		}

		// User disabled publishing of this type
		$actionSettingsTable = Engine_Api::_() -> getDbtable('actionSettings', 'activity');
		if (!$actionSettingsTable -> checkEnabledAction($subject, $type)) {
			return;
		}
		// Edit action
		$action -> body = $body;
		$action -> params = $params;
		$action -> save();
		
		// Remove all privacies
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$action_id = $action -> getIdentity();
		$streamTable -> delete("action_id = {$action_id}");
		
		// Add bindings
		$this -> addActivityBindings($action, $type, $subject, $object);

		// We want to update the subject
		if (isset($subject -> modified_date)) {
			$subject -> modified_date = date('Y-m-d H:i:s');
			$subject -> save();
		}

		return $action;
	}

	public function addActivityBindings($action) 
	{
		$privacies = array();
		if(isset($action->params['privacies']))
		{
			$privacies = $action->params['privacies'];
		}
		$aGeneral = array();
		if(isset($privacies['general']))
		{
			$sGeneral = $privacies['general'];
			if($sGeneral)
			{
				$aGeneral = explode(',', $sGeneral);
			}
		}
		
		$aNetwork = array();
		if(isset($privacies['network']))
		{
			$sNetwork = $privacies['network'];
			if($sNetwork)
			{
				$aNetwork = explode(',', $sNetwork);
			}
		}
		
		$aFriendlist = array();
		if(isset($privacies['friend_list']))
		{
			$sFriendlist = $privacies['friend_list'];
			if($sFriendlist)
			{
				$aFriendlist = explode(',', $sFriendlist);
			}
		}
		
		$aFriend = array();
		if(isset($privacies['friend']))
		{
			$sFriend = $privacies['friend'];
			if($sFriend)
			{
				$aFriend = explode(',', $sFriend);
			}
		}
		
		$aGroup = array();
		if(isset($privacies['group']))
		{
			$sGroup = $privacies['group'];
			if($sGroup)
			{
				$aGroup = explode(',', $sGroup);
			}
		}
		
		$general = Engine_Api::_() -> ynfeed() -> getMaxGeneralPrivacy($aGeneral, $action -> getObject() -> getType());
		
		$notInclude = false;
		// not include if not network_list and only me
	    if (!count($aNetwork)
		&& !in_array($general, array('everyone', 'network', 'member')))
		{
	    	$notInclude = true;
	    }
		// Get privacy bindings
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('addActivity', array('subject' => $action -> getSubject(), 'object' => $action -> getObject(), 'type' => $action -> type, 'privacies' => $privacies));
		
		$hasAddNetworkPrivacy = false;
		$hasAddUserPrivacy = false;
		$hasAddGroupPrivacy = false;
		
		// Add privacy bindings
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$include_type = array();
		
		foreach ((array) $event->getResponses() as $response) 
		{
			if (isset($response['target'])) {
				$target_type = $response['target'];
				$target_id = 0;
			} else if (isset($response['type']) && isset($response['identity'])) 
			{
				$target_type = $response['type'];
				$target_id = $response['identity'];
			} else {
				continue;
			}
			
			$objType= $action -> getObject() -> getType();
			if($objType == 'user')
			{
				$array_temp = array('network_list', 'members_list', 'friend', 'group', 'owner', 'parent');
				if(!empty($general))
				{
					if ($general == 'member' && !in_array($target_type, array('network_list', 'members_list', 'friend', 'group', 'owner', 'parent', 'members'))) 
					{
				        continue;
				    }
				
				    if ($general == 'network' && !in_array($target_type, array('members_list', 'friend', 'group', 'owner', 'parent', 'members', 'network'))) 
				    {
				        continue;
				    }
					
					if ($notInclude && !in_array($target_type, array('members_list', 'friend', 'group', 'owner', 'parent'))) 
					{
				        continue;
				    }
				}
				else 
				{
					if (count($aFriendlist) && !in_array($target_type, $array_temp)) 
					{
				        continue;
				    } 
					
					if (count($aFriend) && !in_array($target_type, $array_temp)) 
					{
				        continue;
				    } 
					
					if (count($aGroup) && !in_array($target_type, $array_temp)) 
					{
				        continue;
				    } 
				}
				
				if (count($aNetwork) && !in_array($target_type, $array_temp) && empty($general)) 
				{
			        continue;
			    } 
			    elseif ($target_type == 'network_list') 
			    {
			        $target_type = 'network';
			    }
				
				if (count($aNetwork) && in_array($target_type, array('everyone', 'registered', 'network', 'members')) && empty($general)) 
				{
			        $hasAddNetworkPrivacy = true;
			        continue;
			    }
			}
			elseif($objType == 'group')
			{
				if(!empty($general))
				{
					if ($general == 'member' && !in_array($target_type, array( 'owner', 'parent', 'group'))) 
					{
				        continue;
				    }
				
				    if ($general == 'officer' && !in_array($target_type, array('owner', 'parent', 'officer'))) 
				    {
				        continue;
				    }
					
					if ($general == 'owner' && !in_array($target_type, array('owner', 'parent'))) 
				    {
				        continue;
				    }
				}
			}
			elseif($objType == 'ynbusinesspages_business')
			{
				if(!empty($general))
				{
					if ($general == 'member' && !in_array($target_type, array( 'owner', 'parent', 'business'))) 
					{
				        continue;
				    }
				
				    if ($general == 'admin' && !in_array($target_type, array('owner', 'parent', 'admin'))) 
				    {
				        continue;
				    }
					
					if ($general == 'owner' && !in_array($target_type, array('owner', 'parent'))) 
				    {
				        continue;
				    }
				}
			}
			elseif($objType == 'event')
			{
				if(!empty($general))
				{
					if ($general == 'member' && !in_array($target_type, array('owner', 'parent', 'event'))) 
					{
				        continue;
				    }
				
				   if ($general == 'owner' && !in_array($target_type, array('owner', 'parent'))) 
				    {
				        continue;
				    }
				 }
			}

			if (isset($include_type[$target_type]) && in_array($target_id, $include_type[$target_type]))
			{
		        continue;
		    }
			$include_type[$target_type][] = $target_id;
			$streamTable -> insert(array('action_id' => $action -> action_id, 'type' => $action -> type, 'target_type' => (string)$target_type, 'target_id' => (int)$target_id, 'subject_type' => $action -> subject_type, 'subject_id' => $action -> subject_id, 'object_type' => $action -> object_type, 'object_id' => $action -> object_id, ));
		}
		if ($hasAddNetworkPrivacy) 
		{
	      $target_type = 'network';
	      foreach ($aNetwork as $target_id) 
	      {
	        if (isset($include_type[$target_type]) && in_array($target_id, $include_type[$target_type])) 
	        {
	          continue;
	        }
	        $include_type[$target_type][] = $target_id;
	        $streamTable->insert(array(
	            'action_id' => $action->action_id,
	            'type' => $action->type,
	            'target_type' => 'network',
	            'target_id' => (int) $target_id,
	            'subject_type' => $action->subject_type,
	            'subject_id' => $action->subject_id,
	            'object_type' => $action->object_type,
	            'object_id' => $action->object_id,
	        ));
	      }
	    }
		return $this;
	}

	public function getActivity(User_Model_User $user, array $params = array()) {
		// Proc args
		extract($this -> _getInfo($params));
		// action_id, limit, min_id, max_id, actionFilter, filterValue
		// Prepare main query
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$streamName = $streamTable -> info('name');
		$actionTableName = $this -> info('name');

		$db = $streamTable -> getAdapter();
		$union = new Zend_Db_Select($db);

		// Prepare action types
		$masterActionTypes = Engine_Api::_() -> getDbtable('actionTypes', 'activity') -> getActionTypes();
		$mainActionTypes = array();

		// Filter out types set as not displayable
		foreach ($masterActionTypes as $type) {
			if ($type -> displayable & 4) {
				$mainActionTypes[] = $type -> type;
			}
		}
		$showPost = in_array("post", $mainActionTypes);
		// Filter types based on user request
		if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
			$mainActionTypes = array_intersect($mainActionTypes, $showTypes);
		} else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
			$mainActionTypes = array_diff($mainActionTypes, $hideTypes);
		}

		$mainActionTypesArray = $mainActionTypes;

		// Nothing to show
		if (empty($mainActionTypes)) {
			return null;
		}
		// Show everything
		else if (count($mainActionTypes) == count($masterActionTypes)) {
			$mainActionTypes = true;
		}
		// Build where clause
		else {
			$mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
		}
		// Prepare sub queries
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('getActivity', array('for' => $user, ));
		$responses = (array)$event -> getResponses();
		if (empty($responses)) {
			return null;
		}

		$friendsFlage = false;
		$action_ids = array();
		// Saved feeds
		if ($actionFilter == 'user_saved') {
			$action_ids = Engine_Api::_() -> getDbtable('saveFeeds', 'ynfeed') -> getSaveFeeds($user, $mainActionTypesArray, array('limit' => $limit, 'max_id' => $max_id));
			if (empty($action_ids))
				return null;
		// Hashtag feeds
		} elseif ($actionFilter == 'hashtag' && !empty($filterValue)) {
			$action_ids = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed') -> getHashtagFeeds($filterValue, $mainActionTypesArray, array('limit' => $limit, 'max_id' => $max_id));
			if (empty($action_ids))
				return null;
		} elseif ($actionFilter == 'user_follow' && !empty($filterValue)) 
		{
			$action_ids = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed') -> getNotificationFeeds($user, $mainActionTypesArray, array('limit' => $limit, 'max_id' => $max_id));
			if (empty($action_ids))
				return null;
		}
		// Following feeds
		
		foreach ($responses as $response) 
		{
			if (empty($response))
				continue;
			
		    if (in_array($actionFilter, array('membership', 'member_list')) && !in_array($response['type'], array('members', 'members_list'))) 
		    {
		        continue;
		    } 
		    elseif (in_array($actionFilter, array('membership', 'member_list'))) 
		    {
		        if ($response['type'] == 'members') 
		        {
		           $friendsFlage = true;
		        }
		    }
			
			if (in_array($actionFilter, array('network_list')) && !in_array($response['type'], array('network'))) 
			{
		        continue;
		    }
			if ($actionFilter == 'network_list' && !empty($listTypeFilter) && in_array($response['type'], array('network'))) 
			{
		        $response['data'] = $listTypeFilter;
		    }
		    if ($actionFilter == 'member_list' && !empty($listTypeFilter)) 
		    {
		        if ($response['type'] == 'members') 
		        {
		        	$response['data'] = $listTypeFilter['member_list']['value'];
		        } 
		        elseif ($response['type'] == 'members_list') 
		        {
		        	$response['data'] = $listTypeFilter['member_list']['list_ids'];
		        }
		    }
			$select = $streamTable -> select() -> from($streamTable -> info('name'), 'action_id') 
				-> where('target_type = ?', $response['type']);

			if (empty($response['data'])) {
				// Simple
				$select -> where('target_id = ?', 0);
			} else if (is_scalar($response['data']) || count($response['data']) === 1) {
				// Single
				if (is_array($response['data'])) {
					list($response['data']) = $response['data'];
				}
				$select -> where('target_id = ?', $response['data']);
			} else if (is_array($response['data'])) {
				// Array
				$select -> where('target_id IN(?)', (array)$response['data']);
			} else {
				// Unknown
				continue;
			}

			// Add action_id/max_id/min_id
			if (null !== $action_id) {
				$select -> where('action_id = ?', $action_id);
			} else {
				if (null !== $min_id) {
					$select -> where('action_id >= ?', $min_id);
				} else if (null !== $max_id) {
					$select -> where('action_id <= ?', $max_id);
				}
			}
		
			if ($mainActionTypes !== true) 
			{
        		if ($showPost && !empty($actionFilter) && !in_array($actionFilter, array('all', 'members', 'members_list', 'custom_list', 'posts', 'facebook_feeds', 'linkedin_feeds', 'twitter_feeds'))) 
        		{
            		$object_type = $actionFilter;
          			$select->where('(' . $streamName . '.type IN(' . $mainActionTypes . ') OR (' . $streamName . '.type = "post" and ' . $streamName . '.object_type ="' . $object_type . '") )');
        		} 
        		else 
        		{
         			 $select->where('' . $streamName . '.type IN(' . $mainActionTypes . ')');
        		}
      		}
			
			if ($actionFilter == 'member_list' && !empty($listTypeFilter) && in_array($response['type'], array('members_list'))) 
			{
		        $select->where('' . $streamName . '.type IN(' . $mainActionTypes . ')');
		    }

			// Add order/limit
			$select -> order('action_id DESC') -> limit($limit);

			// Saved feed filter
			if (!empty($action_ids)) 
			{
				$select -> where($streamName . '.action_id IN(?)', (array)$action_ids);
			}
			
			// Friend filter
			if (in_array($actionFilter, array('membership', 'member_list')) && in_array($response['type'], array('members'))) 
			{
		        $ids = $user->membership()->getMembershipsOfIds();
		        if (!empty($ids)) 
		        {
		          $select
		              -> where($streamName . '.subject_type = ?', 'user')
		              -> where($streamName . '.subject_id IN (?)', (array) $ids);
		        }
			}
			
			if ($actionFilter == 'custom_list' && !empty($listTypeFilter)) 
			{
		        foreach ($listTypeFilter as $resource) 
		        {
		          $selectSubject = clone $select;
		          // Add subject to main query
		          $selectSubject
		                  ->where($streamName . '.subject_type = ?', $resource->child_type)
		                  ->where($streamName . '.subject_id = ?', $resource->child_id);
						  
		          $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
		          // Add object to main query
		          $selectObject = clone $select;
		
		          $selectObject
		                  ->where($streamName . '.object_type = ?', $resource->child_type)
		                  ->where($streamName . '.object_id = ?', $resource->child_id);
						  
		          $union->union(array('(' . $selectObject->__toString() . ')')); // (string) not work before PHP 5.2.0
		        }
		    } 
		    else 
		    {
		        // Add to main query
		        $union->union(array('(' . $select->__toString() . ')')); // (string) not work before PHP 5.2.0
		    }
		}

		// Finish main query
		$union -> order('action_id DESC') -> limit($limit);
		
		if (in_array($actionFilter, array('membership', 'member_list')) && !$friendsFlage) 
		{
	    	return null;
	    }

		// Get actions
		$actions = $db -> fetchAll($union);
		
		// Process ids
		$ids = array();
		if (in_array($actionFilter, array('all', 'posts'))) {
			$ids = Engine_Api::_() -> ynfeed() -> getTaggedBaseActionIds($user, array('min' => $min_id, 'max' => $max_id));
		}
		// No visible actions
		if (empty($actions) && empty($ids)) {
			return null;
		}

		// Process ids
		foreach ($actions as $data) {
			$ids[] = $data['action_id'];
		}
		$ids = array_unique($ids);
		// Finally get activity
		return $this -> fetchAll($this -> select() -> where('action_id IN(' . join(',', $ids) . ')') -> order('action_id DESC') -> limit($limit));
	}

	public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user, array $params = array()) {
		// Proc args
		extract($this -> _getInfo($params));
		// action_id, limit, min_id, max_id, actionFilter, filterValue

		// Prepare main query
		$streamTable = Engine_Api::_() -> getDbtable('stream', 'activity');
		$streamName = $streamTable -> info('name');
		$actionTableName = $this -> info('name');
		$db = $streamTable -> getAdapter();
		$union = new Zend_Db_Select($db);

		// Prepare action types
		$masterActionTypes = Engine_Api::_() -> getDbtable('actionTypes', 'activity') -> getActionTypes();
		$subjectActionTypes = array();
		$objectActionTypes = array();

		// Filter types based on displayable
		foreach ($masterActionTypes as $type) {
			if (($about -> getType() == 'event' && Engine_Api::_() -> hasItemType('event')) || ($about -> getType() == 'group' && Engine_Api::_() -> hasItemType('group')) || ($about -> getType() == 'ynbusinesspages_business' && Engine_Api::_() -> hasItemType('ynbusinesspages_business'))) {
				if ($actionFilter == 'owner' && isset($type -> is_object_thumb) && !$type -> is_object_thumb)
					continue;
				if ($actionFilter == 'membership' && isset($type -> is_object_thumb) && $type -> is_object_thumb)
					continue;
			}
			if ($type -> displayable & 1) {
				$subjectActionTypes[] = $type -> type;
			}
			if ($type -> displayable & 2) {
				$objectActionTypes[] = $type -> type;
			}
		}

		// Filter types based on user request
		if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
			$subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
			$objectActionTypes = array_intersect($objectActionTypes, $showTypes);
		} else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
			$subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
			$objectActionTypes = array_diff($objectActionTypes, $hideTypes);
		}

		// Nothing to show
		if (empty($subjectActionTypes) && empty($objectActionTypes)) {
			return null;
		}

		if (empty($subjectActionTypes)) {
			$subjectActionTypes = null;
		} else if (count($subjectActionTypes) == count($masterActionTypes)) {
			$subjectActionTypes = true;
		} else {
			$subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
		}

		if (empty($objectActionTypes)) {
			$objectActionTypes = null;
		} else if (count($objectActionTypes) == count($masterActionTypes)) {
			$objectActionTypes = true;
		} else {
			$objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
		}

		// Prepare sub queries
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('getActivity', array('for' => $user, 'about' => $about, ));
		$responses = (array)$event -> getResponses();

		if (empty($responses)) {
			return null;
		}

		$friendsFlage = false;
		$action_ids = array();
		
		// Filter by hashtag
		if ($actionFilter == 'hashtag' && !empty($filterValue)) 
		{
			$action_ids = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed') -> getHashtagFeeds($filterValue, array() ,array('limit' => $limit, 'max_id' => $max_id));
			if (empty($action_ids))
			{
				return null;
			}
		}
		
		// Filter by login as business post
		if ($actionFilter == 'business') 
		{
			$select = $this -> select() -> where('`subject_type` = ?', 'ynbusinesspages_business') -> where('subject_id', $about -> getIdentity()) -> limit($limit);
			if (null !== $max_id) 
			{
				$select -> where('action_id <= ?', $max_id);
			}
			$data = $select -> query() -> fetchAll();
			foreach ($data as $row) 
			{
				$action_ids[] = $row['action_id'];
			}
			if (empty($action_ids))
			{
				return null;
			}
		}
		
		$member_ids = array();
		if ($actionFilter == 'owner') {
			if ($about instanceof User_Model_User) {
				$member_ids[] = $about -> getIdentity();
			} elseif ($about instanceof Group_Model_Group || $about instanceof Advgroup_Model_Group)// Group & Adv Group
			{
				$objectParent = $about -> getParent('user');
				if ($objectParent instanceof User_Model_User) {
					$member_ids[] = $objectParent -> getIdentity();
				}
			} else// Event & Adv Event & other plugin
			{
				$objectParent = $about -> getParent('user');
				if ($objectParent instanceof User_Model_User) {
					$member_ids[] = $objectParent -> getIdentity();
				}
			}
		} elseif ($actionFilter == 'officers') {
			if ($about instanceof Group_Model_Group || $about instanceof Advgroup_Model_Group)// Group & Adv Group
			{
				$objectParent = $about -> getParent('user');
				if ($objectParent instanceof User_Model_User) {
					$member_ids[] = $objectParent -> getIdentity();
				}
				foreach ($about->getOfficerList()->getAll() as $value) {
					$member_ids[] = $value -> child_id;
				}
			}
		}
		elseif ($actionFilter == 'admins') {
			if ($about instanceof Ynbusinesspages_Model_Business)// Business
			{
				$objectParent = $about -> getParent('user');
				if ($objectParent instanceof User_Model_User) {
					$member_ids[] = $objectParent -> getIdentity();
				}
				foreach ($about->getAdminList()->getAll() as $value) {
					$member_ids[] = $value -> child_id;
				}
			}
		} 
		elseif ($actionFilter == 'membership') 
		{
			if (in_array($about -> getType(), array('event', 'group', 'ynbusinesspages_business')))
			{
				$members = $about -> membership() -> getMembers(true);
				foreach ($members as $member) {
					$member_ids[] = $member -> getIdentity();
				}
			} else {
				$member_ids = $user -> membership() -> getMembershipsOfIds();
			}
			if (empty($member_ids))
				return array();
		}
		
		foreach ($responses as $response) {
			if (empty($response))
				continue;

			// Target info
			$select = $streamTable -> select() -> from($streamTable -> info('name'), 'action_id') -> where('target_type = ?', $response['type']);

			if (empty($response['data'])) {
				// Simple
				$select -> where('target_id = ?', 0);
			} else if (is_scalar($response['data']) || count($response['data']) === 1) {
				// Single
				if (is_array($response['data'])) {
					list($response['data']) = $response['data'];
				}
				$select -> where('target_id = ?', $response['data']);
			} else if (is_array($response['data'])) {
				// Array
				$select -> where('target_id IN(?)', (array)$response['data']);
			} else {
				// Unknown
				continue;
			}

			// Add action_id/max_id/min_id
			if (null !== $action_id) {
				$select -> where('action_id = ?', $action_id);
			} else {
				if (null !== $min_id) {
					$select -> where('action_id >= ?', $min_id);
				} else if (null !== $max_id) {
					$select -> where('action_id <= ?', $max_id);
				}
			}

			// Add order/limit
			$select -> order('action_id DESC') -> limit($limit);

			if (!empty($action_ids)) {
				$select -> where($streamName . '.action_id IN(?)', (array)$action_ids);
			}

			// Add subject to main query
			$selectSubject = clone $select;
			if ($subjectActionTypes !== null) {
				if ($subjectActionTypes !== true) {
					$selectSubject -> where('type IN(' . $subjectActionTypes . ')');
				}
				$selectSubject -> where('subject_type = ?', $about -> getType()) -> where('subject_id = ?', $about -> getIdentity());

				if (!empty($member_ids)) {
					$selectSubject -> where('object_type = ?', 'user') -> where('object_id  IN (?)', (array)$member_ids);
				}

				$union -> union(array('(' . $selectSubject -> __toString() . ')'));
			}

			// Add object to main query
			$selectObject = clone $select;
			if ($objectActionTypes !== null) {
				if ($objectActionTypes !== true) {
					$selectObject -> where('type IN(' . $objectActionTypes . ')');
				}
				$selectObject -> where('object_type = ?', $about -> getType()) -> where('object_id = ?', $about -> getIdentity());

				if (!empty($member_ids)) {
					$selectObject -> where('subject_type = ?', 'user') -> where('subject_id IN (?)', (array)$member_ids);
				}
				$union -> union(array('(' . $selectObject -> __toString() . ')'));
				// (string) not work before PHP 5.2.0
			}
		}

		// Finish main query
		$union -> order('action_id DESC') -> limit($limit);

		// Get actions
		$actions = $db -> fetchAll($union);
		// Process ids
		$ids = array();
		if (in_array($actionFilter, array('all', 'posts')) && $action_id) 
		{
			$tag_ids = Engine_Api::_() -> ynfeed() -> getTaggedBaseActionIds($user, array('min' => $min_id, 'max' => $max_id));
			if(in_array($action_id, $tag_ids) )
			{
				$ids[] = $action_id;
			}
		}
		// No visible actions
		if (empty($actions) && empty($ids)) {
			return null;
		}

		// Process ids
		foreach ($actions as $data) {
			$ids[] = $data['action_id'];
		}
		$ids = array_unique($ids);

		// Finally get activity
		return $this -> fetchAll($this -> select() -> where('action_id IN(' . join(',', $ids) . ')') -> order('action_id DESC') -> limit($limit));
	}

	protected function _getInfo(array $params) {
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$args = array('limit' => $settings -> getSetting('activity.length', 20), 'action_id' => null, 'max_id' => null, 'min_id' => null, 'showTypes' => null, 'hideTypes' => null, );

		$newParams = array();
		foreach ($args as $arg => $default) {
			if (!empty($params[$arg])) {
				$newParams[$arg] = $params[$arg];
			} else {
				$newParams[$arg] = $default;
			}
			if (isset($params[$arg]))
				unset($params[$arg]);
		}
		$newParams = array_merge($newParams, $params);
		return $newParams;
	}

}
?>