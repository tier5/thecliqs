<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynfeed
 * @copyright  Copyright 2014 YouNet Company
 * @author     YouNet Company
 */

class Ynfeed_Api_Core extends Core_Api_Abstract {
	public function getEmoticons() {
		$table = Engine_Api::_() -> getDbTable("emoticons", "ynfeed");
		return $table -> fetchAll($table -> select());
	}
	
	public function getEmoticonByImg($img) {
		$table = Engine_Api::_() -> getDbTable("emoticons", "ynfeed");
		return $table -> fetchRow($table -> select()-> where('image = ?', $img) -> limit(1));
	}

	public function getCheckin($action_id) {
		$map_table = Engine_Api::_() -> getDbTable('maps', 'ynfeed');
		$select = $map_table -> select() -> where('action_id = ?', $action_id);
		$map = $map_table -> fetchRow($select);
		return $map;
	}

	public function getWithFriends($action_id, $friend_id = 0) {
		$tagfriend_table = Engine_Api::_() -> getDbTable('tagfriends', 'ynfeed');
		$select = $tagfriend_table -> select() -> where('action_id = ?', $action_id);
		if ($friend_id) {
			$select -> where('friend_id <> ?', $friend_id);
		}
		$friends = $tagfriend_table -> fetchAll($select);
		$friendIds = array();
		$friendObjs = array();
		foreach ($friends as $friend) {
			$friendIds[] = $friend -> friend_id;
		}
		$friendObjs = Engine_Api::_() -> user() -> getUserMulti($friendIds);
		return array($friendIds, $friendObjs);
	}

	public function removeTag($action_id, $friend_id) {
		// remove from tagfriend
		$tagfriend_table = Engine_Api::_() -> getDbTable('tagfriends', 'ynfeed');
		$select = $tagfriend_table -> select() -> where('action_id = ?', $action_id);
		$select -> where('friend_id = ?', $friend_id);
		$friend = $tagfriend_table -> fetchRow($select);
		if ($friend) {
			$friend -> delete();
		}
	}

	public function getViewerFriends($viewer) {
		try {
			$table = Engine_Api::_() -> getItemTable('user');
			$select = $viewer -> membership() -> getMembersObjectSelect() -> limit(1000);
			$friendIds = array();
			foreach ($table -> fetchAll($select ) as $friend) {
				$friendIds[] = array('id' => $friend -> getIdentity());
			}
			return $friendIds;
		} catch (Exception $e) {
		}
	}

	public function getShareds($action_id, $type, $id) {
		try {
			$table = Engine_Api::_() -> getDbTable('attachments', 'activity');
			if ($type && $id) {
				$select = $table -> select() -> where("type = ?", $type) -> where("id = ?", $id) -> where("action_id <> ?", $action_id);
			} else {
				$select = $table -> select() -> where("type = 'activity_action'") -> where("id = ?", $action_id);
			}
			$sharedActions = $table -> fetchAll($select -> order("action_id DESC"));
			$arr_result = array();
			foreach ($sharedActions as $sharedAction) 
			{
				$action = Engine_Api::_() -> getItem('activity_action', $sharedAction -> action_id);
				if($action -> type == 'share')
				{
					$arr_result[] = $action;
				}
			}
			return $arr_result;
		} catch (Exception $e) {
		}
	}

	public function getTaggedBaseActionIds($user, $params = array()) {

		$memberIds = $user -> membership() -> getMembershipsOfIds();
		$memberIds[] = $user -> getIdentity();
		$table = Engine_Api::_() -> getDbtable('tags', 'ynfeed');
		$select = $table -> select() -> from($table -> info('name'), "action_id") -> where('item_type = ?', 'user') -> where('item_id in(?)', (array)$memberIds);
		if (!empty($params['min'])) {
			$select -> where('action_id >= ?', $params['min']);
		} else if (!empty($params['max'])) {
			$select -> where('action_id <= ?', $params['max']);
		}

		$actions = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);

		$table = Engine_Api::_() -> getDbtable('tagfriends', 'ynfeed');
		$select = $table -> select() -> from($table -> info('name'), "action_id") -> where('friend_id in(?)', (array)$memberIds);
		if (!empty($params['min'])) {
			$select -> where('action_id >= ?', $params['min']);
		} else if (!empty($params['max'])) {
			$select -> where('action_id <= ?', $params['max']);
		}

