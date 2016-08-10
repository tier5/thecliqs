<?php

class Ynmobile_Service_Feed extends Ynmobile_Service_Base{
    
    protected $module = 'activity';
    protected $mainItemType = 'action';
    
    protected $_hasParentList = array(
        'forum_topic', 'ynforum_topic',
        'forum_post', 'ynforum_post',
    );
		
	public function get_user_groups($aData) {
		
		extract($aData);
		$iLimit =  $iLimit?intval($iLimit):10;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity()) {
			return array();
		} 
		
		$data = array();
		$table = Engine_Api::_() -> getItemTable('user');
		$table_name = $table -> info('name');
		$select = $viewer -> membership() -> getMembersObjectSelect();
		$select -> limit($iLimit);
		
		
		$text = $q?substr($q, 1):"";

		if (null !== ($text)) {
			$select -> where("$table_name.displayname LIKE '%{$text}%' OR $table_name.username LIKE '%{$text}%'");
		}
		if ($exclude_users) {
			$select -> where("$table_name.user_id NOT IN (?)", $exclude_users);
		}
		
		foreach ($table -> fetchAll ( $select ) as $friend) {
			$data[] = Ynmobile_AppMeta::_export_one($friend, array('simple_array'));
		}

		$checkGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('group');
		$checkAdvGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('advgroup');
		if (($checkGroup) || ($checkAdvGroup)) {
			$groupTable = Engine_Api::_() -> getItemTable('group');
			$group_select = $groupTable -> select() -> where('title LIKE ?', '%' . $text . '%') -> where('search = 1');
			if ($arr_groups) {
				$group_select -> where("group_id NOT IN (?)", $arr_groups);
			}
			$group_select -> limit($iLimit);
			
			$group_results = $groupTable -> fetchAll($group_select);
			foreach ($group_results as $result) {
				$data[] = Ynmobile_AppMeta::_export_one($result, array('simple_array'));
			}
		}
		
