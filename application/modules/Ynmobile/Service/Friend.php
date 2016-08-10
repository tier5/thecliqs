<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Friend.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Service_Friend extends Ynmobile_Service_Base
{
    
    protected $module = 'user';
    
    /**
	 * Input data:
	 * + iUserId: int, optional.
	 * + sDisplayName: string
	 * + iLimit: int, optional.
	 * + iLastFriendIdViewed: int, optional.
	 * + sType: string, optional. Ex: "more" or "new".
	 * + sAction: string, optional. Ex: "all" or "confirm"
	 *
	 * Output data:
	 * + sFullName: string.
	 * + id: int.
	 * + UserProfileImg_Url: string.
	 * + BigUserProfileImg_Url: string
	 * + isFriend: bool
	 * + iMutualFriends: int
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/get
	 *
	 * @param array $aData
	 * @return array
	 */
	public function fetch($aData)
	{
		extract($aData, EXTR_SKIP);
        
        $fields = explode(',',$fields);
        
        $fields[] = 'action';
        
        if(empty($fields)) return array();
        
        $bExcludeMe = intval(@$bExcludeMe);
        $iPage     = intval(@$iPage);
        $iLimit = $iLimit?intval($iLimit):20;
        $sAction  = isset($sAction)?strtolower($sAction): 'all';

		
		$oViewer = Engine_Api::_() -> user() -> getViewer();
		$iViewerId = intval($oViewer->getIdentity());
        
		if (!isset($iUserId) || empty($iUserId))
		{
			// my friends
			$iUserId = $oViewer -> getIdentity();
		}
		$iUserId = (int)$iUserId;
		
		$oUser = Engine_Api::_() -> user() -> getUser($iUserId);
		$membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
		$userTable = Engine_Api::_() -> getItemTable('user');
        $membershipName =  $membershipTable->info('name');
        $userName =  $userTable->info('name');
        
		// Multiple friend mode
		
                       
        $select = null;
		
        
		if ($sAction == 'confirm')
		{
		    $select = $membershipTable -> select()
                -> from(array('m1'=>$membershipName))
                -> setIntegrityCheck(false);
			$select -> where("m1.user_id = ?", $iUserId) -> where('active = ?',0);
		}
        else if ($sAction  == 'mutual'){
            
            $select = $membershipTable -> select()
                -> from(array('m1'=>$membershipName))
                -> setIntegrityCheck(false)-> where("m1.user_id = ?", $iUserId)
                -> where('m1.active = ?',1)
                -> join(array('m2'=>$membershipName), 'm1.resource_id=m2.user_id',null)
                ->where('m2.resource_id =?', $iViewerId)
                ->where('m2.active=?',1)
            ;
            
        }elseif (!empty($sSearch)){
            $select = $membershipTable -> select()
                -> from(array('m1'=>$membershipName))
                -> setIntegrityCheck(false);
            
            
                $select -> joinLeft(array('u'=>$userName), 
                "u.`user_id` = m1.`resource_id`", null);
                
            $select -> where("`u`.`displayname` LIKE ?", "%{$sSearch}%");    
            $select -> where("`m1`.`user_id` = ?", $iUserId) -> where('active = ?', 1);
        }
		else
		{
		    $select = $membershipTable -> select()
                -> from(array('m1'=>$membershipName))
                -> setIntegrityCheck(false);
                
            $select -> where("`m1`.`user_id` = ?", $iUserId) -> where('`m1`.`active` = ?',1);
		}
        // exculude me
        if($bExcludeMe){
            $select -> where("m1.resource_id <> ?", $iViewerId);    
        }
        
		$friends = Zend_Paginator::factory($select);
		$friends -> setCurrentPageNumber($iPage);
		$friends -> setItemCountPerPage($iLimit);
		
		if ($iPage > $friends->count())
		{
			return array();
		}
		
		// Get stuff
		$ids = array();
		foreach ($friends as $friend)
		{
			$ids[] = $friend -> resource_id;
		}

		// Get the items
		$items = array();
        
        $appMeta = Ynmobile_AppMeta::getInstance();
		
		//-----------------------
		foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $entry)
		{
            $items[] =  $appMeta->getModelHelper($entry)->toArray($fields);
		}
		return $items;
	}

	/**
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/add
	 *
	 * @param array $aData
	 * @return array
	 */
	public function add($aData)
	{
		// Get viewer and other user
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (null == ($iUserId = $aData['iUserId']) || null == ($oUser = Engine_Api::_() -> getItem('user', $iUserId)))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iUserId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('No member specified')
			);
		}

		// check that user is not trying to befriend 'self'
		if ($viewer -> isSelf($oUser))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('You cannot befriend yourself.')
			);
		}
		// check that user is already friends with the member
		if ($oUser -> membership() -> isMember($viewer))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('You are already friends with this member.')
			);
		}

		// check that user has not blocked the member
		if ($viewer -> isBlocked($oUser))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Friendship request was not sent because you blocked this member.')
			);
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('membership', 'user') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// send request
			$oUser -> membership() -> addMember($viewer) -> setUserApproved($viewer);

			if (!$viewer -> membership() -> isUserApprovalRequired() && !$viewer -> membership() -> isReciprocal())
			{
				// if one way friendship and verification not required

				// Add activity
				Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $oUser, 'friends_follow', '{item:$subject} is now following {item:$object}.');

				// Add notification
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($oUser, $viewer, $viewer, 'friend_follow');

			}
			else
			if (!$viewer -> membership() -> isUserApprovalRequired() && $viewer -> membership() -> isReciprocal())
			{
				// if two way friendship and verification not required

				// Add activity
				Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($oUser, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
				Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $oUser, 'friends', '{item:$object} is now friends with {item:$subject}.');

				// Add notification
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($oUser, $viewer, $oUser, 'friend_accepted');
			}
			else
			if (!$oUser -> membership() -> isReciprocal())
			{
				// if one way friendship and verification required

				// Add notification
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($oUser, $viewer, $oUser, 'friend_follow_request');
			}
			else
			if ($oUser -> membership() -> isReciprocal())
			{
				// if two way friendship and verification required

				// Add notification
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($oUser, $viewer, $oUser, 'friend_request');
			}
			$db -> commit();
			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => ""
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error has occurred.')
			);
		}
	}

	/**
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/confirm
	 *
	 * @param array $aData
	 * @return array
	 */
	public function confirm($aData)
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			// Set Norender if user is not logged on
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Fail!')
			);
		}

		if (null == ($iUserId = $aData['iUserId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iUserId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('No member specified')
			);
		}

		$resource_id = $iUserId;
		$user = Engine_Api::_() -> getItem('user', $resource_id);
		if ($resource_id)
		{
			$uid = $viewer -> getIdentity();
			$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
			$db = $userTb -> getAdapter();
			$db -> beginTransaction();
			$select = $userTb -> select() -> where("(user_id = $uid AND resource_id = $resource_id)
					OR (user_id = $resource_id AND resource_id = $uid)") -> where("active = 0");
			$rows = $userTb -> fetchAll($select);
			try
			{
				if (count($rows))
				{
					foreach ($rows as $row)
					{
						$row -> active = 1;
						$row -> user_approved = 1;
						$row -> resource_approved = 1;
						$row -> save();
					}
					// Add activity
					if (!$user -> membership() -> isReciprocal())
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
					}
					else
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
					}

					// Add notification
					if (!$user -> membership() -> isReciprocal())
					{
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow_accepted');
					}
					else
					{
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_accepted');
					}
					// Set the requests as handled
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$db -> commit();
					return array(
						'result' => 1,
						'error_code' => 0,
						'error_message' => ""
					);
				}
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
		}
	}

	/**
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/deny
	 *
	 * @param array $aData
	 * @return array
	 */
	public function deny($aData)
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{

			return false;
		}
		$resource_id = $aData['iUserId'];

		if (!$resource_id || !isset($resource_id))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iUserId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('No member specified')
			);
		}

		$user = Engine_Api::_() -> getItem('user', $resource_id);
		if ($resource_id)
		{
			$uid = $viewer -> getIdentity();
			$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
			$db = $userTb -> getAdapter();
			$db -> beginTransaction();
			$select = $userTb -> select() -> where("(user_id = $uid AND resource_id = $resource_id)
					OR (user_id = $resource_id AND resource_id = $uid)") -> where("active = 0");
			$rows = $userTb -> fetchAll($select);

			try
			{
				if (count($rows))
				{
					foreach ($rows as $row)
					{
						$row -> delete();
					}
					// Set the requests as handled
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$db -> commit();
					return array(
						'result' => 1,
						'error_code' => 0,
						'error_message' => ""
					);
				}
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				return array(
					'error_code' => 1,
					'error_message' => $e -> getMessage()
				);
			}
		}
	}

	/**
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/delete
	 *
	 * @param array $aData
	 * @return array
	 */
	public function delete($aData)
	{
		// Get viewer and other user
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (null == ($iUserId = $aData['iUserId']) || null == ($oUser = Engine_Api::_() -> getItem('user', $iUserId)))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iUserId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('No member specified')
			);
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('membership', 'user') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer -> membership() -> removeMember($oUser);
			// Remove from lists?
			// @todo make sure this works with one-way friendships
			$oUser -> lists() -> removeFriendFromLists($viewer);
			$viewer -> lists() -> removeFriendFromLists($oUser);

			// Set the requests as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($oUser, $viewer, 'friend_request');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> read = 1;
				$notification -> save();
			}
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $oUser, 'friend_follow_request');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> read = 1;
				$notification -> save();
			}

			$db -> commit();

			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => ""
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error has occurred.')
			);
		}
	}

	/**
	 * Input data:
	 * + sName: string, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + iFriendListId: int.
	 * + sMessage: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see friend/addlist
	 *
	 * @param array $aData
	 * @return array
	 */
	public function addlist($aData)
	{
		$sName = isset($aData['sName']) ? $aData['sName'] : '';
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity() || !trim($sName))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'sName',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid data')
			);
		}
		$listTable = Engine_Api::_() -> getItemTable('user_list');
		$list = $listTable -> createRow();
		$list -> owner_id = $viewer -> getIdentity();
		$list -> title = $sName;
		$list -> save();

		return array(
			'iFriendListId' => $list -> getIdentity(),
			'sMessage' => Zend_Registry::get('Zend_Translate') -> _('List Successfully Created')
		);
	}

	/**
	 * Input data:
	 * + iFriendListId: int, required.
	 * + sFriendId: string, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see friend/addfriendstolist
	 *
	 * @param array $aData
	 * @return array
	 */
	public function addfriendstolist($aData)
	{
		$iFriendListId = isset($aData['iFriendListId']) ? (int)$aData['iFriendListId'] : 0;
		if ($iFriendListId < 1)
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iFriendListId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Check list
		$listTable = Engine_Api::_() -> getItemTable('user_list');
		$list = $listTable -> find($iFriendListId) -> current();
		if (!$list || $list -> owner_id != $viewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Missing list/not authorized')
			);
		}

		$aTemp = explode(',', $aData['sFriendId']);
		$aFriendId = array();

		foreach ($aTemp as $iFriendId)
		{
			$friend = Engine_Api::_() -> getItem('user', $iFriendId);
			if (!$friend)
			{
				continue;
			}
			// Check if already target status
			if ($list -> has($friend))
			{
				continue;
			}
			$list -> add($friend);
		}
		return array(
			'result' => 1,
			'message' => Zend_Registry::get('Zend_Translate') -> _('Members added to list.')
		);
	}

	
	public function cancelrequest($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (null == ($iUserId = $aData['iUserId']) || null == ($oUser = Engine_Api::_() -> getItem('user', $iUserId)))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iUserId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('No member specified')
			);
		}
		
		if ($viewer -> isSelf($oUser))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('You cannot cancel request yourself.')
			);
		}
		
		// Process
		$db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
		$db->beginTransaction();
	
		try {
			$oUser->membership()->removeMember($viewer);
	
			// Set the requests as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')
			->getNotificationBySubjectAndType($oUser, $viewer, 'friend_request');
			if( $notification ) {
				$notification->mitigated = true;
				$notification->read = 1;
				$notification->save();
			}
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')
			->getNotificationBySubjectAndType($oUser, $viewer, 'friend_follow_request');
			if( $notification ) {
				$notification->mitigated = true;
				$notification->read = 1;
				$notification->save();
			}
	
			$db->commit();
	
			return array(
					'error_code' => 0,
					'error_message' => "",
					'message' => Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.')
			);
	
		} catch( Exception $e ) {
			$db->rollBack();
			return array(
					'error_code' => 2,
					'error_message' => $e->getMessage()
			);
		}
	}
	
}