		$withFriends = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);

		return array_merge($actions, $withFriends);
	}

	public function getMemberBelongFriendList($params = array()) {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if (empty($viewer_id))
			return null;

		$listTable = Engine_Api::_() -> getItemTable('user_list');
		$listTableName = $listTable -> info('name');

		$listItemTable = Engine_Api::_() -> getItemTable('user_list_item');
		$listItemTableName = $listItemTable -> info('name');
		$select = $listItemTable -> select() -> setIntegrityCheck(false) -> from($listItemTableName, "$listItemTableName.list_id") -> join($listTableName, "$listTableName.list_id = $listItemTableName.list_id", null) -> where('child_id = ?', $viewer_id);
		if (isset($params['owner_ids']) && !empty($params['owner_ids']))
			$select -> where('owner_id  IN(?)', (array)$params['owner_ids']);
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	}

	public function getMemberBelongGroup($params = array()) { 
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if (empty($viewer_id) || !Engine_Api::_() -> hasItemType('group'))
			return null;
		$memTable = NULL;
		if (Engine_Api::_() -> hasModuleBootstrap('advgroup')) {
			$memTable = Engine_Api::_() -> getDbTable('membership', 'advgroup');
		} else {
			$memTable = Engine_Api::_() -> getDbTable('membership', 'group');
		}
		if (!$memTable) {
			return NULL;
		}
		$select = $memTable -> select() -> from($memTable -> info('name'), 'resource_id') -> where('user_id = ?', $viewer_id) -> where('active = 1');
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	}

	public function getMemberBelongGroupOfficer($params = array()) {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if (empty($viewer_id) || !Engine_Api::_() -> hasItemType('group'))
			return null;

		$listTable = $listItemTable = NULL;
		if (Engine_Api::_() -> hasModuleBootstrap('advgroup')) {
			$listTable = Engine_Api::_() -> getItemTable('advgroup_list');
			$listItemTable = Engine_Api::_() -> getItemTable('advgroup_list_item');
		} else {
			$listTable = Engine_Api::_() -> getItemTable('group_list');
			$listItemTable = Engine_Api::_() -> getItemTable('group_list_item');
		}
		if (!$listItemTable || !$listTable) {
			return null;
		}

		$listTableName = $listTable -> info('name');
		$listItemTableName = $listItemTable -> info('name');
		$select = $listItemTable -> select() -> setIntegrityCheck(false) -> from($listItemTableName, "$listItemTableName.list_id") -> join($listTableName, "$listTableName.list_id = $listItemTableName.list_id", null) -> where('child_id = ?', $viewer_id) -> where("$listTableName.title = ?", 'GROUP_OFFICERS');
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	}

	public function getMemberBelongBusinessAdmin($params = array()) {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if (empty($viewer_id) || !Engine_Api::_() -> hasItemType('ynbusinesspages_business'))
			return null;

		$listTable = Engine_Api::_() -> getItemTable('ynbusinesspages_list');
		$listItemTable = Engine_Api::_() -> getItemTable('ynbusinesspages_list_item');

		$listTableName = $listTable -> info('name');
		$listItemTableName = $listItemTable -> info('name');
		$select = $listItemTable -> select() -> setIntegrityCheck(false) -> from($listItemTableName, "$listItemTableName.list_id") -> join($listTableName, "$listTableName.list_id = $listItemTableName.list_id", null) -> where('child_id = ?', $viewer_id) -> where("$listTableName.type = ?", 'admin');
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	}

	public function getMemberBelongEvent($params = array()) {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if (empty($viewer_id) || !Engine_Api::_() -> hasItemType('event'))
			return null;
		$memTable = NULL;
		if (Engine_Api::_() -> hasModuleBootstrap('ynevent')) {
			$memTable = Engine_Api::_() -> getDbTable('membership', 'ynevent');
		} else {
			$memTable = Engine_Api::_() -> getDbTable('membership', 'event');
		}
		if (!$memTable) {
			return NULL;
		}
		$select = $memTable -> select() -> from($memTable -> info('name'), 'resource_id') -> where('user_id = ?', $viewer_id) -> where('active = 1');
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN); 
	}

	public function getMaxGeneralPrivacy($aGeneral, $type) {
		$roles = array('owner', 'member', 'network', 'registered', 'everyone');
		// check $type
		switch ($type) {
			case 'group' :
				$roles = array('owner', 'officer', 'member', 'registered', 'everyone');
				break;
			
			case 'ynbusinesspages_business' :
				$roles = array('owner', 'admin', 'member', 'registered', 'everyone');
				break;

			case 'event' :
				$roles = array('owner', 'member', 'registered', 'everyone');
				break;
		}

		$viewMax = -1;
		foreach ($aGeneral as $auth_view) {
			if (array_search($auth_view, $roles) > $viewMax)
				$viewMax = array_search($auth_view, $roles);
		}
		if ($viewMax > -1)
			return $roles[$viewMax];
		return NULL;
	}

	public function getNetworks($type, $viewer) {
		$ids = array();
		$viewer_id = $viewer -> getIdentity();
		if (empty($type) || empty($viewer_id)) {
			return;
		}
		$networkTable = Engine_Api::_() -> getDbtable('membership', 'network');
		$ids = $networkTable -> getMembershipsOfIds($viewer);
		$ids = array_unique($ids);
		$count = count($ids);
		if (empty($count))
			return;

		$table = Engine_Api::_() -> getItemTable('network');
		$select = $table -> select() -> order('title ASC');
		if ($type == 1 && !empty($ids)) {
			$select -> where('network_id IN(?)', $ids);
		}
		return $table -> fetchAll($select);
	}

	public function getListFriendIds($listId) {
		$listTable = Engine_Api::_() -> getItemTable('user_list');
		$listTableName = $listTable -> info('name');

		$listItemTable = Engine_Api::_() -> getItemTable('user_list_item');
		$listItemTableName = $listItemTable -> info('name');
		return $listItemTable -> select() -> setIntegrityCheck(false) 
			-> from($listItemTableName, "$listItemTableName.child_id") 
			-> join($listTableName, "$listTableName.list_id = $listItemTableName.list_id", null) 
			-> where($listTableName . '.list_id = ?', $listId) -> query() 
			-> fetchAll(Zend_Db::FETCH_COLUMN);
	}

	public function getListBaseContent($type, $params = array()) {
		$list = array();
		if (!isset($params['filterValue']) || empty($params['filterValue']))
			return;

		if ($type == 'member_list') {
			$list['member_list']['value'] = $listFirendIds = $this -> getListFriendIds($params['filterValue']);
			$list['member_list']['list_ids'] = !empty($listFirendIds) ? $this -> getMemberBelongFriendList(array("owner_ids" => $listFirendIds)) : 0;
		} 
		else if ($type == 'custom_list') 
		{
			$custom_list = Engine_Api::_() -> getItem('ynfeed_list', $params['filterValue']);
			if($custom_list)
			{
				if ($custom_list -> count()) {
					$list = $custom_list -> getListItems();
				}
			}
		} 
		return $list;
	}
	
	public function getGeneralPrivacyName($subjectType, $key)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if($subjectType == 'user')
		{
			switch ($key) {
				case 'everyone':
					return $translate -> translate("Everyone");
					break;
				
				case 'network':
					return $translate -> translate("Friends & Networks");
					break;
				case 'member':
					return $translate -> translate("Friends Only");
					break;
				case 'owner':
					return $translate -> translate("Only Me");
					break;
			}
		}
		elseif($subjectType == 'group')
		{
			switch ($key) {
				case 'everyone':
					return $translate -> translate("Everyone");
					break;
				
				case 'member':
					return $translate -> translate("All Group Members");
					break;
				case 'officer':
					return $translate -> translate("Officers and Owner Only");
					break;
				case 'owner':
					return $translate -> translate("Owner Only");
			}
		}
		elseif($subjectType == 'ynbusinesspages_business')
		{
			switch ($key) {
				case 'everyone':
					return $translate -> translate("Everyone");
					break;
				
				case 'member':
					return $translate -> translate("All Business Members");
					break;
				case 'admin':
					return $translate -> translate("Admins and Owner Only");
					break;
				case 'owner':
					return $translate -> translate("Owner Only");
			}
		}
		elseif($subjectType == 'event') 
		{
			switch ($key) 
			{
				case 'everyone':
					return $translate -> translate("Everyone");
					break;
				case 'member':
					return $translate -> translate("Event Guests Only");
					break;
				case 'owner':
					return $translate -> translate("Owner Only");
			}
		}
		
	}

	public function getObjectTitle($type, $identity)
	{
		$object = Engine_Api::_() -> getItem($type, $identity);
		if($object)
		{
			return $object -> getTitle();
		}
		return "";
	}
	
	public function getFriendRequests($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
		$select = $userTb -> select() 
			-> where("user_id = ?", $viewer -> getIdentity()) 
			-> where("active = 0 AND resource_approved = 1 AND user_approved = 0") -> limit($params['limit']);
		
		return $userTb -> fetchAll($select);
	}
	
	public function getMemberSuggestions($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		$friends = $viewer -> membership() -> getMembershipsOfIds();
		$friends[] = $viewer -> getIdentity();
		
		$userTb = Engine_Api::_() -> getItemTable('user');
		$userName = $userTb->info('name');
		$friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
	    $friendsName = $friendsTable->info('name');
		
		$sql = "SELECT count(*) FROM `{$friendsName}` WHERE (`{$friendsName}`.`active`= 1 and `{$friendsName}`.`resource_id` = `{$userName}`.`user_id`)
        and `user_id` in (select `resource_id` from `{$friendsName}` where (`user_id`= {$viewer_id} and `active`= 1))";
		
		$select = $userTb -> select() -> from($userName, array("$userName.*", new Zend_Db_Expr("({$sql}) AS count")));
		if($friends)	 
			$select -> where("$userName.user_id NOT IN (?)", $friends);
		$select -> order('count DESC') -> order("rand()");
	    $limit = 4;
	    if(isset($params['limit']))
		{
			$select -> limit($params['limit'] * 2);
			$limit = $params['limit'];
		}
		
	    $member_suggestions = $userTb -> fetchAll($select);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$index = 1;
		foreach ($member_suggestions as $subject) 
		{
			if($index > $limit)
			{
				break;
			}
		    if($viewer->getGuid(false) === $subject->getGuid(false) ) {
		      continue;
		    }
		    
		    // No blocked
		    if( $viewer->isBlockedBy($subject) ) {
		      continue;
		    }
		
		    // Check if friendship is allowed in the network
		    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
		    if( !$eligible ) {
		      continue;
		    }
		
		    // check admin level setting if you can befriend people in your network
		    else if( $eligible == 1 ){
		      
		      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
		      $networkMembershipName = $networkMembershipTable->info('name');
		
		      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
		      $select
		        ->from($networkMembershipName, 'user_id')
		        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
		        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
		        ->where("`{$networkMembershipName}_2`.user_id = ?", $subject->getIdentity())
		        ;
		
		      $data = $select->query()->fetch();
		
		      if( empty($data) ) {
		        continue;
		      }
		    }
			
			$row = $viewer->membership()->getRow($subject);
			if($row && $row->user_approved == 0 ) {
				continue;
			}
			
			$arr_suggestions[] = $subject;
			$index ++;
		}
		return $arr_suggestions;
	}
	
	public function getGroupSuggestions($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if(!$viewer_id || !Engine_Api::_() -> hasItemType('group'))
		{
			return NULL;
		}
		$groupsJoined = $this ->getMemberBelongGroup();
		
		$groupTb = Engine_Api::_() -> getItemTable('group');
		$groupName = $groupTb->info('name');
		$select = $groupTb -> select();
		if($groupsJoined)
			$select -> where("$groupName.group_id NOT IN (?)", $groupsJoined) -> order("rand()");
		if(isset($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		if(isset($params['category']) && $params['category'])
		{
			$select -> where("$groupName.category_id = ?", $params['category']);
		}
		$groups = $groupTb -> fetchAll($select);
		$tmp_groups = array();
		if(count($groups) < $params['limit'] && isset($params['category']) && $params['category'])
		{
			foreach ($groups as $group) 
			{
				$groupsJoined[] = $group -> getIdentity();
				$tmp_groups[] = $group;
			}
			$new_limit = $params['limit'] - count($groups);
			$select = $groupTb -> select();
			if($groupsJoined)
				$select -> where("$groupName.group_id NOT IN (?)", $groupsJoined) -> order("rand()") -> limit($new_limit);
			
			$new_groups = $groupTb -> fetchAll($select);
			foreach ($new_groups as $new_group) 
			{
				$tmp_groups[] = $new_group;
			}
			$groups = $tmp_groups;
		}
		return $groups;
	}

	public function getEventSuggestions($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		if(!$viewer_id || !Engine_Api::_() -> hasItemType('event'))
		{
			return NULL;
		}
		$eventJoined = $this ->getMemberBelongEvent();
		$eventTb = Engine_Api::_() -> getItemTable('event');
		$eventName = $eventTb->info('name');
		$select = $eventTb -> select();
		if($eventJoined)
			$select -> where("$eventName.event_id NOT IN (?)", $eventJoined) -> order("rand()");
		if(isset($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		if(isset($params['category']) && $params['category'])
		{
			$select -> where("$eventName.category_id = ?", $params['category']);
		}
		
		$oldTz = date_default_timezone_get();
		if($viewer -> getIdentity())
			date_default_timezone_set($viewer -> timezone);
		$date = date('Y-m-d H:i:s');
		date_default_timezone_set($oldTz);
		$time = strtotime($date);
		
		$select = $select -> where("endtime > FROM_UNIXTIME(?)", $time);
		
		$events = $eventTb -> fetchAll($select);
		$tmp_events = array();
		if(count($events) < $params['limit'] && isset($params['category']) && $params['category'])
		{
			foreach ($events as $event) 
			{
				$eventJoined[] = $event -> getIdentity();
				$tmp_events[] = $event;
			}
			$new_limit = $params['limit'] - count($events);
			$select = $eventTb -> select();
			if($eventJoined)
				$select -> where("$eventName.event_id NOT IN (?)", $eventJoined) -> order("rand()") -> limit($new_limit);
			$select = $select -> where("endtime > FROM_UNIXTIME(?)", $time);
			$new_events = $eventTb -> fetchAll($select);
			foreach ($new_events as $new_event) 
			{
				$tmp_events[] = $new_event;
			}
			$events = $tmp_events;
		}
		return $events;
	}

	public function getMostLikedItems($params = array())
	{
		$notInclude = array('core_comment', 'activity_comment');
		$likeTb = Engine_Api::_() -> getDbTable('likes', 'core');
		$likeName = $likeTb->info('name');
		$select = $likeTb -> select() -> from($likeName, array("$likeName.*", new Zend_Db_Expr("COUNT(*) AS count")))
				-> group("$likeName.resource_type") 
				-> group("$likeName.resource_id") 
				-> order("count DESC") -> where("$likeName.resource_type NOT IN (?)", $notInclude);
		if(isset($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		return $likeTb -> fetchAll($select);
	}
	public function getTimeAgo($obj)
	{
		$view = Zend_Registry::get("Zend_View");
		$now = time();
		$timeAgo = "Active ";
		$date = strtotime($obj->creation_date);
		$years = date('Y', $now) - date("Y", $date);
		$months = date('n', $now) - date("n", $date);
		$days = date('j', $now) - date("j", $date);
		if($months < 0)
		{
			$months = 12 + $months;
			$years = $years - 1;
		}
		if($days < 0)
		{
			$days = 30 + $days;
			$months = $months - 1;
		}
		if($years)
		{
			$timeAgo .= $view -> translate(array("%s year ago", "%s years ago", $years),$years);
			return $timeAgo;
		}
		else if($months)
		{
			$timeAgo .= $view -> translate(array("%s month ago", "%s months ago", $months), $months);
			return $timeAgo;
		}	
		else if($days)
		{
			$timeAgo .= $view -> translate(array("%s day ago", "%s days ago", $days), $days);
			return $timeAgo;
		}
		else 
		  return $view->translate("Active today");
	}
    // check adc comemnt enabled
    public function checkEnabledAdvancedComment()
    {
        if(Engine_Api::_() -> hasModuleBootstrap('yncomment'))
        {
            $moduleTable = Engine_Api::_() -> getDbTable('modules', 'yncomment');
            $select = $moduleTable -> select() -> where ('module = ?', 'ynfeed') -> where ('enabled = 1');
            return $moduleTable -> fetchRow($select);
        }
        else
        {
            return NULL;
        }
    }
}