		return array(
			'q'=>$q,
			'rows'=>$data,
		);
		return $data;
	}

	public function emoticons(){
		 $data = array();
		 
		 $emoticons = Engine_Api::_() -> ynfeed() -> getEmoticons();
		 $baseUrl = Ynmobile_Helper_Base::getBaseUrl();
		 
	     foreach ($emoticons as $emoticon) {
	     	$data[] =  array(
				'text'=>$emoticon->text,
				'title'=>$emoticon->title,
				'image'=>$this->finalizeUrl($baseUrl."/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}"),	
			);
		}
		 return $data;
	}
	
	public function filter_list(){
		$tabs  =  Engine_Api::_()->getDbtable('contents', 'ynfeed')->getContentList(array('show' => 1, 'content_tab' => 1));
		$result = array();
		
		foreach($tabs as $tab){
			$result[] =  $tab->toArray();	
		}
		
		return $result;
	}
    
	/**
	 * require:
	 * + $sItemType
	 * + $iItemId
	 */
	public function hide($aData){
		
		extract($aData);
		
		$viewer = $this->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');
		
		$result = $hideTable -> insert(array('user_id' => $viewer_id, 'hide_resource_type' => $sItemType, 'hide_resource_id' => $iItemId));
		
		return array(
			'error_code'=>0,
			'message'=>'This change has been saved.',
		);
	}
    
	/**
	 * + $sItemType
	 * + $iItemId
	 */
	public function unhide($aData){
		extract($aData);
		
		$viewer = $this->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');
		
		$hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $sItemType, 'hide_resource_id =?' => $iItemIds));
		
		return array(
			'error_code'=>0,
			'message'=>'This change has been saved.',
		);
	}
	
	public function get_unhide_from_list($aData){
		
		extract($aData);
		
		$hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');
		$viewer = $this->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$select = $hideTable->select()
				->where('user_id=?', $viewer_id)
				->where('hide_resource_type=?', 'user');
		
		$iLimit =  $iLimit? intval($iLimit):10;
		$iPage  = $iPage?intval($iPage):1;
		
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($iPage);
		$paginator->setItemCountPerPage($iLimit);
		
		if($paginator->count() < $iPage){
			return array();
		}
		
		$result  = array();
		foreach($paginator as $item){
			$object =  $this->getWorkingItem($item->hide_resource_type, $item->hide_resource_id);
			if($object){
				$result[] = Ynmobile_AppMeta::_export_one($object, array('simple_array'));	
			}
		}
		return $result;
	}
	
	/**
	 * aItems: [{sItemType: "user", iItemId: 12}, ... ]
	 */
	public function update_unhide_from($aData){
		extract($aData);
		
		$viewer = $this->getViewer();
		$viewer_id = $viewer->getIdentity();
		
		$hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');
		
		if(!empty($aItems)){
			foreach($aItems as $poster){
				$sItemType =  $poster['sItemType'];
				$iItemId  =  $poster['iItemId'];
				$hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $sItemType, 'hide_resource_id =?' => $iItemId));		
			}
		}
		
		return array(
			'error_code'=>0,
			'aItems'=>$aItems,
			'message'=>'This change has been saved.',
		);
	}

	/**
	 * require 
	 * + $iActionId
	 * + $iValue: 1 => get, 0: reset
	 */
	public function update_notification($aData){
		
		extract($aData);
		
		$viewer  = $this->getViewer();
		
		$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($iActionId);
		
		// Start transaction
		$table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
		
		$iValue  =  $table->getOptionFeed($viewer, $action->getIdentity(), $action -> type, 'notification')?0:1;
		
		$table -> setOptionFeeds($viewer, $action->getIdentity(), $action -> type, 'notification', $iValue);
		
		return array(
			'error_code'=> 0,
			'message'=>'This change has been saved.',
		);
	}
	
	/**
	 * require 
	 * + $iActionId
	 */
	public function update_comment($aData){
		
		extract($aData);
		
		$viewer  = $this->getViewer();
		
		$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($iActionId);
		
		// Start transaction
		$table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
		$table -> setOptionFeeds($viewer, $action->getIdentity(), $action -> type, 'comment');
		
		return array(
			'error_code'=> 0,
			'message'=>'This change has been saved.',
		);
	}
	
	/**
	 * require 
	 * + $iActionId
	 */
	public function update_lock($aData){
		
		extract($aData);
		
		$viewer  = $this->getViewer();
		
		$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($iActionId);
		
		// Start transaction
		$table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
		$table -> setOptionFeeds($viewer, $action->getIdentity(), $action -> type, 'lock');
		
		return array(
			'error_code'=> 0,
			'message'=>'This change has been saved.',
		);
	}
	
	public function update_save_feed($aData){
		extract($aData);
		$viewer = $this->getViewer();
		
		$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($iActionId);
		
		// Start transaction
		$table = Engine_Api::_() -> getDbtable('saveFeeds', 'ynfeed');
		
		$table -> setSaveFeeds($viewer, $action->getIdentity(), $action -> type);
		
		return array(
			'error_code'=>0,
			'message'=>'This change has been saved.'
		);
	}

	public function group_privacies($aData) {
		
		extract($aData);
		
		$viewer = $this->getViewer();
		
		if(!$viewer){
			return array();
		}
		
		$data = array();
		$text = $aData['search'];
		// Get General
		$subjectType = $sItemType;
		$view  = Zend_Registry::get('Zend_View');
		
		if($subjectType == "" or $subjectType == 'user')
		{
			if(!empty($text))
			{
				if(strpos(strtolower($view ->  translate("Everyone")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'everyone', 
						'label' => $view -> translate("Everyone"), 
						'photo' => '', 
						'url' => '');
			    if(strpos(strtolower($view ->  translate("Friends & Networks")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'network', 
						'label' => $view -> translate("Friends & Networks"),  
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view -> translate("Friends Only")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'member', 
						'label' => $view -> translate("Friends Only"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view -> translate("Only Me")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'owner', 
						'label' => $view -> translate("Only Me"), 
						'photo' => '', 
						'url' => '');
			}
			else 
			{
				$data[] = array('type' => 'general', 
					'id' => 'everyone', 
					'label' => $view -> translate("Everyone"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'network', 
					'label' => $view -> translate("Friends & Networks"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'member', 
					'label' => $view -> translate("Friends Only"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'owner', 
					'label' => $view ->  translate("Only Me"), 
					'photo' => '', 
					'url' => '');
			}
			
			// Get Networks
			$networkTable = Engine_Api::_()->getDbtable('networks', 'network');
			$ntable_name = $networkTable -> info('name');
			$select = Engine_Api::_()->getDbtable('membership', 'network') 
				->getMembershipsOfSelect($viewer) 
				-> where("$ntable_name.title LIKE '%{$text}%'");
			foreach ($networkTable -> fetchAll ( $select ) as $network) {
				$data[] = array('type' => 'network', 'id' => $network -> getIdentity(), 'guid' => '', 'label' => $network -> getTitle(), 'photo' => '', 'url' => '');
			}
			
			// Get Friend List
			$listTable = Engine_Api::_()->getItemTable('user_list');
      		$lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()) -> where("title LIKE '%{$text}%'") -> order('title ASC') -> limit(50));
			foreach ($lists as $list) 
			{
				$data[] = array('type' => 'friendlist', 
					'id' => $list -> list_id, 
					'label' => $list -> getTitle(), 
					'photo' => '', 
					'url' => '');
			}
			
			// Get Friends
			$table = Engine_Api::_() -> getItemTable('user');
			$table_name = $table -> info('name');
			$select = $viewer -> membership() -> getMembersObjectSelect();
			$select -> limit(50) -> order("$table_name.displayname ASC");
			if(!empty($text)) 
			{
				$select -> where("$table_name.displayname LIKE '%{$text}%' OR $table_name.username LIKE '%{$text}%'");
			}
			foreach ($table -> fetchAll ( $select ) as $friend) 
			{
				if($friend -> getIdentity())
				{
					$data[] = array(
						'type' => 'user', 
						'id' => $friend -> getIdentity(), 
						'guid' => $friend -> getGuid(), 
						'label' => $friend -> getTitle(), 
						'photo' => $this->finalizeUrl($friend->getPhotoUrl('thumb.icon')),
						 'url' => $friend -> getHref());
				}
			}
			
			// Get groups
			$checkGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('group');
			$checkAdvGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('advgroup');
			if (($checkGroup) || ($checkAdvGroup)) {
				if($checkGroup)
					$membership = Engine_Api::_()->getDbtable('membership', 'group');
				else {
					$membership = Engine_Api::_()->getDbtable('membership', 'advgroup');
				}
				$groupTable = Engine_Api::_() -> getItemTable('group');
				$table_name = $groupTable -> info('name');
				$group_select = $membership->getMembershipsOfSelect($viewer);
				$group_select -> where("$table_name.title LIKE ?", '%' . $text . '%') -> where('search = 1') -> order('title ASC') -> limit(50);
				$group_results = $groupTable -> fetchAll($group_select);
				
				foreach ($group_results as $result) {
					$data[] = array(
						'type' => 'group', 
						'id' => $result -> getIdentity(), 
						'guid' => $result -> getGuid(), 
						'label' => $result -> getTitle(),
						'photo' => $this->finalizeUrl($result->getPhotoUrl('thumb.icon')),
						'url' => $result -> getHref());
				}
			}
		}
		elseif($subjectType == 'group') 
		{
			if(!empty($text))
			{
				if(strpos(strtolower($view ->  translate("Everyone")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'everyone', 
						'label' => $view ->  translate("Everyone"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view ->  translate("All Group Members")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'member', 
						'label' => $view ->  translate("All Group Members"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view ->  translate("Officers and Owner Only")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'officer', 
						'label' => $view ->  translate("Officers and Owner Only"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view ->  translate("Owner Only")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'owner', 
						'label' => $view ->  translate("Owner Only"), 
						'photo' => '', 
						'url' => '');
			}
			else 
			{
				$data[] = array('type' => 'general', 
					'id' => 'everyone', 
					'label' => $view ->  translate("Everyone"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'member', 
					'label' => $view ->  translate("All Group Members"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'officer', 
					'label' => $view ->  translate("Officers and Owner Only"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'owner', 
					'label' => $view ->  translate("Owner Only"), 
					'photo' => '', 
					'url' => '');
			}
		}
		elseif($subjectType == 'event')
		{
			if(!empty($text))
			{
				if(strpos(strtolower($view ->  translate("Everyone")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'everyone', 
						'label' => $view ->  translate("Everyone"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view ->  translate("Event Guests Only")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'member', 
						'label' => $view ->  translate("Event Guests Only"), 
						'photo' => '', 
						'url' => '');
				if(strpos(strtolower($view ->  translate("Owner Only")), strtolower($text)) !== FALSE)
					$data[] = array('type' => 'general', 
						'id' => 'owner', 
						'label' => $view ->  translate("Owner Only"), 
						'photo' => '', 
						'url' => '');
			}
			else
			{
				$data[] = array('type' => 'general', 
					'id' => 'everyone', 
					'label' => $view ->  translate("Everyone"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'member', 
					'label' => $view ->  translate("Event Guests Only"), 
					'photo' => '', 
					'url' => '');
				$data[] = array('type' => 'general', 
					'id' => 'owner', 
					'label' => $view ->  translate("Owner Only"), 
					'photo' => '', 
					'url' => '');
			}
		}
		
		return $data;
	}
	
    public function fetch($aData){
        
        if(isset($aData['iLimit']))
        {	
            $aData['iLimit'] =  $aData['iLimit'];
        }

        $sAction =  isset($aData['sAction'])?$aData['sAction']:'new';

        $sAction = $sAction == 'new'?'new':'more';

        if($sAction == 'more'){
            if(isset($aData['iMinId'])){
                unset($aData['iMinId']);
            }
        }

        if($sAction =='new'){
            if(isset($aData['iMaxId'])){
                unset($aData['iMaxId']);
            }
        }

        if(isset($aData['iMaxId']) && $aData['iMaxId'] > 0){
            $aData['iMaxId'] = $aData['iMaxId'] -1;
        }

        if(isset($aData['iMinId']) && $aData['iMinId'] > 0){
            $aData['iMinId'] = $aData['iMinId'] +1;
        }
		
		if($aData['sItemType'] && $aData['iItemId']){
			$subject = Engine_Api::_() -> getItem($aData['sItemType'], $aData['iItemId']);
			
			if($subject){
				Engine_Api::_()->core()->setSubject($subject);	
			}
		}
		
		if(Engine_Api::_()->hasModuleBootstrap('ynfeed')){
			return $this->ynfeed_get($aData);	
		}else{
			return $this->get($aData);	
		}
        
    }


	public function ynfeed_get($aData) {
		
		extract($aData);
		
		// Don't render this if not authorized
		$viewer = $this->getViewer();
		$subject = null;
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'ynfeed');

		if (Engine_Api::_() -> core() -> hasSubject()) {
			// Get subject
			$subject = Engine_Api::_() -> core() -> getSubject();
			if (!$subject -> authorization() -> isAllowed($viewer, 'view')) {
				return array(
					"error_code"=>"1",
					"error_message"=>"You don't have permission to view feed!"
				);
			}
		}else if(isset($iActionId) && $iActionId > 0){
			$sampleAction = $actionTable->getActionById($iActionId);
            if (is_object($sampleAction)){
            	$subject = $sampleAction->getSubject();
            	Engine_Api::_() -> core() -> setSubject($subject);
            }
		}

		
		$friendUsers = Engine_Api::_() -> ynfeed() -> getViewerFriends($viewer);

		// Get some settings from Ynfeed settings
		$settings = Engine_Api::_() -> getApi('settings', 'core');


		// Get some options
		
		$length = isset($iLimit)?$iLimit:10;
		$itemActionLimit = $settings -> getSetting('activity.userlength', 5);

		
		$default_firstid = null;
    	$listTypeFilter = array();

		$actionFilter = $aData['actionFilter'];
		$filterValue = $aData['filterValue'];
		
		

		$actionTypeFilters = array();
		
		if ($actionFilter && !in_array($actionFilter, array('membership', 'owner', 'all', 'network_list', 'member_list', 'custom_list'))) 
		{
	      $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'ynfeed');
	      $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes($actionFilter);
	      if (isset($groupedActionTypes[$actionFilter])) 
	      {
	        $actionTypeFilters = $groupedActionTypes[$actionFilter];
	      }
	    } 
	    elseif (in_array($actionFilter, array('member_list', 'custom_list')) && $filterValue != null) 
		{
	       $listTypeFilter = Engine_Api::_()->ynfeed()->getListBaseContent($actionFilter, array('filterValue' => $filterValue));
	    } 
	    else if ($actionFilter == 'network_list' && $filterValue != null) 
	    {
	       $listTypeFilter = array($filterValue);
	    }
		 
		// Get config options for activity
		$config = array(
			'action_id' => $iActionId,
			'max_id' => @$iMaxId, 
			'min_id' => @$iMinId, 
			'limit' => $iLimit?intval($iLimit):10,
			'showTypes' => $actionTypeFilters,
			'actionFilter' => $actionFilter, 
			'filterValue' => $filterValue,
			'listTypeFilter' => $listTypeFilter);
			
		// return $config;

		// Pre-process feed items
		$selectCount = 0;
		$nextid = null;
		$firstid = null;
		$tmpConfig = $config;
		$activity = array();
		$endOfFeed = false;

		$friendRequests = array();
		$itemActionCounts = array();
		$enabledModules = Engine_Api::_() -> getDbtable('modules', 'core') -> getEnabledModuleNames();

		$hideItems = array();
		if ($viewer -> getIdentity())
			$hideItems = Engine_Api::_() -> getDbtable('hide', 'ynfeed') -> getHideItemByMember($viewer);
		if ($default_firstid) {
        	$firstid = $default_firstid;
      	}
		
		
		
		// return $hideItems;
		
		do {
			// Get current batch
			$actions = null;
			
			// Where the Activity Feed is Fetched
			if (!empty($subject)) {
				$actions = $actionTable -> getActivityAbout($subject, $viewer, $tmpConfig);
			} else {
				$actions = $actionTable -> getActivity($viewer, $tmpConfig);
			}

			

			$selectCount++;
			
			// Are we at the end?
			if (count($actions) < $length || count($actions) <= 0) {
				$endOfFeed = true;
			}

			// Pre-process
			if (count($actions) > 0) {
				foreach ($actions as $action) {

						
					// get next id
					if (null === $nextid || $action -> action_id <= $nextid) {
						$nextid = $action -> action_id - 1;
					}
					// get first id
					if (null === $firstid || $action -> action_id > $firstid) {
						$firstid = $action -> action_id;
					}
					// skip disabled actions
					if (!$action -> getTypeInfo() || !@$action -> getTypeInfo() -> enabled)
						continue;

					// skip items with missing items
					if (!@$action -> getSubject() || !@$action -> getSubject() -> getIdentity())
						continue;

					if (!@$action -> getObject() || !@$action -> getObject() -> getIdentity())
						continue;

					// skip the hide actions and content
					if (!empty($hideItems)) {
						
						if (isset($hideItems[$action -> getType()]) && in_array($action -> getIdentity(), $hideItems[$action -> getType()])) {
							continue;
						}
						

						if ($action -> getSubject() -> getType() == 'user' && isset($hideItems[$action -> getSubject() -> getType()]) && in_array($action -> getSubject() -> getIdentity(), $hideItems[$action -> getSubject() -> getType()])) 
						{
							continue;
						}
					}

					// track/remove users who do too much (but only in the main feed)
					if (empty($subject)) {
						$actionSubject = $action -> getSubject();
						$actionObject = $action -> getObject();
						if (!isset($itemActionCounts[$actionSubject -> getGuid()])) {
							$itemActionCounts[$actionSubject -> getGuid()] = 1;
						} else if ($itemActionCounts[$actionSubject -> getGuid()] >= $itemActionLimit) {
							continue;
						} else {
							$itemActionCounts[$actionSubject -> getGuid()]++;
						}
					}
					// remove duplicate friend requests
					if ($action -> type == 'friends') {
						$id = $action -> subject_id . '_' . $action -> object_id;
						$rev_id = $action -> object_id . '_' . $action -> subject_id;
						if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
							continue;
						} else {
							$friendRequests[] = $id;
							$friendRequests[] = $rev_id;
						}
					}

					// remove items with disabled module attachments
					try {
						$attachments = $action -> getAttachments();
					} catch (Exception $e) {
						// if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
						continue;
					}

					// add to list
					if (count($activity) < $length) {
							
						$activity[] = Ynmobile_AppMeta::_export_one($action, array('detail'));;
						
						if (count($activity) == $length) {
							$actions = array();
						}
					}
					
				
				}
			}

			// Set next tmp max_id
			if ($nextid) {
				$tmpConfig['max_id'] = $nextid;
			}
			if (!empty($tmpConfig['action_id'])) {
				$actions = array();
			}
		} while( count($activity) < $length && $selectCount <= 3 && !$endOfFeed );
		
		return $activity;
		
	}

    /**
     * Input data:
     * + iActionId: int, optional.
     * + iMaxId: int, optional.
     * + iMinId: int, optional.
     * + iLimit: int, optional.
     * + iItemId: int, optional.
     * + sItemType: string, optional.
     *
     * Output data:
     * + iActionId: int.
     * + iUserId: int.
     * + sUsername: string.
     * + UserProfileImg_Url: string.
     * + sFullName: string.
     * + bCanPostComment: bool.
     * + sTime: string.
     * + sTimeConverted: string.
     * + sActionType: string.
     * + iItemId: int.
     * + sItemTitle: item title of the object (album, music, user,..)
     * + sItemType: object type (ex: music,...)
     * + bIsLike: bool.
     * + sContent: string
     * + bReadMore: bool
     * + iShare: (shareable: 1, 2, 3, 4)
     * + aAttachments: array()
     * + sModule
     * + sType
     * + sTitle
     * + sDescription
     * + sPhoto_Url
     * + sLink_Url
     * @see Mobile - API SE/Api V1.0
     * @see feed/get
     */

    public function get($aData)
    {
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : null;
        $iMinId = isset($aData['iMinId']) ? (int)$aData['iMinId'] : null;
        $iMaxId = isset($aData['iMaxId']) ? (int)$aData['iMaxId'] : null;
        $iActionId = isset($aData['iActionId']) ? (int)$aData['iActionId'] : null;
        $iTotalFeeds = (int) isset($aData['iLimit']) ? (int)$aData['iLimit'] : Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.length', 15);
        $itemActionLimit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.userlength', 5);
        $oViewer = Engine_Api::_() -> user() -> getViewer();
        
        $subject = null;
        $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
        
        if ($iItemId)
        {
            // Get subject
            $subject = Engine_Api::_() -> getItem($sItemType, $iItemId);
            if (!$subject -> authorization() -> isAllowed($oViewer, 'view'))
            {
                return array(
                    'error_code' => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to view feed on wall!")
                );
            }
        }
        else 
        {
            $sampleAction = $actionTable->getActionById($iActionId);
            if (is_object($sampleAction) &&  $sampleAction instanceof Activity_Model_Action )
                $subject = $sampleAction->getSubject();
        }

        // Get config options for activity
        $config = array(
            'action_id' => $iActionId,
            'max_id' => $iMaxId,
            'min_id' => $iMinId,
            'limit' => $iTotalFeeds,
        );
        // Pre-process feed items
        $selectCount = 0;
        $nextid = null;
        $firstid = null;
        $tmpConfig = $config;
        $activity = array();
        $endOfFeed = false;

        $friendRequests = array();
        $itemActionCounts = array();
        
        do
        {
            // Get current batch
            $actions = null;
            
            // Where the Activity Feed is Fetched
            if (!empty($subject))
            {
                $actions = $actionTable -> getActivityAbout($subject, $oViewer, $tmpConfig);
            }
            else
            {
                $actions = $actionTable -> getActivity($oViewer, $tmpConfig);
            }
            
            $selectCount++;
            // Are we at the end?
            if (count($actions) < $iTotalFeeds || count($actions) <= 0)
            {
                $endOfFeed = true;
            }

            // Pre-process
            if (count($actions) > 0)
            {
                foreach ($actions as $action)
                {
                    try{
                    // get next id
                    if (null === $nextid || $action -> action_id <= $nextid)
                    {
                        $nextid = $action -> action_id - 1;
                    }
                    // get first id
                    if (null === $firstid || $action -> action_id > $firstid)
                    {
                        $firstid = $action -> action_id;
                    }
                    // skip disabled actions
                    if (!$action -> getTypeInfo() || !$action -> getTypeInfo() -> enabled)
                        continue;
                    // skip items with missing items
                    if (!$action -> getSubject() || !$action -> getSubject() -> getIdentity())
                        continue;
                    if (!$action -> getObject() || !$action -> getObject() -> getIdentity())
                        continue;
                    // track/remove users who do too much (but only in the main feed)
                    if (empty($oUser))
                    {
                        $actionSubject = $action -> getSubject();
                        $actionObject = $action -> getObject();
                        if (!isset($itemActionCounts[$actionSubject -> getGuid()]))
                        {
                            $itemActionCounts[$actionSubject -> getGuid()] = 1;
                        }
                        else
                        if ($itemActionCounts[$actionSubject -> getGuid()] >= $itemActionLimit)
                        {
                            continue;
                        }
                        else
                        {
                            $itemActionCounts[$actionSubject -> getGuid()]++;
                        }
                    }
                    // remove duplicate friend requests
                    if ($action -> type == 'friends')
                    {
                        $id = $action -> subject_id . '_' . $action -> object_id;
                        $rev_id = $action -> object_id . '_' . $action -> subject_id;
                        if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests))
                        {
                            continue;
                        }
                        else
                        {
                            $friendRequests[] = $id;
                            $friendRequests[] = $rev_id;
                        }
                    }

                    // remove items with disabled module attachments
                    try
                    {
                        $attachments = $action -> getAttachments();
                    }
                    catch (Exception $e)
                    {
                        // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
                        continue;
                    }

                    // add to list
                    if (count($activity) < $iTotalFeeds)
                    {
                     
                     
                     
                        $activity[] = Ynmobile_AppMeta::_export_one($action, array('detail'));    
                    
                        

                        if (count($activity) == $iTotalFeeds)
                        {
                            $actions = array();
                        }
                    }
                     }   
                     catch(Exception $ex){
                         
                         // var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
                         // exit();
//                          
                        return array('error_code'=>1,
                            'error_message'=>$ex->getMessage(),
                            'error_file'=>$ex->getFile(),
                            'error_line'=>$ex->getLine());    
                         }
                }
            }

            // Set next tmp max_id
            if ($nextid)
            {
                $tmpConfig['max_id'] = $nextid;
            }
            if (!empty($tmpConfig['action_id']))
            {
                $actions = array();
            }
        }
        while( count($activity) < $iTotalFeeds && $selectCount <= 3 && !$endOfFeed );
        return $activity;
    }
    
    /**
     * view a feed.
     */
    
    public function view($aData){
        
        extract($aData);

        $iActionId  = $iActionId?intval($iActionId):0;

        $actionTable = Engine_Api::_()->getItemTable('activity_action');

        $select = $actionTable->select()->where('action_id=?', $iActionId);

       	$action     = $actionTable->fetchRow($select);

        if (is_object($sampleAction)){
        	$subject = $sampleAction->getSubject();
        	Engine_Api::_() -> core() -> setSubject($subject);
        }
        
        if(!$action){
            return array('error_code'=>1,'message'=>'Feed not found!');
        }

        return Ynmobile_AppMeta::_export_one($action, array('detail'));
    }

    /**
     * Input data:
     * + iActionId: int, required.
     * + iCommentId: int, required
     *
     * Output data:
     * + result: int.
     * + error_code: int.
     * + error_message: string.
     *
     * @see Mobile - API SE/Api V1.0
     * @see feed/delete
     *
     * @global type $token
     * @param array $aData
     * @return array
     */
    public function delete($aData)
    {

        extract($aData);
        $iActionId =  intval($iActionId);
        
        $action = Engine_Api::_() -> getDbtable('actions', 'activity') -> getActionById($iActionId);
        if (!$action)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, the action not found")
            );
        }

        $viewer =  $this->getViewer();
        
        if (!$viewer){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this action!")
            );
        }
        
        $activity_moderate = Engine_Api::_() -> getDbtable('permissions', 'authorization') -> getAllowed('user', $viewer -> level_id, 'activity');
        
        
        if (($activity_moderate || ('user' == $action -> subject_type && $viewer -> getIdentity() == $action -> subject_id) ||
        ('user' == $action -> object_type && $viewer -> getIdentity() == $action -> object_id)))// commenter
        {
            try
            {
                $action -> deleteItem();
                return array(
                    'message' => Zend_Registry::get('Zend_Translate') -> _("This activity item has been removed.")
                );
            }
            catch (Exception $e)
            {
                return array(
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Delete action fail!")
                );
            }
        }
        else
        {
            return array(
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!")
            );
        }
    }

	public function post($aData){
		if(Engine_Api::_()->hasModuleBootstrap('ynfeed')){
    		return $this->post_ynfeed($aData);
    	}else{
    		return $this->post_default($aData);
    	}
	}
	
	/**
     * Input data:
     * + sContent: string, required.
     * + iSubjectId: int, optional.
     * + sSubjectType: string, optional. (user/group)
     * + aAttachment: array optional.
     *
     * Output data:
     * + error_code: int.
     * + error_message: string.
     * + iActionId: int.
     * + iUserId: int.
     * + sUsername: string.
     * + UserProfileImg_Url: string.
     * + sFullName: string.
     * + bCanPostComment: bool.
     * + sTime: string.
     * + sTimeConverted: string.
     * + sActionType: string.
     * + iItemId: int.
     * + sItemTitle: item title of the object (album, music, user,..)
     * + sItemType: object type (ex: music,...)
     * + bIsLike: bool.
     * + sContent: string
     * + iShare: (shareable: 1, 2, 3, 4)
     *
     * @see Mobile - API SE/Api V1.0.
     * @see feed/post
     *
     * @param array $aData
     * @return array
     */
    public function post_default($aData)
    {
    
		extract($aData, EXTR_SKIP);
        $viewer = Engine_Api::_() -> user() -> getViewer();
		
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this action!")
            );
        }
        
        if (!isset($sContent))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $subject = null;
        if (!empty($sSubjectType) && !empty($iSubjectId))
        {
            $subject = Engine_Api::_() -> getItem($sSubjectType, $iSubjectId);
        }
        // Use viewer as subject if no subject
        if (null === $subject)
        {
            $subject = $viewer;
        }

        $body = html_entity_decode($sContent, ENT_QUOTES, 'UTF-8');
        
        // set up action variable
        $action = null;
        // Process
        $db = Engine_Api::_() -> getDbtable('actions', 'activity') -> getAdapter();
        $db -> beginTransaction();

        try
        {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $aAttachment;
            
            if (!empty($attachmentData) && !empty($attachmentData['type']))
            {
                $type = $attachmentData['type'];
                if($type != 'photo')
                {
                    $config = null;
                    foreach (Zend_Registry::get('Engine_Manifest') as $data)
                    {
                        if (!empty($data['composer'][$type]))
                        {
                            $config = $data['composer'][$type];
                        }
                    }
                    if (!empty($config['auth']) && !Engine_Api::_() -> authorization() -> isAllowed($config['auth'][0], null, $config['auth'][1]))
                    {
                        $config = null;
                    }
                    if ($config)
                    {
                        $plugin = Engine_Api::_() -> loadClass($config['plugin']);
                        $method = 'onAttach' . ucfirst($type);
                        $attachment = $plugin -> $method($attachmentData);
                    }
                }
            }

            // Special case: status
            if (!$attachment && $viewer -> isSelf($subject) && $type != 'photo')
            {
                if ($body != '')
                {
                    $viewer -> status = $body;
                    $viewer -> status_date = date('Y-m-d H:i:s');
                    $viewer -> save();

                    $viewer -> status() -> setStatus($body);
                }
                $action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $subject, 'status', $body);
            }
            else
            {
                // General post
                $type = 'post';
                if ($viewer -> isSelf($subject))
                {
                    $type = 'post_self';
                }
                // Add notification for <del>owner</del> user
                $subjectOwner = $subject -> getOwner();

                if (!$viewer -> isSelf($subject) && $subject instanceof User_Model_User)
                {
                    $notificationType = 'post_' . $subject -> getType();
                    Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($subjectOwner, $viewer, $subject, $notificationType, array('url1' => $subject -> getHref(), ));
                }

                // Add activity
                $action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $subject, $type, $body);
                
                if($attachmentData['type'] != 'photo')
                {
                    // Try to attach if necessary
                    if ($action && $attachment)
                    {
                        Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
                    }
                }
                else 
                {
                    if ($action && !empty($attachmentData['photo_id']))
                    {
                        $count = 0;
                        $ids = explode(',', $attachmentData['photo_id']);
                        foreach($ids as $photo_id)
                        {
                            if (Engine_Api::_() -> hasModuleBootstrap('advalbum'))
                                $photo = Engine_Api::_()->getItem("advalbum_photo", $photo_id);
                            else
                                $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                                
                            if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) 
                            {
                                continue;
                            }
                            if($count < 8 )
                    {
                                Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                            }
                            $count ++;
                        }
                    }
                }
                
            }
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
        // If we're here, we're done
        //photoURL
        $sProfileImage = $viewer -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
        if ($sProfileImage != "")
        {
            $sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
        }
        else
        {
            $sProfileImage = NO_USER_ICON;
        }
        //userName
        $sUsername = $viewer -> username;
        if (!trim($sUsername))
            $sUsername = $viewer -> getIdentity();

        //canPostComment
        $canComment = ($action -> getTypeInfo() -> commentable && Engine_Api::_() -> authorization() -> isAllowed($subject, null, 'comment'));

        // Prepare data in locale timezone
        $timezone = null;
        if (Zend_Registry::isRegistered('timezone'))
        {
            $timezone = Zend_Registry::get('timezone');
        }
        if (null !== $timezone)
        {
            $prevTimezone = date_default_timezone_get();
            date_default_timezone_set($timezone);
        }

        $sTime = date("D, j M Y G:i:s O", $action -> getTimeValue());

        if (null !== $timezone)
        {
            date_default_timezone_set($prevTimezone);
        }
        return array(
            'error_code' => 0,
            'error_message' => "",
            'iActionId' => $action -> action_id,
            'iUserId' => $viewer -> getIdentity(),
            'sUsername' => $sUsername,
            'UserProfileImg_Url' => $sProfileImage,
            'sFullName' => $viewer -> getTitle(),
            'bCanPostComment' => $canComment,
            'sTime' => $sTime,
            'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($action -> getTimeValue()),
            'sActionType' => $action -> type,
            'iItemId' => $action -> object_id,
            'sItemTitle' => $subject -> getTitle(),
            'sItemType' => $subject -> getType(),
            'bIsLike' => $action -> likes() -> isLike($viewer),
            'sContent' => $body,
            'iTotalLike' => $action -> likes() -> getLikeCount(),
            'aUserLike' => Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($action),
            'iTotalComment' => $action -> comments() -> getCommentCount(),
            'iShare' => $action -> getTypeInfo() -> shareable
        );
    }
	
	public function post_ynfeed($aData) {
		
		extract($aData, EXTR_SKIP);
        $viewer = Engine_Api::_() -> user() -> getViewer();
		$view = Zend_Registry::get('Zend_View');
		
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this action!")
            );
        }
        
        if (!isset($sContent))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $subject = null;
        if (!empty($sSubjectType) && !empty($iSubjectId))
        {
            $subject = Engine_Api::_() -> getItem($sSubjectType, $iSubjectId);
        }
        // Use viewer as subject if no subject
        if (null === $subject)
        {
            $subject = $viewer;
        }

		
		// Check auth
		if (!$subject -> authorization() -> isAllowed($viewer, 'comment')) {
			return array(
				'error_code'=>1,
				'error_message'=>"You don't have permission to post"
			);
		}


		// Check if form is valid
		$postData = $aData;

		$body = $sContent;
		
		$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
		
		$privacies = array(
						'general' => $postData['SPRI_GE']?implode(',',$postData['SPRI_GE']):'',
						'friend_list' => $postData['SPRI_FL']?implode(',',$postData['SPRI_FL']):'',
						'network' => $postData['SPRI_NE']?implode(',',$postData['SPRI_NE']):'',
						'group' => $postData['SPRI_GR']?implode(',',$postData['SPRI_GR']):'',
						'friend' => $postData['SPRI_FR']?implode(',',$postData['SPRI_FR']):''
						);
						
		$arrTags = array();
		$arrHashTags = array();
		
		$url = $this->finalizeUrl("/");
		
		if (isset($postData['body_html']) && $postData['body_html'] != '') {
			
			$body = $postData['body_html'];
			$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
			
			$replacements = array();
			
			// Tags
			// $pattern = '/#tags@\w+@\d+@/';
			$friend_tag_reg = "/(\[x\=(\w+)\@(\d+)\])([^\[]+)(\[\/x\])/mi";
			$matches = array();
			$count_matched =  preg_match_all($friend_tag_reg, $body, $matches);
			
			if($count_matched){
				
				for($index =0; $index < $count_matched; ++$index){
					
					$type = $matches[2][$index];
					$item_id  = $matches[3][$index];
					$title  =  $matches[4][$index];
					
					$item = Engine_Api::_() -> getItem($type, $item_id);
					
					if($item){
						$arrTags[] = array('item_type' => $type, 'item_id' => $item_id);
						$replacements[$matches[0][$index]] = sprintf('<a ng-url="#/app/%s/%s" href="%s">%s</a>', $type, $item_id, $item->getHref(), $title);
					}else{
						$replacements[$matches[0][$index]] = $title;
					}
				}
			}
			

			// Hashtags
			// $pattern = '/#hashtags@(\w+)@/';
			$hash_tag_reg  =  "/(#)([\S]+)/";
			$matches =  array();
			
			$count_matched = preg_match_all($hash_tag_reg, $body, $matches);
			
			if($count_matched){
				
				for($index =0; $index < $count_matched; ++$index){
					$hashtag = $matches[2][$index];
					$replacements[$matches[0][$index]] = sprintf('<a ng-click="filterHashTag(\'%s\')" href="javascript:ynfeedFilter(\'hashtag\',\'%s\')">%s</a>',$hashtag,$hashtag,$matches[0][$index]);
					$arrHashTags[]= $hashtag ;
				}
			}
			$arrHashTags = array_unique($arrHashTags);
			
			if($replacements){
				$body =  strtr($body, $replacements);	
			}
		}
		
		
		

		$body = urldecode($body);
		$body = strip_tags($body, '<a><br>');
		$postData['body'] = $body;

		// set up action variable
		$action = null;

		// Process
		$db = Engine_Api::_() -> getDbtable('actions', 'activity') -> getAdapter();
		$db -> beginTransaction();

		try {
			// upload video.
			// move from video/upload to here.
			$video = null;
			
			if($upload_video){
				$paginator = $this->getWorkingApi('core','video') 
				  -> getVideosPaginator($values);
				
				$quota = Engine_Api::_() -> authorization()
		             -> getPermission($viewer -> level_id, 'video', 'max');
		             
				$current_count = $paginator -> getTotalItemCount();
				
				if (($current_count >= $quota) && !empty($quota)){
					return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first."),
						'result' => 0
					);
				}
				
				if( !isset($_FILES['video'])){
					return array(
							'error_code' => 2,
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("Upload failed, file size is too large!"),
							'result' => 0
					);
				}
				
				$illegal_extensions = array(
					'php',
					'pl',
					'cgi',
					'html',
					'htm',
					'txt'
				);
				if (in_array(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION), $illegal_extensions))
				{
					return array(
						'error_code' => 3,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid Upload'),
						'result' => 0
					);
				}
				
				$parent_type = 'user';
				$parent_id = $viewer -> getIdentity();
				$type = 3;
				
				if (!empty($aData['sSubjectType']))
				{
					$parent_type = $aData['sSubjectType'];
				}
		
				if (!empty($aData['iSubjectId']))
				{
					$parent_id = $aData['iSubjectId'];
				}
				
				$table = $this->getWorkingTable('videos','video');
        
				$db = $table -> getAdapter();
		
				try
				{
					$values = array();
					$values['user_id'] = $viewer -> getIdentity();
					$values['default_video_module'] = true;
					
					$params = array(
						'owner_type' => 'user',
						'owner_id' => $viewer -> getIdentity()
					);
		            
		            //fix issues.
		            // $params  =  array_merge(array('code'=>''), $params);
		            
					$video = Engine_Api::_() -> ynmobile() 
					 -> createVideo($params, $_FILES['video'], $values, 0);
					
					// sets up title and owner_id now just incase members switch page as soon as upload is completed
					$video -> title = $_FILES['video']['name'];
					$video -> owner_id = $viewer -> getIdentity();
					$video -> type = 3;
					
					$video -> parent_type = $sSubjectType;
					$video -> parent_id = $iSubjectId;
					$video -> search = 1;
					$video -> status_text = $body;
					
					if (!empty($aData['title']))
					{
						$video -> title = $aData['title'];
					}
					if (!empty($aData['description']))
					{
						$video -> description = $aData['description'];
					}
					$video -> save();
					
				}catch( Exception $e ){
					$db -> rollBack();
					return array(
						'error_code' => 4,
						'error_message' => $e->getMessage()
					);
				}
			}
			
			// Try attachment getting stuff
			$attachment = null;
			$attachmentData = $aAttachment;
			
			if (!empty($attachmentData) && !empty($attachmentData['type'])) {
				$type = $attachmentData['type'];
				$config = null;
				foreach (Zend_Registry::get('Engine_Manifest') as $data) {
					if (!empty($data['composer'][$type])) {
						$config = $data['composer'][$type];
					}
				}
				if (!empty($config['auth']) && !Engine_Api::_() -> authorization() -> isAllowed($config['auth'][0], null, $config['auth'][1])) {
					$config = null;
				}
				if ($config) {
					$plugin = Engine_Api::_() -> loadClass($config['plugin']);
					$method = 'onAttach' . ucfirst($type);
					$attachment = $plugin -> $method($attachmentData);
				}
			}

			$body = preg_replace('/<br[^<>]*>/', "\n", $body);
			$body = str_replace('../', '', $body);
			
			$baseUrl = $view->baseUrl();

			foreach (Engine_Api::_() -> ynfeed() -> getEmoticons() as $emoticon) {
				$body = str_replace($emoticon -> text, "<img src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}'/>", $body);
			}

			// Special case: status
			if (!$video && !$attachment && $viewer -> isSelf($subject)) {
				if ($body != '') {
					$viewer -> status = $body;
					$viewer -> status_date = date('Y-m-d H:i:s');
					$viewer -> save();

					$viewer -> status() -> setStatus($body);
				}

				$action = Engine_Api::_() 
				-> getDbtable('actions', 'ynfeed') 
				-> addActivity($viewer, $subject, 'status', $body, array('privacies' => $privacies));

			} else {// General post

				$type = 'post';
				if ($viewer -> isSelf($subject)) {
					$type = 'post_self';
				}

				// Add notification for <del>owner</del> user
				$subjectOwner = $subject -> getOwner();

				if (!$viewer -> isSelf($subject) && $subject instanceof User_Model_User) {
					$notificationType = 'post_' . $subject -> getType();
					Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($subjectOwner, $viewer, $subject, $notificationType, array('url1' => $subject -> getHref(), ));
				}

				// Add activity
				$action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> addActivity($viewer, $subject, $type, $body, array('privacies' => $privacies));

				// Try to attach if necessary
				if ($action && $attachment) {
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
				}
				
				if($action && $video){
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
					
					// Rebuild privacy
		            $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
		            
		            foreach ($actionTable->getActionsByObject($video) as $action)
		            {
		                $actionTable -> resetActivityBindings($action);
		            }
				}
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
			// This should be caught by error handler
		}
		
		if($video){
			return array(
				'upload_video'=>1,
				'video'=>$video->toArray()
			);
		}
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

		// save add friend
		$arr_friends = $friendValues;
		if ($arr_friends) {
			$tagfriend_table = Engine_Api::_() -> getDbtable('tagfriends', 'ynfeed');
			foreach ($arr_friends as $friendId) {
				$tagfriend = $tagfriend_table -> createRow();
				$tagfriend -> user_id = $viewer -> getIdentity();
				$tagfriend -> action_id = $action -> getIdentity();
				$tagfriend -> friend_id = $friendId;
				$tagfriend -> save();

				// send notitcation to user tagged
				$obj_item = Engine_Api::_() -> getItem('user', $friendId);
				if (!$viewer -> isSelf($obj_item)) {
					$notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
				}
			}
		}

		// save tags
		$tag_table = Engine_Api::_() -> getDbtable('tags', 'ynfeed');
		foreach ($arrTags as $item) {
			$tag = $tag_table -> createRow();
			$tag -> user_id = $viewer -> getIdentity();
			$tag -> action_id = $action -> getIdentity();
			$tag -> item_type = $item['item_type'];
			$tag -> item_id = $item['item_id'];
			$tag -> save();

			// send notitcation to user tagged
			$obj_item = Engine_Api::_() -> getItem($item['item_type'], $item['item_id']);
			if ($item['item_type'] == 'user' && !$viewer -> isSelf($obj_item) && !in_array($item['item_id'], $arr_friends)) {
				$notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
			}
		}

		// save hash tags
		$hashtag_table = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed');
		foreach ($arrHashTags as $item) {
			$hashtag = $hashtag_table -> createRow();
			$hashtag -> user_id = $viewer -> getIdentity();
			$hashtag -> action_id = $action -> getIdentity();
			$hashtag -> action_type = $action -> type;
			$hashtag -> hashtag = $item;
			$hashtag -> save();
		}
		// checkin
		if ($checkin_lat && $checkin_long && $checkinValue) {
			if ($action) {
				$map_table = Engine_Api::_() -> getDbTable("maps", "ynfeed");
				$map = $map_table -> createRow();
				$map -> title = $checkinValue;
				$map -> latitude = $checkin_lat;
				$map -> longitude = $checkin_long;
				$map -> user_id = $viewer -> getIdentity();
				$map -> action_id = $action -> getIdentity(); ;
				$map -> save();
				
				if(!$attachment)
				{
					// CREATE AUTH STUFF HERE
					$roles = array(
						'owner',
						'owner_member',
						'owner_member_member',
						'owner_network',
						'registered',
						'everyone'
					);
					$auth = Engine_Api::_() -> authorization() -> context;
					$viewMax = array_search('everyone', $roles);
			
					foreach ($roles as $i => $role)
					{
						$auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
					}
				
					Engine_Api::_()->getDbtable('actions', 'activity') -> attachActivity($action, $map);
				}
			}
		}
		return Ynmobile_AppMeta::_export_one($action, array('detail'));
	}


    /**
     * Input data:
     * + sContent: string, required.
     * + iSubjectId: int, optional.
     * + sSubjectType: string, optional. (user/group)
     * + aAttachment: array optional.
     *
     * Output data:
     * + error_code: int.
     * + error_message: string.
     * + iActionId: int.
     * + iUserId: int.
     * + sUsername: string.
     * + UserProfileImg_Url: string.
     * + sFullName: string.
     * + bCanPostComment: bool.
     * + sTime: string.
     * + sTimeConverted: string.
     * + sActionType: string.
     * + iItemId: int.
     * + sItemTitle: item title of the object (album, music, user,..)
     * + sItemType: object type (ex: music,...)
     * + bIsLike: bool.
     * + sContent: string
     * + iShare: (shareable: 1, 2, 3, 4)
     *
     * @see Mobile - API SE/Api V1.0.
     * @see feed/post
     *
     * @param array $aData
     * @return array
     */
    public function post_old($aData)
    {
    	/**
		 * tagged_users:
			tagged_groups:
			body_html:test%20what%20happend
			body:test what happend
			return_url:/mobile/se460/members/home
			token:50b3ba2ad77234bd9a05d9e326f1a200
			friendValues:119,249
			friends:
			checkin_lat:
			checkin_long:
			checkinValue:Lac Long Quan Clinic, phường 8, Ho Chi Minh, Vietnam
		 */
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this action!")
            );
        }
        extract($aData, EXTR_SKIP);
        if (!isset($sContent))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $subject = null;
        if (!empty($sSubjectType) && !empty($iSubjectId))
        {
            $subject = Engine_Api::_() -> getItem($sSubjectType, $iSubjectId);
        }
        // Use viewer as subject if no subject
        if (null === $subject)
        {
            $subject = $viewer;
        }
		
		$body = $sContent;
		
		$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
		
		$privacies = array(
						'general' => $aData['SPRI_GE'],
						'friend_list' => $aData['SPRI_FL'],
						'network' => $aData['SPRI_NE'],
						'group' => $aData['SPRI_GR'],
						'friend' => $aData['SPRI_FR']
						);
		$arrTags = array();
		$arrHashTags = array();
		$url = Ynmobile_Helper_Base::getBaseUrl();
		
		if (isset($aData['body_html']) && $aData['body_html'] != '') {
			$body = $aData['body_html'];
			$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');

			// Tags
			$pattern = '/#tags@\w+@\d+@/';
			preg_match_all($pattern, $body, $matches);
			$matches = $matches[0];

			foreach ($matches as $match) {
				$pattern2 = '/#tags@(\w+)@(\d+)@/';
				preg_match_all($pattern2, $match, $temp_matches);
				$type = $temp_matches[1][0];
				$type = substr($type, 3);
				$item_id = $temp_matches[2][0];

				$arrTags[] = array('item_type' => $type, 'item_id' => $item_id);

				$item = Engine_Api::_() -> getItem($type, $item_id);
				$href = "";
				if ($item) {
					$href = $item -> getHref();
				}
				$body = str_replace($match, $href, $body);
			}

			// Hashtags
			$pattern = '/#hashtags@(\w+)@/';
			preg_match_all($pattern, $body, $matches);
			$matches = $matches[0];
			foreach ($matches as $match) {
				$pattern2 = '/#hashtags@(\w+)/';
				preg_match_all($pattern2, $match, $temp_matches);
				$hashtag = $temp_matches[1][0];

				$arrHashTags[] = $hashtag;

				$href = "javascript:ynfeedFilter('hashtag', '" . $hashtag . "')";
				$body = str_replace($match, $href, $body);
			}
		}
		
		
		$body = urldecode($body);
		$body = strip_tags($body, '<a><br>');
		$aData['body'] = $body;
		
	
	
        // $body = html_entity_decode($sContent, ENT_QUOTES, 'UTF-8');
        
        // set up action variable
        $action = null;
        // Process
        $db = Engine_Api::_() -> getDbtable('actions', 'activity') -> getAdapter();
        $db -> beginTransaction();

        try
        {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $aAttachment;
            
            if (!empty($attachmentData) && !empty($attachmentData['type']))
            {
                $type = $attachmentData['type'];
                if($type != 'photo')
                {
                    $config = null;
                    foreach (Zend_Registry::get('Engine_Manifest') as $data)
                    {
                        if (!empty($data['composer'][$type]))
                        {
                            $config = $data['composer'][$type];
                        }
                    }
                    if (!empty($config['auth']) && !Engine_Api::_() -> authorization() -> isAllowed($config['auth'][0], null, $config['auth'][1]))
                    {
                        $config = null;
                    }
                    if ($config)
                    {
                        $plugin = Engine_Api::_() -> loadClass($config['plugin']);
                        $method = 'onAttach' . ucfirst($type);
                        $attachment = $plugin -> $method($attachmentData);
                    }
                }
            }

			
			$body = preg_replace('/<br[^<>]*>/', "\n", $body);
			$body = str_replace('../', '', $body);
			$baseUrl = $this->finalizeUrl('/');
	
			foreach (Engine_Api::_() -> ynfeed() -> getEmoticons() as $emoticon) {
				$body = str_replace($emoticon -> text, "<img src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}'/>", $body);
			}

            // Special case: status
            if (!$attachment && $viewer -> isSelf($subject) && $type != 'photo')
            {
                if ($body != '')
                {
                    $viewer -> status = $body;
                    $viewer -> status_date = date('Y-m-d H:i:s');
                    $viewer -> save();

                    $viewer -> status() -> setStatus($body);
                }
                $action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $subject, 'status', $body);
            }
            else
            {
                // General post
                $type = 'post';
                if ($viewer -> isSelf($subject))
                {
                    $type = 'post_self';
                }
                // Add notification for <del>owner</del> user
                $subjectOwner = $subject -> getOwner();

                if (!$viewer -> isSelf($subject) && $subject instanceof User_Model_User)
                {
                    $notificationType = 'post_' . $subject -> getType();
                    Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($subjectOwner, $viewer, $subject, $notificationType, array('url1' => $subject -> getHref(), ));
                }

                // Add activity
                $action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $subject, $type, $body);
                
                if($attachmentData['type'] != 'photo')
                {
                    // Try to attach if necessary
                    if ($action && $attachment)
                    {
                        Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
                    }
                }
                else 
                {
                    if ($action && !empty($attachmentData['photo_id']))
                    {
                        $count = 0;
                        $ids = explode(',', $attachmentData['photo_id']);
                        foreach($ids as $photo_id)
                        {
                            if (Engine_Api::_() -> hasModuleBootstrap('advalbum'))
                                $photo = Engine_Api::_()->getItem("advalbum_photo", $photo_id);
                            else
                                $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                                
                            if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) 
                            {
                                continue;
                            }
                            if($count < 8 )
                    {
                                Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                            }
                            $count ++;
                        }
                    }
                }
                
            }
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
		
		if(Engine_Api::_()->hasModuleBootstrap('ynfeed')){
			// add hashtag & friend tags.
			try{
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
	
				// save add friend
				$arr_friends = $friendValues;
				if ($arr_friends) {
					$tagfriend_table = Engine_Api::_() -> getDbtable('tagfriends', 'ynfeed');
					foreach ($arr_friends as $friendId) {
						$tagfriend = $tagfriend_table -> createRow();
						$tagfriend -> user_id = $viewer -> getIdentity();
						$tagfriend -> action_id = $action -> getIdentity();
						$tagfriend -> friend_id = $friendId;
						$tagfriend -> save();
		
						// send notitcation to user tagged
						$obj_item = Engine_Api::_() -> getItem('user', $friendId);
						if (!$viewer -> isSelf($obj_item)) {
							$notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
						}
					}
				}
		
				// save tags
				$tag_table = Engine_Api::_() -> getDbtable('tags', 'ynfeed');
				foreach ($arrTags as $item) {
					$tag = $tag_table -> createRow();
					$tag -> user_id = $viewer -> getIdentity();
					$tag -> action_id = $action -> getIdentity();
					$tag -> item_type = $item['item_type'];
					$tag -> item_id = $item['item_id'];
					$tag -> save();
		
					// send notitcation to user tagged
					$obj_item = Engine_Api::_() -> getItem($item['item_type'], $item['item_id']);
					if ($item['item_type'] == 'user' && !$viewer -> isSelf($obj_item) && !in_array($item['item_id'], $arr_friends)) {
						$notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
					}
				}
		
				// save hash tags
				$hashtag_table = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed');
				foreach ($arrHashTags as $item) {
					$hashtag = $hashtag_table -> createRow();
					$hashtag -> user_id = $viewer -> getIdentity();
					$hashtag -> action_id = $action -> getIdentity();
					$hashtag -> action_type = $action -> type;
					$hashtag -> hashtag = $item;
					$hashtag -> save();
				}
				// checkin
				if ($checkin_lat && $checkin_long && $checkinValue) {
					if ($action) {
						$map_table = Engine_Api::_() -> getDbTable("maps", "ynfeed");
						$map = $map_table -> createRow();
						$map -> title = @$checkinValue;
						$map -> latitude = @$checkin_lat;
						$map -> longitude = @$checkin_long;
						$map -> user_id = $viewer -> getIdentity();
						$map -> action_id = $action -> getIdentity(); ;
						$map -> save();
						
						if(!$attachment)
						{
							// CREATE AUTH STUFF HERE
							$roles = array(
								'owner',
								'owner_member',
								'owner_member_member',
								'owner_network',
								'registered',
								'everyone'
							);
							$auth = Engine_Api::_() -> authorization() -> context;
							$viewMax = array_search('everyone', $roles);
					
							foreach ($roles as $i => $role)
							{
								$auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
							}
						
							Engine_Api::_()->getDbtable('actions', 'activity') -> attachActivity($action, $map);
						}
					}
				}
			}catch(Exception $ex){
				
			}
		}
        
		
		return Ynmobile_AppMeta::_export_one($action, array('detail'));
    }


   
    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + sContent: string optional.
     *
     * Output data:
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see feed/share
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function share($aData)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to share this item!")
            );
        }
        $sContent = isset($aData['sContent']) ? $aData['sContent'] : '';
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        
        if ($sItemType != 'feed' && $sItemType != 'activity_action')
        {
            if (!$sItemType || !$iItemId)
            {
                return array(
                        'error_code' => 1,
                        'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Item Type or Item Id")
                );
            }
            
            $attachment = Engine_Api::_() -> getItem($sItemType, $iItemId);
        }
        else
        {
            $action = Engine_Api::_()->getItem('activity_action', $aData['iItemId']);
            if ($action->attachment_count)
            {
                $attachments = $action->getAttachments();
                $attachment = $attachments[0];
                $attachment = $attachment->item;
            }
            else
            {
                $attachment = $action;
            }
            
        }

        if (!$attachment)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _('You cannot share this item.')
            );
        }
        // Process
        $db = Engine_Api::_() -> getDbtable('actions', 'activity') -> getAdapter();
        $db -> beginTransaction();

        try
        {
            // Get body
            $body = $sContent;
            // Set Params for Attachment
            $params = array('type' => '<a href="' . $attachment -> getHref() . '">' . $attachment -> getMediaType() . '</a>', );

            // Add activity
            $api = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $api -> addActivity($viewer, $attachment -> getOwner(), 'share', $body, $params);
            if ($action)
            {
                $api -> attachActivity($action, $attachment);
            }
            $db -> commit();

            // Notifications
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            // Add notification for owner of activity (if user and not viewer)
            if ($action -> subject_type == 'user' && $attachment -> getOwner() -> getIdentity() != $viewer -> getIdentity())
            {
                $notifyApi -> addNotification($attachment -> getOwner(), $viewer, $action, 'shared', array('label' => $attachment -> getMediaType(), ));
            }
            return array(
                'result' => 1,
                'message' => Zend_Registry::get('Zend_Translate') -> _('Sharing successfully!')
            );
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => $e->getMessage()
            );
        }
    }

    
 /**
  * + iMinId: int, required.
    * + iItemId: int, optional.
    * + sItemType: string, optional.
  * Output data:
  * + iTotalFeedUpdate: int
  */
  public function getupdate($aData)
    {
        if (!isset($aData['iMinId']))
        {
            return array(
                'error_code' => 1,
                'error_element' => 'iMinId',
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("iMinId is not valid!")
            );
        }
        $feeds = $this->get($aData);
        return array('iTotalFeedUpdate' => count($feeds));
    }
}
    