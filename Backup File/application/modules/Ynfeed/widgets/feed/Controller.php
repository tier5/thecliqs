<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynfeed
 * @copyright  Copyright 2014 YouNet Company
 * @author     YouNet Company
 */
class Ynfeed_Widget_FeedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();
		$subject = null;
		if (Engine_Api::_() -> core() -> hasSubject()) {
			// Get subject
			$subject = Engine_Api::_() -> core() -> getSubject();
			if (!$subject -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> setNoRender();
			}
		}
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'ynfeed');

		$this -> view -> friendUsers = $friendUsers = Engine_Api::_() -> ynfeed() -> getViewerFriends($viewer);

		// Get some settings from Ynfeed settings
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> autoUpdate = $settings -> getSetting('ynfeed.autoupdate', true);
		$liveUpdateValue = $settings -> getSetting('ynfeed.liveupdatevalue', 2);
		$liveUpdatePeriod = $settings -> getSetting('ynfeed.liveupdateperiod', 'm');

		$updateSettings = 0;
		if ($liveUpdateValue) {
			switch ($liveUpdatePeriod) {
				case 'm' :
					$updateSettings = $liveUpdateValue * 60000;
					break;

				case 'h' :
					$updateSettings = $liveUpdateValue * 60 * 60000;
					break;
			}
		}
		$this -> view -> updateSettings = $updateSettings;
		$this -> view -> autoLoadMore = $autoLoadMore = $settings -> getSetting('ynfeed.autoloadfeed', true);

		// Get some options
		$countAutoload = $request -> getParam('countAutoload', 0);
		$this -> view -> countAutoload = $countAutoload + 1;
		$this -> view -> feedOnly = $feedOnly = $request -> getParam('feedOnly', false);
		$this -> view -> length = $length = $request -> getParam('limit', $settings -> getSetting('activity.length', 15));
		if($autoLoadMore)
		{
			$this -> view -> max_times =  $settings -> getSetting('ynfeed.length', 5);
		}
		$this -> view -> itemActionLimit = $itemActionLimit = $settings -> getSetting('activity.userlength', 5);

		$this -> view -> viewAllLikes = $request -> getParam('viewAllLikes', $request -> getParam('show_likes', false));
		$this -> view -> viewAllComments = $request -> getParam('viewAllComments', $request -> getParam('show_comments', false));
		$this -> view -> getUpdate = $request -> getParam('getUpdate');
		$this -> view -> checkUpdate = $request -> getParam('checkUpdate');
		$this -> view -> action_id = $action_id = (int)$request -> getParam('action_id');
		$this -> view -> post_failed = (int)$request -> getParam('pf');
         // Open comment hidden
        $this -> view -> openHide = $openHide = $request -> getParam('openHide', 0);
		if ($feedOnly) {
			$this -> getElement() -> removeDecorator('Title');
			$this -> getElement() -> removeDecorator('Container');
		}
		if ($length > 50) {
			$this -> view -> length = $length = 50;
		}
		
		$default_firstid = null;
    	$listTypeFilter = array();
		$this -> view -> enableContentTabs = true;
		$this->view->actionFilter = $actionFilter = $request->getParam('actionFilter', 'all');
		$this->view->isFromTab = $request->getParam('isFromTab', false);
		$this -> view -> filterValue = $filterValue = $request -> getParam('filterValue', '');
		
		if (!$feedOnly && empty($subject)) 
		{
			// Start filter tabs
			$this -> view -> contentTabMax = $settings -> getSetting('ynfeed.defaultvisible', 7); 
			$enableContentTabs = 0;
			
			$contentTabs = Engine_Api::_()->getDbtable('contents', 'ynfeed')->getContentList(array('show' => 1, 'content_tab' => 1));
			$defaultcontentTab = $request->getParam('actionFilter');
			$i = 0;
			$friendListIcon = $networkIcon = $customIcon = "";
			$friendContent = Engine_Api::_()->getDbtable('contents', 'ynfeed')->getContents(array('filter_type' => 'member_list'));
			if($friendContent)
				$friendListIcon = $friendContent -> getPhotoUrl();
			$networkContent = Engine_Api::_()->getDbtable('contents', 'ynfeed')->getContents(array('filter_type' => 'only_network'));
			if($networkContent)
				$networkIcon = $networkContent -> getPhotoUrl();
			$customContent = Engine_Api::_()->getDbtable('contents', 'ynfeed')->getContents(array('filter_type' => 'custom_list'));
			if($customContent)
				$customIcon = $customContent -> getPhotoUrl();
			
			foreach ($contentTabs as $value) 
			{
				if (empty($viewer_id) && in_array($value->filter_type, array('membership', 'user_saved', 'user_follow')))
          			continue;
				$filterTabs[$i]['filter_type'] = $value->filter_type;
        		$filterTabs[$i]['tab_title'] = $this -> view -> translate($value->resource_title);
        		$filterTabs[$i]['list_id'] = $value->content_id;
				$icon_url = $value -> getPhotoUrl();
				$filterTabs[$i]['icon_url'] = $icon_url;
        		$i++;
				if (empty($defaultcontentTab)) {
		          $defaultcontentTab = $value->filter_type;
		        }
			}
			if ($defaultcontentTab) 
			{
		        $this->view->actionFilter = $defaultcontentTab;
		        if ($defaultcontentTab != 'all')
		          $default_firstid = $actionTable->select()->from($actionTable, 'action_id')->order('action_id DESC')->limit(1)->query()->fetchColumn();
		    }
			
			// Filter by networks
			$enableNetworkListFilter = $settings->getSetting('ynfeed.networklist.filtering', 0);
		    if ($viewer_id && $enableNetworkListFilter) 
		    {
		        $networkLists = Engine_Api::_()->ynfeed()->getNetworks($enableNetworkListFilter, $viewer);
		        $countNetworkLists = count($networkLists);
		        if ($countNetworkLists) {
		          if (count($filterTabs) > $this->view->contentTabMax)
		            $filterTabs[$i]['filter_type'] = "separator";
		          $i++;
		          foreach ($networkLists as $value) {
		            $filterTabs[$i]['filter_type'] = "network_list";
		            $filterTabs[$i]['tab_title'] = $value->getTitle();
		            $filterTabs[$i]['list_id'] = $value->getIdentity();
					$filterTabs[$i]['icon_url'] = $networkIcon;
		            $i++;
		          }
		        }
		    }
			
			// Filter by friend list
			$userFriendListEnable = $settings->getSetting('user.friends.lists');
			$enableFriendListFilter = $userFriendListEnable && $settings->getSetting('ynfeed.friendlist.filtering', 1);
			if ($enableFriendListFilter) 
			{
				$listTable = Engine_Api::_()->getItemTable('user_list');
       			$lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
		        $countlistsLists = count($lists);
		        if ($countlistsLists) {
		          if (count($filterTabs) > $this->view->contentTabMax)
		            $filterTabs[$i]['filter_type'] = "separator";
		          $i++;
		          foreach ($lists as $value) {
		            $filterTabs[$i]['filter_type'] = "member_list";
		            $filterTabs[$i]['tab_title'] = $value->title;
		            $filterTabs[$i]['list_id'] = $value->list_id;
					$filterTabs[$i]['icon_url'] = $friendListIcon;
		            $i++;
		          }
		        }
	        }
			
			// Filter by custom list
			$this->view->canCreateCustomList = 0;
			if ($viewer_id) 
			{
		        $this->view->canCreateCustomList = $settings->getSetting('ynfeed.customlist.filtering', 1);
		        $customTypeLists = Engine_Api::_()->getDbtable('customtypes', 'ynfeed')->getCustomTypeList(array('enabled' => 1));
		        $count = count($customTypeLists);
		        if (empty($count))
		          $this->view->canCreateCustomList = 0;
		        if ($this->view->canCreateCustomList) 
		        {
		          $customLists = Engine_Api::_()->getDbtable('lists', 'ynfeed')->getMemberOfList($viewer, 'default');
		          $countCustomLists = count($customLists);
		          if ($countCustomLists) {
		            if (count($filterTabs) > $this->view->contentTabMax) {
		              $filterTabs[$i]['filter_type'] = "separator";
		              $i++;
		            }
		            foreach ($customLists as $value) 
		            {
		              $filterTabs[$i]['filter_type'] = "custom_list";
		              $filterTabs[$i]['tab_title'] = $value->title;
		              $filterTabs[$i]['list_id'] = $value->list_id;
				 	  $filterTabs[$i]['icon_url'] = $customIcon;
		              $i++;
		            }
		          }
		        }
		    }
			$this -> view -> filterTabs = $filterTabs;
			// End filter tabs
		}

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
			'action_id' => $action_id, 
			'max_id' => (int)$request -> getParam('maxid'), 
			'min_id' => (int)$request -> getParam('minid'), 
			'limit' => (int)$length,
			'showTypes' => $actionTypeFilters,
			'actionFilter' => $actionFilter, 
			'filterValue' => $filterValue,
			'listTypeFilter' => $listTypeFilter);

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
		{
			$hideItems = Engine_Api::_() -> getDbtable('hide', 'ynfeed') -> getHideItemByMember($viewer);
		}
		if ($default_firstid) {
        	$firstid = $default_firstid;
      	}
		
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
					if(@$action -> subject_type == 'ynbusinesspages_business' && !Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
					{
						continue;
					}
					if (!@$action -> getSubject() || !@$action -> getSubject() -> getIdentity())
						continue;

					if (!@$action -> getObject() || !@$action -> getObject() -> getIdentity())
						continue;

					// skip the hide actions and content
					if (!empty($hideItems) && !$action_id) 
					{
						if (isset($hideItems[$action -> getType()]) && in_array($action -> getIdentity(), $hideItems[$action -> getType()])) {
							continue;
						}

						if ($action -> getSubject() -> getType() == 'user' && isset($hideItems[$action -> getSubject() -> getType()]) && in_array($action -> getSubject() -> getIdentity(), $hideItems[$action -> getSubject() -> getType()])) 
						{
							continue;
						}
						
						if ($action -> getSubject() -> getType() == 'ynbusinesspages_business' && isset($hideItems[$action -> getSubject() -> getType()]) && in_array($action -> getSubject() -> getIdentity(), $hideItems[$action -> getSubject() -> getType()])) 
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
						$activity[] = $action;
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
		
		if (count($activity) == 0){
            $this->view->endOfFeed = true;
            $this->view->noFeed = true;
        } else {
            $this->view->noFeed = false;
        }
		
		$this -> view -> activity = $activity;
		$this -> view -> activityCount = count($activity);
		$this -> view -> nextid = $nextid;
		$this -> view -> firstid = $firstid;
		$this -> view -> endOfFeed = $endOfFeed;

		// Get some other info
		if (!empty($subject)) {
			$this -> view -> subjectGuid = $subject -> getGuid(false);
		}

		$this -> view -> enableComposer = false;
		if ($viewer -> getIdentity()) {
			if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject -> isSelf($viewer))) {
				if (Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'user', 'status')) {
					$this -> view -> enableComposer = true;
				}
			} else if ($subject) {
				if (Engine_Api::_() -> authorization() -> isAllowed($subject, $viewer, 'comment')) {
					$this -> view -> enableComposer = true;
				}
			}
		}
		// Assign the composing values
		$composePartials = array();
		foreach (Zend_Registry::get('Engine_Manifest') as $data) {
			if (empty($data['composer'])) {
				continue;
			}
			foreach ($data['composer'] as $type => $config) {
				if (!empty($config['auth']) && !Engine_Api::_() -> authorization() -> isAllowed($config['auth'][0], null, $config['auth'][1])) {
					continue;
				}
				$composePartials[] = $config['script'];
			}
		}
		$this -> view -> composePartials = $composePartials;

		// Add javascript
		$headScript = new Zend_View_Helper_HeadScript();

		$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/core.js');
		$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yncomposer.js');
        
        //update to support Video 487
        $headScript -> appendFile( 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
       
		$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynfeed.js');

		if ($viewer -> getIdentity()) 
		{
			// Support tags
			$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yntag.js');
			$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddfriend.js');
			$this -> view -> hasTag = true;
			
			// Checkin JS
			$headScript -> appendFile('//maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places');
			$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yncheckin.js');
		}

		// Auto scroll
		$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/scrollspy.1.2.js');

		if($viewer && ($subject && $viewer -> isSelf($subject) 
		  || !$subject
		  || ($subject && in_array($subject -> getType(), array('event', 'group', 'ynbusinesspages_business')))
		  ))
		{
			// Add privacy
			$headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddprivacies.js');
			$this -> view -> hasPrivacy = true;
		}
		
		if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
		{
			  $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddbusiness.js');
		}

		// Form token
		$session = new Zend_Session_Namespace('ActivityFormToken');
		if (empty($session -> token)) {
			$this -> view -> formToken = $session -> token = md5(time() . $viewer -> getIdentity() . get_class($this));
		} else {
			$this -> view -> formToken = $session -> token;
		}
		
		//Fix for Vincent
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'ynfeed');
		$Name = $actionTable -> info('name');
		$select = $actionTable -> select() -> from($Name, array('action_id')) -> order('action_id DESC') -> limit(1);
		$result = $actionTable -> fetchRow($select);
		$this -> view -> last_id_pva = $result -> action_id;
		$this -> view -> firstid = $result -> action_id;
	}

}
