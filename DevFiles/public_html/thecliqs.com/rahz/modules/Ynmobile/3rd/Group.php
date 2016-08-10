<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Group.php $
 * @author     LONGL
 */

class Ynmobile_Api_Group extends Core_Api_Abstract
{
	CONST ADV = TRUE;
	CONST GROUP_MEMBER_COUNT = 6;
	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iLastGroupId: int, optional.
	 *
	 * Output data:
	 * + iGroupId: int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + bCanPostComment: bool.
	 * + sGroupImageUrl: string.
	 * + sGroupBigImageUrl: string.
	 * + iUserId: int.
	 * + sUserName: string.
	 * + sUserImageUrl: string.
	 * + iMemberCount: int.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see group/fetch
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function fetch($aData)
	{
		extract($aData);
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		
		$groupTbl = Engine_Api::_()->getItemTable('group');
		$params = array();
		if ( (isset($iUserId) && !empty($iUserId)) || ($sView === "my") )
		{
			$select = $this->getMyGroupSelect($aData);
		}
		else
		{
			$params['search'] = 1;
			if (isset($sSearch))
			{
				$params['text'] = $sSearch;
			}
			if (isset($iCategoryId) && is_numeric($iCategoryId) && $iCategoryId > 0)
			{
				$params['category_id'] = $iCategoryId;
			}
			if (isset($sOrderBy) && in_array($sOrderBy, array('recent', 'popular')))
			{
				if($sOrderBy === 'popular')
				{
					$params['order'] = "member_count";
					$params['direction'] = "DESC";
				}
			}
			$select = $groupTbl->getGroupSelect($params);
		}
		$groups = Zend_Paginator::factory($select);
		$groups->setCurrentPageNumber($iPage);
		$groups->setDefaultItemCountPerPage($iLimit);
		$totalPage = (integer) ceil($groups->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
		return $this -> parserGroups($groups);
	}
	
	
	/**
	 * Input data:
	 * groups: array()
	 *
	 * Output data:
	 * + iGroupId: int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + bCanPostComment: bool.
	 * + sGroupImageUrl: string.
	 * + sGroupBigImageUrl: string.
	 * + iUserId: int.
	 * + sUserName: string.
	 * + sUserImageUrl: string.
	 * + iMemberCount: int.
	 *
	 * @param array $aData
	 * @return array
	 */
	private function parserGroups($groups)
	{
		$arr_results = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		foreach ($groups as $group)
		{
			$bCanPostComment = Engine_Api::_() -> authorization() -> isAllowed($group, null, 'comment');
				
			$sGroupImageUrl = $group -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			$sGroupBigImageUrl = $group -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
				
			if ($sGroupImageUrl != "")
			{
				$sGroupImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sGroupImageUrl);
				$sGroupBigImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sGroupBigImageUrl);
			}
			else
			{
				$sGroupImageUrl = NO_GROUP_NORMAL;
				$sGroupBigImageUrl = NO_GROUP_PROFILE;
			}
			$user = $group -> getOwner();
			$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$category = $group->getCategory();
			$arr_results[] = array(
					'iGroupId' => $group -> getIdentity(),
					'sTitle' => $group -> getTitle(),
					'sDescription' => $group -> description,
					'sCategory' => (is_object($category)) ? $category->getTitle() : "",
					'sGroupImageUrl' => $sGroupImageUrl,
					'sGroupBigImageUrl' => $sGroupBigImageUrl,
					'bCanPostComment' => $bCanPostComment,
					'iOwnerId' => $user -> getIdentity(),
					'sOwnerName' => $user -> getTitle(),
					'sOwnerImageUrl' => $sUserImageUrl,
					'iMemberCount' => $group->member_count,
					'aMembers' => $this->getMembers($group),
					'aActionData' => $this->getGroupStateByUser($group),
			);
		}
		return $arr_results;
	}
	
	protected function getMembers($group)
	{
		$members = $group->membership()->getMembers();
		if (!count($members))
		{
			return array();
		}
		$aMembers = array();
		$i = 0;
		foreach ($members as $member)
		{
			if ($i == self::GROUP_MEMBER_COUNT) break;
			$sMemberImageUrl = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sMemberImageUrl != "")
			{
				$sMemberImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sMemberImageUrl);
			}
			else
			{
				$sMemberImageUrl = NO_USER_ICON;
			}
			$aMembers[] = array(
					'iUserId' => $member -> getIdentity(),
					'sUserName' => $member -> getTitle(),
					'sUserImage' => $sMemberImageUrl
			);
			$i++;
		}
		return $aMembers;
	}
	
	protected function getOfficers($group)
	{
		$list = $group->getOfficerList();
		$paginator = $list->getChildPaginator();
		$paginator->setItemCountPerPage(self::GROUP_MEMBER_COUNT);
		$paginator->setCurrentPageNumber(1);
		$aOfficers = array();
		foreach ($paginator as $member)
		{
			$sMemberImageUrl = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sMemberImageUrl != "")
			{
				$sMemberImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sMemberImageUrl);
			}
			else
			{
				$sMemberImageUrl = NO_USER_ICON;
			}
			$aOfficers[] = array(
					'iUserId' => $member -> getIdentity(),
					'sUserName' => $member -> getTitle(),
					'sUserImage' => $sMemberImageUrl
			);
		}
		return $aOfficers;
	}
	
	
	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iLastGroupId: int, optional.
	 *
	 * Output data:
	 * + iGroupId: int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + bCanPostComment: bool.
	 * + sGroupImageUrl: string.
	 * + sGroupBigImageUrl: string.
	 * + iUserId: int.
	 * + sUserName: string.
	 * + sUserImageUrl: string.
	 * + iMemberCount: int.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see group/my
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function my($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$aData['user_id'] = $viewer->getIdentity(); 
		return $this -> fetch($aData);
	}
	
	public function getMyGroupSelect($aData)
	{
		extract($aData);
		if (isset($iUserId))
		{
			$viewer = Engine_Api::_()->user()->getUser($iUserId);
		}
		else
		{
			$viewer = Engine_Api::_()->user()->getViewer();
		}
		
		$membership = (self::ADV)
			? Engine_Api::_()->getDbtable('membership', 'advgroup')
			: Engine_Api::_()->getDbtable('membership', 'group');
		
		$select = $membership->getMembershipsOfSelect($viewer);
		$select->where('group_id IS NOT NULL');
		
		$table = Engine_Api::_()->getItemTable('group');
		$tName = $table->info('name');
		
		if (isset($sSearch) && !empty($sSearch))
		{
			$select->where(
					$table->getAdapter()->quoteInto("`{$tName}`.`title` LIKE ?", '%' . $sSearch . '%') . ' OR ' .
					$table->getAdapter()->quoteInto("`{$tName}`.`description` LIKE ?", '%' . $sSearch . '%')
			);
		}
		if (isset($iCategoryId) && is_numeric($iCategoryId) && $iCategoryId > 0)
		{
			$select->where("`{$tName}`.`category_id` = ?", $iCategoryId);
		}
		if (isset($sOrderBy) && in_array($sOrderBy, array('recent', 'popular')))
		{
			if($sOrderBy === 'popular')
			{
				$select->order("member_count DESC");
			}
			else
			{
				$select->order("creation_date DESC");
			}
		}
		return $select;
		
	}
	
	
	/**
	 * Input data: NA
	 *
	 * Output data:
	 * + array
	 * 
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see group/category
	 *
	 * @global string $token
	 * @return array
	 */
	public function category()
	{
		$categories = (self::ADV) 
			? Engine_Api::_()->getDbtable('categories', 'advgroup')->getCategoriesAssoc()
			: Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
		
		asort($categories, SORT_LOCALE_STRING);
		$categoryOptions = array();
		foreach ($categories as $k => $v)
		{
			if ($v === "")
			{
				continue;
			}
			$categoryOptions[] = array(
				'iId' => $k,
				'sName' => $v
			);
		}
		return $categoryOptions;
	}
	
	
	/**
	 * Input data: NA
	 *
	 * Output data:
	 * + array
	 *
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see group/privacies
	 *
	 * @global string $token
	 * @return array
	 */
	public function privacies($aData)
	{
		if (!isset($aData['sType']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("sType is empty!"),
				'result' => 0
			);
		}
		
		if (!in_array($aData['sType'], array('view', 'comment', 'photo', 'event')))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sType is not valid!"),
					'result' => 0
			);
		}
		
		$user = Engine_Api::_()->user()->getViewer();
		
		// Privacy
		$availableLabels = array(
				'everyone' => 'Everyone',
				'registered' => 'Registered Members',
				'member' => 'All Group Members',
				'officer' => 'Officers and Owner Only',
		);
		
		$privacyOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_'.$aData['sType']);
		$privacyOptions = array_intersect_key($availableLabels, array_flip($privacyOptions));
		
		$privacies = array();
		
		foreach ($privacyOptions as $k => $v)
		{
			$privacies[] = array(
					'sValue' => $k,
					'sPhrase' => $v
			);
		}
		
		return $privacies;
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/delete
	 *
	 * @param array $aData
	 * @return array
	 */
	public function delete($aData)
	{
		if (!isset($aData['iGroupId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!"),
					'result' => 0
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItem('group', $aData['iGroupId']);
		
		$db = $group->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			$group->delete();
			$db->commit();
			return array(
					'error_code' => 0,
					'message' => Zend_Registry::get('Zend_Translate') -> _('Deleted group successfully.')
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	protected  function saveGroup($aData)
	{
		extract($aData);
		//Only check these things when creating new group situation
		if (!isset($iGroupId))
		{
			if (!isset($sTitle))
			{
				return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("sTitle is empty or not valid!"),
						'result' => 0
				);
			}
		}
		
		$values = array();
		$values['title'] = $sTitle;
		$values['description'] = isset($sDescription) ? html_entity_decode($sDescription, ENT_QUOTES, 'UTF-8') : "";
		$values['category_id'] = isset($iCategoryId) ? $iCategoryId : 0;
		$values['search'] = ( isset($iSearch) && ($iSearch == 0 || $iSearch == 1) ) ? $iSearch : 1;
		$values['approval'] = ( isset($iApproval) && ($iApproval == '1') ) ? 1 : 0;
		
		$roles = array('officer', 'member', 'registered', 'everyone');
		
		$values['auth_invite'] = ( isset($sAuthInvite) && in_array($sAuthInvite, array('member', 'officer')) ) ? $sAuthInvite : 'member'; //'member', 'officer'
		$values['auth_view'] = ( isset($sAuthView) && in_array($sAuthView, $roles) ) ? $sAuthView : 'everyone'; //'officer', 'member', 'registered', 'everyone'
		$values['auth_comment'] = ( isset($sAuthComment) && in_array($sAuthComment, $roles) ) ? $sAuthComment : 'member'; //'officer', 'member', 'registered', 'everyone'
		$values['auth_photo'] = ( isset($sAuthPhoto) && in_array($sAuthPhoto, $roles) ) ? $sAuthPhoto : 'member'; //'officer', 'member', 'registered', 'everyone'
		$values['auth_event'] = ( isset($sAuthEvent) && in_array($sAuthEvent, $roles) ) ? $sAuthEvent : 'member'; //'officer', 'member', 'registered', 'everyone'
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$values['user_id'] = $viewer->getIdentity();
		
		$db = Engine_Api::_()->getItemTable('group')->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			// Create group
			$table = Engine_Api::_()->getItemTable('group');
			
			if (isset($iGroupId))
				$group = $table->find($iGroupId)->current();
			else
				$group = $table->createRow();
			
			$group->setFromArray($values);
			$group->save();
		
			// Add owner as member when creating group
			if (!isset($iGroupId))
			{
				$group->membership()->addMember($viewer)
				->setUserApproved($viewer)
				->setResourceApproved($viewer);
			}
			// Process privacy
			$auth = Engine_Api::_()->authorization()->context;
		
			if( empty($values['auth_view']) ) {
				$values['auth_view'] = 'everyone';
			}
		
			if( empty($values['auth_comment']) ) {
				$values['auth_comment'] = 'everyone';
			}
		
			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$photoMax = array_search($values['auth_photo'], $roles);
			$eventMax = array_search($values['auth_event'], $roles);
			$inviteMax = array_search($values['auth_invite'], $roles);
		
			$officerList = $group->getOfficerList();
		
			foreach( $roles as $i => $role ) {
				if( $role === 'officer' ) {
					$role = $officerList;
				}
				$auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
				$auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
				$auth->setAllowed($group, $role, 'event', ($i <= $eventMax));
				$auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
			}
		
			// Create some auth stuff for all officers
			$auth->setAllowed($group, $officerList, 'photo.edit', 1);
			$auth->setAllowed($group, $officerList, 'topic.edit', 1);
		
			// Add auth for invited users
			$auth->setAllowed($group, 'member_requested', 'view', 1);
		
			// Add action
			if (!isset($iGroupId))
			{
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($viewer, $group, 'advgroup_create');
				if( $action ) {
					$activityApi->attachActivity($action, $group);
				}
			}
		
			// Commit
			$db->commit();
			
			// Adding group photo
			if(!empty($_FILES['image']))
			{
				$group = Engine_Api::_()->ynmobile()->setGroupPhoto($group, $_FILES['image']);
			}
			
			if (isset($iGroupId))
			{
				return array(
						'error_code' => 0,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Edited group successfully"),
						'iGroupId' => $group->getIdentity()
				);
			}
			else
			{
				return array(
						'error_code' => 0,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Created group successfully"),
						'iGroupId' => $group->getIdentity()
				);
			}
		}
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _($e->getMessage() . " at " .  $e->getLine() . $e->getFile()),
			);
		}
	}
	
	
	
	/**
	 * Input data:
	 * + sTitle: string, required.
	 * + sDescription: string, required.
	 * + iCategoryId: int, optional.
	 * + sAuthInvite: string, optional, 'member'/'officer'
	 * + sAuthView: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthComment: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthPhoto: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthEvent: string, optional,'officer'/'member'/'registered'/'everyone'
	 * + iSearch: int, optional
	 * + iApproval: int, optional
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + iGroupId: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/create
	 *
	 * @param array $aData
	 * @return array
	 */
	public function create($aData)
	{
		if (isset($aData['iGroupId']))
		{
			unset($aData['iGroupId']);
		}
		return $this->saveGroup($aData);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + sTitle: string, required.
	 * + sDescription: string, required.
	 * + iCategoryId: int, optional.
	 * + sAuthInvite: string, optional, 'member'/'officer'
	 * + sAuthView: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthComment: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthPhoto: string, optional, 'officer'/'member'/'registered'/'everyone'
	 * + sAuthEvent: string, optional,'officer'/'member'/'registered'/'everyone'
	 * + iSearch: int, optional
	 * + iApproval: int, optional
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + iGroupId: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/edit
	 *
	 * @param array $aData
	 * @return array
	 */
	public function edit($aData)
	{
		if (!isset($aData['iGroupId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		return $this->saveGroup($aData);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + iGroupId': int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + iCategory: int.
	 * + sCategory: string.
	 * + bCanSearch: 0 or 1
	 * + bApproval: 0 or 1
	 * + bIsFeatured: 0 or 1
	 * + iMemberCount: int
	 * + iViewCount: int
	 * + bComment: 0 or 1
	 * + bUploadPhoto: 0 or 1
	 * + bCreateEvent: 0 or 1
	 * + bInvite: 0 or 1
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/view
	 *
	 * @param array $aData
	 * @return array
	 */
	public function detail($aData)
	{
		if (!isset($aData['iGroupId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		$subject = Engine_Api::_()->getItemTable('group')->findRow($aData['iGroupId']);
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// Check auth
		if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Sorry! You don't have the permission to view this file")
			);
		}
		$oAuth = $subject->authorization();
		
		// Increment view count
		if( !$subject->getOwner()->isSelf($viewer) )
		{
			$subject->view_count++;
			$subject->save();
		}
		
		$aCategories = (self::ADV) 
			? Engine_Api::_()->getDbtable('categories', 'advgroup')->getCategoriesAssoc()
			: Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
		
		$owner = $subject->getOwner();
		$sOwnerImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sOwnerImageUrl != "")
		{
			$sOwnerImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sOwnerImageUrl);
		}
		else
		{
			$sOwnerImageUrl = NO_USER_ICON;
		}
		$sGroupImageUrl = $subject -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
		$sBigGroupImageUrl = $subject -> getPhotoUrl(null);
		if ($sGroupImageUrl != "")
		{
			$sGroupImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sGroupImageUrl);
			$sBigGroupImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sBigGroupImageUrl);
		}
		else
		{
			$sBigGroupImageUrl = $sGroupImageUrl = NO_GROUP_PROFILE;
		}
		$officerList = $subject->getOfficerList();
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('officer', 'member', 'registered', 'everyone');
		foreach( $roles as $roleString )
		{
			$role = $roleString;
			if ($role == "officer")
			{
				$role = $officerList;
			}
			if( 1 === $auth->isAllowed($subject, $role, 'view') )
			{
				$sViewPrivacy = $roleString;
			}
			if( 1 === $auth->isAllowed($subject, $role, 'comment') )
			{
				$sCommentPrivacy = $roleString;
			}
		}
		$waitingMembers = Zend_Paginator::factory($subject->membership()->getMembersSelect(false));
		$topicTbl = (self::ADV)
			? Engine_Api::_()->getItemTable("advgroup_topic")
			: Engine_Api::_()->getItemTable("group_topic");
		$topicSelect = $topicTbl->select()->where('group_id = ?', $subject->getIdentity());
		$category = $subject->getCategory();
		$result = array(
				'iGroupId' => $subject->getIdentity(),
				'sTitle' => $subject->getTitle(),
				'sDescription' => $subject->description,
				'iCategoryId' => $subject->category_id,
				'sCategory' => (is_object($category)) ? $category->getTitle() : "",
				//'sCategoryName' => ($subject->category_id && $aCategories[$subject->category_id]) ? $aCategories[$subject->category_id] : '',
				'sGroupImageUrl' => $sGroupImageUrl,
				'sBigGroupImageUrl' => $sBigGroupImageUrl,
				'iOwnerId' => $owner->getIdentity(),
				'sOwnerName' => $owner->getTitle(),
				'sOwnerImageUrl' => $sOwnerImageUrl,
				'bCanSearch' => $subject->search,
				'bCanUploadPhoto' => $oAuth->isAllowed($viewer, 'photo'),
				'bCanCreateEvent' => $oAuth->isAllowed($viewer, 'event'),
				'bCanComment' => $oAuth->isAllowed($viewer, 'comment'),
				'bCanView' => $oAuth->isAllowed($viewer, 'view'),
				'bCanInvite' => $oAuth->isAllowed($viewer, 'invite'),
				'bApproval' => $subject->approval,
				'iMemberCount' => $subject->member_count,
				'iGuestCount' => $waitingMembers->getTotalItemCount(),
				'iViewCount' => $subject->view_count,
				'sViewPrivacy' => $sViewPrivacy,
				'sCommentPrivacy' => $sCommentPrivacy,
				'aOfficers' => $this->getOfficers($subject),
				'aMembers' => $this->getMembers($subject),
				'aActionData' => $this->getGroupStateByUser($subject),
				'iPhotoCount' => $subject->getSingletonAlbum()->getCollectiblesPaginator()->getTotalItemCount(),
				'iEventCount' => $this->getEventsPaginator($subject)->getTotalItemCount(),
				'iTopicCount' => Zend_Paginator::factory($topicSelect)->getTotalItemCount(),
		);
		return $result;
	}
	
	public function getEventsPaginator($group)
	{
		$table = Engine_Api::_()->getItemTable('event');
		$select = $table->select()->where('parent_type = ?', 'group');
		$select = $select->where('parent_id = ?', $group->getIdentity());
		return  Zend_Paginator::factory($select);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + iWaiting: int, optional.
	 * + iLimit: int, optional.
	 * + iLastMemberId: int, optional
	 * 
	 * Output data:
	 * + iUserId: int.
	 * + sUserName: string.
	 * + sUserImage: string. 
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/members
	 *
	 * @param array $aData
	 * @return array
	 */
	public function members($aData)
	{
		extract($aData);
		$viewer = Engine_Api::_()->user()->getViewer();
	
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
				
		// Get subject and check auth
		$group = Engine_Api::_()->getItemTable("group")->findRow($aData['iGroupId']);
		
		if( !$group -> authorization()->isAllowed($viewer, 'view') ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have the permission to view this group members.")
			);
		}
	
		// Get params
		if(!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		
		if (!isset($sSearch))
		{
			$sSearch = "";
		}
			
		if (isset($iWaiting) && $iWaiting)
		{
			$iWaiting = true;
		}
		else
		{
			$iWaiting = false;
		}
	
		$list = $group->getOfficerList();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		
		if (isset($sView) && $sView == "guest")
		{
			$select = $group->membership()->getMembersObjectSelect(false);
		}
		else
		{
			$select = $group->membership()->getMembersObjectSelect();
		}
		
		if( $sSearch ) 
		{
			$select->where('displayname LIKE ?', '%' . $sSearch . '%');
		}
		$paginator = Zend_Paginator::factory($select);
	
		// Set item count per page and current page number
		$paginator->setItemCountPerPage($iLimit);
		$paginator->setCurrentPageNumber($iPage);
	
		// Do not render if nothing to show and no search
		if( $paginator->getTotalItemCount() <= 0 && '' == $sSearch ) 
		{
			return array();
		}
	
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
		$result = array();
		foreach ($paginator as $user)
		{
			$sUserImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			if ($sUserImage != "")
			{
				$sUserImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImage);
			}
			else
			{
				$sUserImage = NO_USER_NORMAL;
			}
				
			$result[] = array(
					"iUserId" => $user->getIdentity(),
					"sUserName" => $user->getTitle(),
					"sUserImageUrl" => $sUserImage,
					"aActionData" => $this->getGroupStateByUser($group, $user)
			);
		}
		return $result;
	}
	
	
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + iLimit: int, optional.
	 * + iLastPhotoId: int, optional
	 *
	 * Output data:
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 * + sThumbUrl: string.
	 * + iAlbumId: int.
	 * + iGroupId: int.
	 * + iUserId: int.
	 * + sUserName: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/photos
	 *
	 * @param array $aData
	 * @return array
	 */
	public function photos($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing group identity!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		if (!is_object($group))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This group is not existed.")
			);
		}
		
		if( !$group->authorization()->isAllowed($viewer, 'view') ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have the permission to view this group members.")
			);
		}
		
		if(!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		
		// Get paginator
		$album = $group->getSingletonAlbum();
		//$select = $album->getCollectiblesSelect();
		$photoTbl = (self::ADV) 
			? Engine_Api::_()->getItemTable("advgroup_photo")
			: Engine_Api::_()->getItemTable("group_photo");
		
		$select = $photoTbl->select() ->where("collection_id = ?", $album->getIdentity());
      	if (isset($sOrder) && in_array($sOrder, array("recent", "most_view", "most_comment")))
      	{
      		if ($sOrder == "recent")
      			$select->order("photo_id DESC");
      		else if ($sOrder == "most_view")
      			$select->order("view_count DESC");
      		else if ($sOrder == "most_comment")
      			$select->order("comment_count DESC");
      	}
      	else
      	{
      		$select->order("photo_id DESC");
      	}
		
		$paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($iLimit);
		$paginator->setCurrentPageNumber($iPage);
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
		
		$result = array();
		foreach($paginator as $photo)
		{
			$user = $photo->getOwner();
			if (isset($iInDetails) && $iInDetails == '1')
			{
				$iTotalComment = $photo -> comments() -> getCommentPaginator() -> getTotalItemCount();
				$iTotalLike = $photo -> likes() -> getLikePaginator() -> getTotalItemCount();
				$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
				if ($sUserImageUrl != "")
				{
					$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
				}
				else
				{
					$sUserImageUrl = NO_USER_ICON;
				}
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$canComment = ($photo->authorization()->isAllowed($viewer, 'comment')) ? true : false;
				$result[] = array(
						'iPhotoId' => $photo -> getIdentity(),
						'iUserId' => $user -> getIdentity(),
						'sUserImageUrl' => $sUserImageUrl,
						'sUserName' => $user -> getTitle(),
						'sPhotoThumbUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.normal')),
						'sPhotoUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.profile')),
						'sTitle' => $photo -> getTitle(),
						'sDescription' => $photo -> description,
						'bCanPostComment' => $canComment,
						'bCanLike' => $canComment,
						'bCanComment' => $canComment,
						'bIsLiked' => $photo -> likes() -> isLike($viewer),
						'iTotalComment' => $iTotalComment,
						'iTotalLike' => $iTotalLike,
						'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
						'sItemType' => 'photo',
						'sModelType'=>'group_photo',
						'iTimeStamp' => strtotime($photo -> creation_date),
				);
			}
			else
			{
				$result[] = array(
						'iPhotoId' => $photo->getIdentity(),
						'iUserId' => $user->getIdentity(),
						'sUserName' => $user->getTitle(),
						'sUserImageUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($user->getPhotoUrl()),
						'sPhotoThumbUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.normal')),
						'sPhotoUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.profile'))
				);
			}
			
		}
		return $result;
	}

	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + iLimit: int, optional.
	 * + iLastEventId: int, optional
	 *
	 * Output data:
	 * + iEventId: int.
	 * + sTitle: string.
	 * + sEventImageUrl: string.
	 * + iUserId: int.
	 * + sUserName: string.
	 * + sUserImageUrl: string.
	 * + iStartTime: int.
	 * + sStartTime: string.
	 * + sStartFullTime: string.
	 * + iEndTime: int.
	 * + sEndTime: string.
	 * + sEndFullTime: string.
	 * + sLocation: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/events
	 *
	 * @param array $aData
	 * @return array
	 */
	public function events($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing group id!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable("group")->findRow($iGroupId);
		if( !$group->authorization()->isAllowed($viewer, 'view') ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have the permission to view this group members.")
			);
		}
		if(!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		// Get paginator
		$table = Engine_Api::_()->getItemTable('event');
		$select = $table->select()->where('parent_type = ?', 'group');
		$select = $select->where('parent_id = ?', $group->getIdentity());
		$paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($iLimit);
		$paginator->setCurrentPageNumber($iPage);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
		
		return Engine_Api::_()->getApi("Event", "Ynmobile") -> parserEvents($paginator);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + iLimit: int, optional.
	 * + iPage: int, optional
	 *
	 * Output data:
	 * + iTopicId: int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + iReplies: int.
	 * + iLastPosterId: int.
	 * + sLastPosterName: string.
	 * + sLastPosterImageUrl: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/discussions
	 *
	 * @param array $aData
	 * @return array
	 */
	public function discussions($aData)
	{
		extract($aData);
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 5;
		}
		
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable("group")->findRow($iGroupId);
		// Get paginator
		$table = (self::ADV) 
			? Engine_Api::_()->getItemTable('advgroup_topic') 
			: Engine_Api::_()->getItemTable('group_topic');
		
		$select = $table->select()
		->where('group_id = ?', $group->getIdentity())
		->order('sticky DESC')
		->order('modified_date DESC');
		
		$paginator = Zend_Paginator::factory($select);
	    $paginator->setItemCountPerPage($iLimit);
	    $paginator->setCurrentPageNumber($iPage);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
	    
	    $result = array();
	    foreach ($paginator as $topic)
	    {
	    	$lastpost = $topic->getLastPost();
	    	$lastposter = $topic->getLastPoster();
	    	$modifiedDate = strtotime($topic -> modified_date);
	    	$lastPostedDate = strtotime($lastpost->creation_date);
	    	$result[] = array(
	    			'iTopicId' => $topic->getIdentity(),
	    			'sTitle' => $topic->getTitle(),
	    			'sDescription' => '',
	    			'iViewCount' => $topic->view_count,
	    			'iReplyCount' => $topic->post_count-1,
	    			'iLastUserId' => $lastposter->getIdentity(),
	    			'sLastUserName' => $lastposter->getTitle(),
	    			'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($modifiedDate),
	    			'sLastPostedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($lastPostedDate),
	    			'iGroupId' => $aData['iGroupId'],
	    			'sGroupTitle' => $group->getTitle(),
	    			'bSticky' => $topic->sticky,
	    	);
	    }
	    return $result;
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + id: int.
	 * + sFullName: string.
	 * + UserProfileImg_Url: string
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/getinvitepeople
	 *
	 * @param array $aData
	 * @return array
	 */
	public function getinvitepeople($aData)
	{
		if (!isset($aData['iGroupId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable("group")->findRow($aData['iGroupId']);
		
		$friends = $viewer->membership()->getMembers();
		$result = array();
		foreach( $friends as $friend )
		{
			if( $group->membership()->isMember($friend, null) ) 
				continue;
			
			$sProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sProfileImage)
				$sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
			else
				$sProfileImage = NO_USER_ICON;
			
			$result[] = array(
					'iUserId' => $friend->getIdentity(),
					'sFullName' => $friend->getTitle(),
					'sUserImageUrl' => $sProfileImage
			);
		}
		return $result;
	}
	
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + aUserIds: array, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/invite
	 *
	 * @param array $aData
	 * @return array
	 */
	public function invite($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		if (!is_array($aUserIds) || count($aUserIds) == 0)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("aUserIds in not valid!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$friends = Engine_Api::_()->user()->getUserMulti($aUserIds);
		$group = Engine_Api::_()->getItemTable("group")->findRow($aData['iGroupId']);
		
		// Process
		$table = $group->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			foreach( $friends as $friend )
			{
				$group->membership()->addMember($friend)->setResourceApproved($friend);
				$notifyApi->addNotification($friend, $viewer, $group, 'advgroup_invite');
			}
			$db->commit();
			return array(
					'error_code' => 0,
					'result' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Invited friend(s) successfully!")
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function remove_member($aData)
	{
		return $this->remove($aData);
	}
	
	public function remove_invitation($aData)
	{
		return $this->remove($aData);
	}
	
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/remove
	 *
	 * @param array $aData
	 * @return array
	 */
	public function remove($aData)
	{
		extract($aData);
		
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		if (!isset($iUserId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("iUserId in not valid!")
			);
		}
	
		// Get user
		$user = Engine_Api::_()->getItem('user', $iUserId);
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		
		$list = $group->getOfficerList();
	
		if( !$group->membership()->isMember($user) ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Cannot remove a non-member")
			);
		}
	
		$db = $group->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			// Remove as officer first (if necessary)
			$list->remove($user);

			// Remove membership
			$group->membership()->removeMember($user);

			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get('Zend_Translate') -> _("Removed membership successfully!"),
					'aActionData' => $this->getGroupStateByUser($group, $user),
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/leave
	 *
	 * @param array $aData
	 * @return array
	 */
	public function leave($aData)
	{
		extract($aData);
		
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
	
		if( $group->isOwner($viewer) ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Can not leave this group! You are the group.")
			);
		}
	
		
		$list = $group->getOfficerList();
		$db = $group->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$list->remove($viewer);
			$group->membership()->removeMember($viewer);
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get('Zend_Translate') -> _("Left this group successfully."),
					'aActionData' => $this->getGroupStateByUser($group),
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/join
	 *
	 * @param array $aData
	 * @return array
	 */
	public function join($aData)
	{
		extract($aData);
		
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
	
		// If member is already part of the group
		if( $group->membership()->isMember($viewer) ) {
			$db = $group->membership()->getReceiver()->getTable()->getAdapter();
			$db->beginTransaction();
	
			try
			{
				// Set the request as handled
				$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
						$viewer, $group, 'advgroup_invite');
				if( $notification )
				{
					$notification->mitigated = true;
					$notification->save();
				}
				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are already a member of this group.")
			);
		}
	
		
		$db = $group->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$group->membership()->addMember($viewer)->setUserApproved($viewer);

			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$viewer, $group, 'advgroup_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}

			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $group, 'advgroup_join');

			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
		
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You are now a member of this group.'),
				'aActionData' => $this->getGroupStateByUser($group),
		);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/accept
	 *
	 * @param array $aData
	 * @return array
	 */
	public function accept($aData)
	{
		extract($aData);
		
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
	
		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		$db = $group->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$group->membership()->setUserApproved($viewer);
	
			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$viewer, $group, 'advgroup_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $group, 'advgroup_join');
	
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
	
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the group.'),
				'aActionData' => $this->getGroupStateByUser($group),
		);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/reject
	 *
	 * @param array $aData
	 * @return array
	 */
	public function reject($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		// Get user
		if (isset($iUserId))
		{
			$user = Engine_Api::_()->getItem('user', $iUserId);
		}
		else
		{
			$user = Engine_Api::_()->user()->getViewer();
		}
		
		// Process
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		$db = $group->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$group->membership()->removeMember($user);
	
			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$user, $group, 'advgroup_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}
	
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
		
		if (isset($iUserId))
		{
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You have rejected a membership request.'),
				'aActionData' => $this->getGroupStateByUser($group, $user),
			);
		}
		else
		{
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the group.'),
				'aActionData' => $this->getGroupStateByUser($group, $user),
			);
		}
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/reject
	 *
	 * @param array $aData
	 * @return array
	 */
	public function approve($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		if ( !isset($iUserId) || null === ($user = Engine_Api::_()->getItem('user', $iUserId)) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iUserId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = $group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$subject->membership()->setResourceApproved($user);

			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'advgroup_accepted');

			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($user, $subject, 'advgroup_join');

			$db->commit();
			$this->updateMemberCount($subject);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('Group request approved.'),
				'aActionData' => $this->getGroupStateByUser($group, $user),
		);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/request
	 *
	 * @param array $aData
	 * @return array
	 */
	public function request($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = $group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		$owner = $subject->getOwner();
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$subject->membership()->addMember($viewer)->setUserApproved($viewer);
			Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, 'advgroup_approve');
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}

		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You have requested membership successfully!'),
				'aActionData' => $this->getGroupStateByUser($subject),
		);
	}
	
	/**
	 * Input data:
	 * + iGroupId: int, required.
	 *
	 * Output data:
	 * + error_code: string.
	 * + result: string.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see group/cancel
	 *
	 * @param array $aData
	 * @return array
	 */
	public function cancel($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = $group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
		try
		{
			$subject->membership()->removeMember($viewer);

			// Remove the notification?
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$subject->getOwner(), $subject, 'advgroup_approve');
			if( $notification ) 
			{
				$notification->delete();
			}
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
			);
		}
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You have canceled a membership request successully!'),
				'aActionData' => $this->getGroupStateByUser($subject),
		);
	}
	
	
	public function view_topic($aData)
	{
		extract($aData);
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		
		if ($iPage == '0')
		{
			return array();
		}
		
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
	
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$topic =  (self::ADV)
			? Engine_Api::_()->getDbTable("topics","advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics","group")->findRow($iTopicId);
		
		$group = $topic->getParentGroup();
		$canEdit = $topic->canEdit(Engine_Api::_()->user()->getViewer());
    	$officerList = $group->getOfficerList();
		$canPost = $group->authorization()->isAllowed($viewer, 'comment');
	
		if( !$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id ) {
			$topic->view_count = new Zend_Db_Expr('view_count + 1');
			$topic->save();
		}
	
		// Check watching
		$isWatching = null;
		if( $viewer->getIdentity() ) {
			$topicWatchesTable = (self::ADV) 
				? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
				: Engine_Api::_()->getDbtable('topicWatches', 'group');
			
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
	
		// @todo implement scan to post
		$post_id = (int) $iPostId;
	
		$table = (self::ADV) 
			? Engine_Api::_()->getDbtable('posts', 'advgroup')
			: Engine_Api::_()->getDbtable('posts', 'group');
		
		$select = $table->select()
			->where('group_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->order('creation_date ASC');
	
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($iPage);
		$paginator->setItemCountPerPage($iLimit);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
	
		$result = array();
		$view = Zend_Registry::get("Zend_View");
		
		foreach ($paginator as $post)
		{
			$user = Engine_Api::_()->user()->getUser($post->user_id);
			$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$view = Zend_Registry::get("Zend_View");
			$body = $post->body;
			$body = nl2br($view->BBCode($body, array('link_no_preparse' => true)));
			
			$canPost = false;
			if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($group, null, 'comment') ) 
			{
				$canPost = true;
			}
			if( Engine_Api::_()->authorization()->isAllowed($group, null, 'topic.edit') ) 
			{
				$canEdit = true;
			}
			
			$canEditPost = false; 
			if ( $post->user_id == $viewer->getIdentity() || $group->getOwner()->getIdentity() == $viewer->getIdentity() || $canEdit)
			{
				$canEditPost = true;
			}
			
			$result[] = array(
					'iPostId' => $post->getIdentity(),
					'iTopicId' => $topic->getIdentity(),
					'iUserId' => $post->user_id,
					'sUserName' => $user->getTitle(),
					'sUserPhoto' => $sUserImageUrl,
					'sSignature' =>  '',
					'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
					'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
					'sBody' => $body,
					'sPhotoUrl' => '',
					'bCanPost' => $canPost,
					'bCanEditPost' => $canEditPost,
					'bCanDeletePost' => $canEditPost,
			);
		}
		return $result;
	}
	
	public function post_reply($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		if (!isset($sBody))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
				
		$topic = (self::ADV) 
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		
		$group = $topic->getParentGroup();
		
		if( $topic->closed ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This has been closed for posting.")
			);
		}
		
		$allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_html', 0);
		$allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_bbcode', 0);
		
		if( isset($iQuoteId) && !empty($iQuoteId) ) 
		{
			$quote = (self::ADV)
				? Engine_Api::_()->getItemTable("advgroup_post")->findRow($iQuoteId)
				: Engine_Api::_()->getItemTable("group_post")->findRow($iQuoteId);
			
			if($quote->user_id == 0) {
				$owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
			} else {
				$owner_name = $quote->getOwner()->__toString();
			}
				
// 			if( $allowHtml || !$allowBbcode ) {
// 				$sBody = "<blockquote><strong>" . "{$owner_name} said:" . "</strong><br />" . $quote->body . "</blockquote><br />" . $sBody;
// 			} else {
// 				$sBody = "[blockquote][b]" . strip_tags("{$owner_name} said:") . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
// 			}
			$sBody = "[blockquote][b]" . strip_tags("{$owner_name} said:") . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$allowHtml ) 
		{
			$filter = new Engine_Filter_HtmlSpecialChars();
		} 
		else 
		{
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'group', 'commentHtml')));
			$filter->setAllowedTags($allowed_tags);
		}
		
		$sBody = $filter->filter($sBody);
		if ($sBody == '')
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Post content is invalid!")
			);
		}
		
		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$topicOwner = $topic->getOwner();
		$isOwnTopic = $viewer->isSelf($topicOwner);
		
		$postTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('posts', 'advgroup')
			: Engine_Api::_()->getDbtable('posts', 'group');
			
		$topicWatchesTable = (self::ADV)
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
		
		$values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
		$values['user_id'] = $viewer->getIdentity();
		$values['group_id'] = $group->getIdentity();
		$values['topic_id'] = $topic->getIdentity();
		$values['watch'] =  (isset($iWatch) && $iWatch == '1') ? 1 : 0;
		
		$watch = (bool) $values['watch'];
		$isWatching = $topicWatchesTable
		->select()
		->from($topicWatchesTable->info('name'), 'watch')
		->where('resource_id = ?', $group->getIdentity())
		->where('topic_id = ?', $topic->getIdentity())
		->where('user_id = ?', $viewer->getIdentity())
		->limit(1)
		->query()
		->fetchColumn(0)
		;
		
		$db = $group->getTable()->getAdapter();
		$db->beginTransaction();
		
		try
		{
			// Create post
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
		
			// Watch
			if( false === $isWatching ) 
			{
				$topicWatchesTable->insert(array(
						'resource_id' => $group->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} 
			else if( $watch != $isWatching ) 
			{
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $group->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			// Activity
			$action = $activityApi->addActivity($viewer, $group, 'advgroup_topic_reply', null, array('child_id' => $topic->getIdentity()));
			if( $action ) 
			{
				$action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
		
		
			// Notifications
			$notifyUserIds = $topicWatchesTable->select()
			->from($topicWatchesTable->info('name'), 'user_id')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('watch = ?', 1)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN)
			;
		
			$view = Zend_Registry::get("Zend_View");
			
			foreach( $userTable->find($notifyUserIds) as $notifyUser ) 
			{
				if( $notifyUser->isSelf($viewer) ) 
				{
					continue;
				}
				if( $notifyUser->isSelf($topicOwner) ) 
				{
					$type = 'advgroup_discussion_response';
				} else 
				{
					$type = 'advgroup_discussion_reply';
				}
				$notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
						'message' => $view->BBCode($post->body),
				));
			}
		
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get('Zend_Translate') -> _("Posted reply successfully!"),
					'iPostId' => $post->getIdentity(),
					'iTopicId' => $iTopicId,
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}
	
	public function edit_post($aData)
	{
		extract($aData);
		
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		if (!isset($sBody) || $sBody == "")
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
		
		$post = (self::ADV) 
			? Engine_Api::_()->getItemTable("advgroup_post")->findRow($iPostId)
			: Engine_Api::_()->getItemTable("group_post")->findRow($iPostId);
		
		$group = $post->getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($viewer, 'topic.edit') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$post->modified_date = date('Y-m-d H:i:s');
			$post->body = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$post->save();
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Edited post successfully!"),
				'iPostId' => $post->getIdentity(),
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function delete_post($aData)
	{
		extract($aData);
		
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		$post = (self::ADV)
			? Engine_Api::_()->getItemTable("advgroup_post")->findRow($iPostId)
			: Engine_Api::_()->getItemTable("group_post")->findRow($iPostId);
		
		$group = $post->getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($user, 'topic.edit') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		$topic_id = $post->topic_id;
	
		try
		{
			$post->delete();
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted post successfully!"),
				'iTopicId' => $topic_id,
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_watch($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iWatch))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iWatch!")
			);
		}
		
		$topic = (self::ADV) 
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
			
		$group = Engine_Api::_()->getItem('group', $topic->group_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$watch = ( isset($iWatch) && $iWatch == '1' ) ? true : false;

		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$db = $topicWatchesTable->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
		
			if( false === $isWatching ) {
				$topicWatchesTable->insert(array(
						'resource_id' => $group->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if( $watch != $isWatching ) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $group->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => ($watch) 
					? Zend_Registry::get('Zend_Translate')->_("Set watching successfully")
					: Zend_Registry::get('Zend_Translate')->_("Unset watching successfully")
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
		
	}
	
	public function topic_sticky($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iSticky))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iSticky!")
			);
		}
		
		
		$topic = (self::ADV)
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		
		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$sticky = ( isset($iSticky) && $iSticky == '1' ) ? true : false;
		
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->sticky = (bool) $sticky;
			$topic->save();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => ($sticky)
					? Zend_Registry::get('Zend_Translate')->_("Set sticky successfully")
					: Zend_Registry::get('Zend_Translate')->_("Unset sticky successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_close($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iClosed))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iClosed!")
			);
		}
		
		$topic = (self::ADV)
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		
		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$closed = ( isset($iClosed) && $iClosed == '1' ) ? true : false;
		
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->closed = (bool) $closed;
			$topic->save();
	
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => ($closed)
					? Zend_Registry::get('Zend_Translate')->_("Closed topic successfully")
					: Zend_Registry::get('Zend_Translate')->_("Opened topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_rename($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($sTitle))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing sTitle!")
			);
		}
		
		$topic = (self::ADV)
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		
		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		$viewer = Engine_Api::_()->user()->getViewer();
	
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->title = htmlspecialchars($sTitle);
			$topic->save();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("Renamed topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
	}
	
	public function topic_delete($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		$topic = (self::ADV)
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		
		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$viewer = Engine_Api::_()->user()->getViewer();
	
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$group = $topic->getParent('group');
			$topic->delete();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("Deleted topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
	}
	
	public function featured($aData)
	{
		if (!self::ADV)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("This feature is only supported on Advanced Group module")
			);
		}
		
		$aData['search'] = 1;
		
		$groupTbl = Engine_Api::_()->getItemTable('group');
		$select = $groupTbl->getGroupSelect($aData);
		
		if (isset($aData['iLastGroupId']))
			$select->where("group_id < " . $aData['iLastGroupId']);
		
		if (!isset($aData['iLimit']))
			$aData['iLimit'] = 10;
		
		$select->where('featured = 1')->where('is_subgroup = 0')->where('search = 1');
		
		$select->limit($aData['iLimit']);
		$groups = $groupTbl->fetchAll($select);
		
		return $this -> parserGroups($groups);
	}
	
	public function set_featured($aData)
	{
		if (!self::ADV)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("This feature is only supported on Advanced Group module")
			);
		}
		
		if (!isset($aData['iGroupId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("Missing iGroupId!")
			);
		}
		
		$group = Engine_Api::_()->getItem('group', $aData['iGroupId']);
		$group->featured = 1;
		$group->save();
		
		return array(
				'error_code' => 0,
				'message' => Zend_Registry::get('Zend_Translate')->_("Setting featured successfully")
		);
	}
	
	/**
	 * form add
	 */
	public function formadd($aData)
	{
		$response  =  array(
				'view_options'=> $this->privacies(array("sType" => "view")),
				'comment_options'=> $this->privacies(array("sType" => "comment")),
				'photo_options'=> $this->privacies(array("sType" => "photo")),
				'event_options'=> $this->privacies(array("sType" => "event")),
				'categories' => $this->category(),
		);
		return $response;
	}
	
	/**
	 * all data needed for event edit form.
	 */
	public function formedit($aData)
	{
	
		return array_merge(
				$this->detail($aData),
				$this->formadd(array())
		);
	}
	
	protected function getGroupStateByUser($group, $user = null)
	{
		if (null !== $user)
		{
			$viewer = $user;
		}
		else
		{
			$viewer = Engine_Api::_()->user()->getViewer();
		}
		$owner = $group->getOwner();
		$list = $group->getOfficerList();
		$membership = $group->membership()->getRow($viewer);
		return array(
			'iGroupId' => $group->getIdentity(),
			'bIsOwner' => ($owner->getIdentity() === $viewer->getIdentity()) ? true : false,
			'bIsMember' => ($membership->active) ? true : false,
			'bOfficer' => ($list->has($viewer)) ? true : false,
			'bRequestedMembership' => (!$membership->resource_approved && $membership->user_approved) ? true : false,
			'bIsInvited' => (!$membership->user_approved && $membership->resource_approved ) ? true : false,
			'bIsNeedApprove' => ($group->approval) ? true : false,
		);
	}
	
	public function promote($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		if ( !isset($iUserId) || null === ($user = Engine_Api::_()->getItem('user', $iUserId)) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iUserId!")
			);
		}
		$subject = $group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		
		$list = $group->getOfficerList();
		$viewer = Engine_Api::_()->user()->getViewer();
	
		if( !$group->membership()->isMember($user) ) 
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Cannot add a non-member as an officer!")
			);
		}
		
		$table = $list->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$list->add($user);
	
			// Add notification
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$notifyApi->addNotification($user, $viewer, $group, 'advgroup_promote');
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($user, $group, 'advgroup_promote');
	
			$db->commit();
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('Member Promoted.'),
				'aActionData' => $this->getGroupStateByUser($group, $user),
		);
	}
	
	public function demote($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		if ( !isset($iUserId) || null === ($user = Engine_Api::_()->getItem('user', $iUserId)) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iUserId!")
			);
		}
		$subject = $group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		
		$list = $group->getOfficerList();
		if( !$group->membership()->isMember($user) ) 
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Cannot remove a non-member as an officer!")
			);
		}
		
		$table = $list->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$list->remove($user);
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
		return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('Member Demoted.'),
				'aActionData' => $this->getGroupStateByUser($group, $user),
		);
	}
	
	public function create_topic($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
		$group = Engine_Api::_()->getItemTable('group')->findRow($iGroupId);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->authorization()->isAllowed($group, $viewer, 'comment'))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to create topic!")
			);
		}
	
		if (!isset($sTitle) || empty($sTitle))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic title!")
			);
		}
		
		if (!isset($sBody) || empty($sBody))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic body!")
			);
		}
		
		// Process
		$values = array(
			'title' => $sTitle, 
			'body' => $sBody,
			'watch' => (isset($iWatch) && ($iWatch == '0' || $iWatch == '1')) ? $iWatch : 1,  	
		);
		
		$values['user_id'] = $viewer->getIdentity();
		$values['group_id'] = $group->getIdentity();
	
		$topicTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topics', 'advgroup')
			: Engine_Api::_()->getDbtable('topics', 'group');
		
		$topicWatchesTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
			: Engine_Api::_()->getDbtable('topicWatches', 'group');
		
		$postTable = (self::ADV) 
			? Engine_Api::_()->getDbtable('posts', 'advgroup')
			: Engine_Api::_()->getDbtable('posts', 'group');
	
		$db = $group->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			// Create topic
			$topic = $topicTable->createRow();
			$topic->setFromArray($values);
			$topic->save();
	
			// Create post
			$values['topic_id'] = $topic->topic_id;
	
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
	
			// Create topic watch
			$topicWatchesTable->insert(array(
					'resource_id' => $group->getIdentity(),
					'topic_id' => $topic->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'watch' => (bool) $values['watch'],
			));
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $group, 'advgroup_topic_create', null, array('child_id' => $topic->getIdentity()));
			if( $action ) {
				$action->attach($topic, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
	
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Created topic successfully!"),
				'iTopicId' => $topic->getIdentity()
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 3,
					'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function topic_info($aData)
	{
		extract($aData);
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
		$topic = (self::ADV)
			? Engine_Api::_()->getDbTable("topics", "advgroup")->findRow($iTopicId)
			: Engine_Api::_()->getDbTable("topics", "group")->findRow($iTopicId);
		if( is_null($topic) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = $topic->getParentGroup();
	
		// Check watching
		$isWatching = null;
		if( $viewer->getIdentity() ) {
			$topicWatchesTable = (self::ADV) 
				? Engine_Api::_()->getDbtable('topicWatches', 'advgroup')
				: Engine_Api::_()->getDbtable('topicWatches', 'group');
			
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
	
		// Auth for topic
		$canPost = false;
		$canEdit = false;
		$canDelete = false;
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($group, null, 'comment') ) 
		{
			$canPost = true;
		}
		$canDelete = $canEdit = $topic->canEdit($viewer);
		return array(
				'iTopicId' => $iTopicId,
				'sTopicTitle' => $topic->getTitle(),
				'iGroupId' => $group->getIdentity(),
				'sGroupTitle' => $group->getTitle(),
				'bCanPost' => $canPost,
				'bCanEdit' => $canEdit,
				'bCanDelete' => $canDelete,
				'bIsWatching' => $isWatching,
				'bIsSticky' => ($topic->sticky) ? true : false,
				'bIsClosed' => ($topic->closed) ? true : false,
		);
	}
	
	public function post_info($aData)
	{
		extract($aData);
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
	
		$post = (self::ADV)
			? Engine_Api::_()->getItemTable("advgroup_post")->findRow($iPostId)
			: Engine_Api::_()->getItemTable("group_post")->findRow($iPostId);
	
		if (is_null($post))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This post is not existed!")
			);
		}
		
		$user = $post->getOwner();
		$body = $post->body;
		$view = Zend_Registry::get("Zend_View");
		$body = nl2br($view->BBCode($body, array('link_no_preparse' => true)));
		if( strip_tags($body) == $body ) {
			$body = nl2br($body);
		}
			
		$postPhoto = "";
		$userPhoto = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($userPhoto != "")
		{
			$userPhoto = Engine_Api::_() -> ynmobile() -> finalizeUrl($userPhoto);
		}
		else
		{
			$userPhoto = NO_USER_ICON;
		}
		
		return array(
				'iPostId' => $iPostId,
				'iUserId' => $post->user_id,
				'sUserName' => $user->getTitle(),
				'sUserPhoto' => $userPhoto,
				'sSignature' =>  "",
				'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
				'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
				'sBody' => $body,
				'sPhotoUrl' => $postPhoto,
		);
	}
	
	public function upload_photo($aData)
	{
		extract($aData);
		if (!isset($iGroupId) || empty($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Group ID!")
			);
		}
		$group = Engine_Api::_()->getItem('group', $iGroupId);
		
		if (!is_object($group) || is_null($group))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This group is not existed!")
			);
		}
		if( !$group->authorization()->isAllowed($viewer, 'photo') ) 
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have permission to upload photos to this group!")
			);
		}
		if (!isset($_FILES['image']))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("No file!"),
					'result' => 0
			);
		}
	
		$db = (self::ADV)
			? Engine_Api::_()->getDbtable('photos', 'advgroup')->getAdapter()
			: Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
		
		$db->beginTransaction();
	
		try 
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$album = $group->getSingletonAlbum();
			$params = array(
					'user_id' => $viewer->getIdentity(),
					'group_id' => $group->getIdentity(),
					'album_id'=> $album->album_id,
					'collection_id'	=> $album->album_id,
			);
	
			$photoTable = (self::ADV) 
				? Engine_Api::_()->getItemTable('advgroup_photo')
				: Engine_Api::_()->getItemTable('group_photo');
			
			$photo = $photoTable->createRow();
			$photo->setFromArray($params);
			$photo->save();
			//$photo->setPhoto($_FILES['image']);
			$photo = $this->setPhoto($photo, $_FILES['image']);
			$db->commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Photo successfully uploaded."),
				'iPhotoId' => $photo -> getIdentity(),
				'sPhotoTitle' => $photo -> getTitle(),
				'sType' => 'group_photo',
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 3,
				'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function updateMemberCount($group)
	{
		$table = (self::ADV) 
			? Engine_Api::_()->getDbtable('membership', 'advgroup')
			: Engine_Api::_()->getDbtable('membership', 'group');
		
		$select = new Zend_Db_Select($table->getAdapter());
		$select
			->from($table->info('name'), new Zend_Db_Expr('COUNT(user_id) as member_count'))
			->where('resource_id = ?', $group->getIdentity())
			->where('active = ?', (bool) true);

		$row = $table->getAdapter()->fetchRow($select);
		$memberCount = $row['member_count'];
		$group->member_count = $memberCount;
		$group->save();
	}
	
	public function photo_edit($aData)
	{
		extract($aData);
		if (!isset($iPhotoId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('Missing photo id.')
			);
		}
	
		$photo = Engine_Api::_()->getItem("advgroup_photo", $iPhotoId);
	
		if ($photo->getIdentity())
		{
			if (isset($sTitle))
				$photo->title = $sTitle;
	
			if (isset($sDescription))
				$photo->description = $sDescription;
	
			$photo->save();
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('Edited successfully'),
					'iPhotoId' => $iPhotoId
			);
	
		}
		else
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('This photo is not existed.')
			);
		}
	
	}
	
	public function setPhoto($photoObj, $photo)
	{
		if( $photo instanceof Zend_Form_Element_File ) 
		{
			$file = $photo->getFileName();
			$fileName = $file;
		} 
		else if( $photo instanceof Storage_Model_File ) 
		{
			$file = $photo->temporary();
			$fileName = $photo->name;
		} 
		else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) 
		{
			$tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
			$file = $tmpRow->temporary();
			$fileName = $tmpRow->name;
		} 
		else if( is_array($photo) && !empty($photo['tmp_name']) ) 
		{
			$file = $photo['tmp_name'];
			$fileName = $photo['name'];
		} 
		else if( is_string($photo) && file_exists($photo) ) 
		{
			$file = $photo;
			$fileName = $photo;
		} 
		else 
		{
			throw new Classified_Model_Exception('invalid argument passed to set photo');
		}
	
		if( !$fileName ) 
		{
			$fileName = basename($file);
		}
	
		$extension = ltrim(strrchr(basename($fileName), '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
	
		$params = array(
				'parent_type' => $photoObj->getType(),
				'parent_id' => $photoObj->getIdentity(),
				'user_id' => $photoObj->user_id,
				'name' => $fileName,
		);
	
		// Save
		$filesTable = Engine_Api::_()->getItemTable('storage_file');
		$angle = 0;
		if (function_exists("exif_read_data"))
		{
			$exif = exif_read_data($file);
			if (!empty($exif['Orientation']))
			{
				switch($exif['Orientation'])
				{
					case 8 :
						$angle = 90;
						break;
					case 3 :
						$angle = 180;
						break;
					case 6 :
						$angle = -90;
						break;
				}
			}
		}
		
		// Resize image (main)
		$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
		$image = Engine_Image::factory();
		$image->open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image	->resize(720, 720)
				->write($mainPath)
				->destroy();
	
		// Resize image (normal)
		$normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
		$image = Engine_Image::factory();
		$image->open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image	->resize(140, 160)
				->write($normalPath)
				->destroy();
	
		// Store
		$iMain = $filesTable->createFile($mainPath, $params);
		$iIconNormal = $filesTable->createFile($normalPath, $params);
	
		$iMain->bridge($iIconNormal, 'thumb.normal');
	
		// Remove temp files
		@unlink($mainPath);
		@unlink($normalPath);
	
		// Update row
		$photoObj->modified_date = date('Y-m-d H:i:s');
		$photoObj->file_id = $iMain->file_id;
		$photoObj->save();
	
		return $photoObj;
	}
	
}