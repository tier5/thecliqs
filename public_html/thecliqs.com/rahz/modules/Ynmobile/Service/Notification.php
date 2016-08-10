<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Notification.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Service_Notification extends Ynmobile_Service_Base
{
    protected $module =  'activity';
    
    protected $mainItemType = 'activity_notification';
    
	/**
	 * Get total update of viewer
	 * Input data: N/A
	 *
	 * Output data:
	 * + iNumberOfFriendRequest: int.
	 * + iNumberOfMessage: int.
	 * + iNumberNotification: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/get
	 *
	 * @param array $aData
	 * @return array
	 */
	public function update($aData)
	{
		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			
			if (isset($aData['iUserId']))
			{
				$viewer_id = $aData['iUserId'];
			}
			else 
			{
				$viewer = Engine_Api::_() -> user() -> getViewer();
				if (!$viewer_id = $viewer -> getIdentity())
				{
					return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get updates!")
					);
				}	
			}
			
			$notificationTb = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$tbName = $notificationTb -> info('name');
			$db = $notificationTb -> getAdapter();
			$notif_friend = $db -> query("SELECT (SELECT COUNT(*) FROM $tbName WHERE `user_id` = {$viewer_id} AND `mitigated` = 0 AND `type` = 'friend_request') as frequest_count, (SELECT COUNT(*) FROM $tbName WHERE `user_id` = {$viewer_id} AND `mitigated` = 0 AND `type` = 'message_new') as message_count") -> fetch();

			//check notificcation type you want to receive notifications
			$notificationSettings = Zend_Json::decode(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmobile_notifications', ''));
			
			$select = $notificationTb -> select() -> where("`user_id` = ?", $viewer_id) -> where("`type` NOT IN ('friend_request','message_new')") -> where("`mitigated` = 0");
			
			if($notificationSettings)
				$select -> where("type NOT IN (?)", $notificationSettings);
			
			$notifications = $notificationTb -> fetchAll($select);
			$request = $notif_friend['frequest_count'];
			//$message = $notif_friend['message_count'];
			$message = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
			
			if($notificationSettings)
			{
				if (in_array('friend_request', $notificationSettings))
				{
					$request = 0;
				}
					
				if (in_array('message_new', $notificationSettings))
				{
					$message = 0;
				}	
			} 
			
			$data = array(
				'iNumberNotification' => count($notifications),
				'iNumberOfFriendRequest' => $request,
				'iNumberOfMessage' => $message
			);
			return $data;

		}
		catch(exception $e)
		{
			return array(
				'error_message' => $e->getMessage(),
				'error_code' => 1,
				'result' => 0
			);
		}
	}

	/**
	 *
	 * Input data: N/A
	 *
	 * Output data:
	 * + iConversationId: int.
	 * + sTitle: string.
	 * + sPreview: string.
	 * + iUserId: int.
	 * + sFullName: string.
	 * + sUserImage: string.
	 * + iTimeStamp: int.
	 * + sTime: string.
	 * + sTimeConverted: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/message
	 *
	 * @param array $aData
	 * @return array
	 */
	public function message($aData)
	{
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get message!")
			);
		}
		//check notificcation type you want to receive notifications
		$notificationSettings = Zend_Json::decode(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmobile_notifications', ''));
		if (in_array('message_new', $notificationSettings))
		{
			return array();
		}

		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Get notification
			$notifications = $notificationsTable -> fetchAll("`user_id` = {$viewer->getIdentity()} AND `type` = 'message_new' AND `mitigated` = 0 AND `read` = 0 ");
			if ($notifications)
			{
				foreach ($notifications as $notification)
				{
					$notification -> mitigated = 1;
					$notification -> save();
				}
				$db -> commit();
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		// Get Message
		$table = Engine_Api::_() -> getItemTable('messages_conversation');
		$rName = Engine_Api::_() -> getDbtable('recipients', 'messages') -> info('name');
		$cName = $table -> info('name');
		$select = $table -> select() -> setIntegrityCheck(false) -> from($cName) -> joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", "{$rName}.inbox_updated") -> where("`{$rName}`.`user_id` = ?", $viewer -> getIdentity()) -> where("`{$rName}`.`inbox_deleted` = ?", 0) -> order('inbox_read ASC') -> order(new Zend_Db_Expr('inbox_updated DESC'));

		if (isset($aData['sInboxUpdated']))
		{
			$select = $select -> where("`{$rName}`.`inbox_updated` < (?)", $aData['sInboxUpdated']);
		}

		$conversations = $table -> fetchAll($select);
		$aMessages = array();
		foreach ($conversations as $conversation)
		{
			//photoURL
			$sender = Engine_Api::_() -> user() -> getUser($conversation -> user_id);
			$sOwnerImage = $sender -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sOwnerImage != "")
			{
				$sOwnerImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sOwnerImage);
			}
			else
			{
				$sOwnerImage = NO_USER_ICON;
			}
			$sTimeStamp = strtotime($conversation -> modified);
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

			$sTime = date("D, j M Y G:i:s O", $sTimeStamp);

			if (null !== $timezone)
			{
				date_default_timezone_set($prevTimezone);
			}
			$message = $conversation -> getInboxMessage($viewer);
			$recipient = $conversation -> getRecipientInfo($viewer);
			
			$aMessages[] = array(
				'iConversationId' => $conversation -> conversation_id,
				'bIsRead' => ($recipient->inbox_read == '1') ? true : false,
				'iUserId' => $sender -> getIdentity(),
				'sFullName' => $sender -> getTitle(),
				'sUserImage' => $sOwnerImage,
				'sTitle' => $conversation -> title,
				'sPreview' => nl2br(html_entity_decode($message -> body)),
				'iTimeStamp' => $sTimeStamp,
				'sTime' => $sTime,
				'sInboxUpdated' => $conversation -> inbox_updated,
				'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($sTimeStamp)
			);
		}
		//$aMessages = array_reverse($aMessages);
		return $aMessages;
	}

	/**
	 *
	 * Input data: N/A
	 *
	 * Output data:
	 * + iResourceId: int.
	 * + iUserId: int.
	 * + sFullName: string.
	 * + UserProfileImg_Url: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/friendrequested
	 *
	 * @param array $aData
	 * @return array
	 */
	public function friendrequested($aData)
	{
		if (!isset($aData['iPage']))
			$aData['iPage'] = 1;
		
		if ($aData['iPage'] == '0')
			return array();
		
		// Get Viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get friends requested!")
			);
		}
		//check notificcation type you want to receive notifications
		$notificationSettings = Zend_Json::decode(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmobile_notifications', ''));
		if (in_array('friend_request', $notificationSettings))
		{
			return array();
		}
		// Mark read action
		$notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$db = $notificationsTable -> getAdapter();
		// $db -> beginTransaction();
		try
		{
			// Get friend request notifications
			$notifications = $notificationsTable -> fetchAll("`user_id` = {$viewer->getIdentity()} AND `type` = 'friend_request'");
			if ($notifications)
			{
				foreach ($notifications as $notification)
				{
					$notification -> read = 1;
					// set it as read
					$notification -> mitigated = 1;
					$notification -> save();
				}
				// $db -> commit();
			}
		}
		catch (Exception $e)
		{
			// $db -> rollBack();
			throw $e;
		}

		// Get Friend request but not confirm yet
		$userTb = Engine_Api::_() -> getDbTable('membership', 'user');
		$select = $userTb -> select() -> where("user_id = ?", $viewer -> getIdentity()) -> where("active = 0 AND resource_approved = 1 AND user_approved = 0");
		//$freqs = $userTb -> fetchAll($select);
		
		//starting paging
		$paginator = Zend_Paginator::factory($select);
		
		//Set current page
		if (!empty($aData['iPage'])) {
			$paginator -> setCurrentPageNumber($aData['iPage'], 1);
		}
		//Item per page
		$itemPerPage = (isset($aData['iLimit']) && ((int)$aData['iLimit'] > 0)) ? $aData['iLimit'] : 5;
		$paginator -> setItemCountPerPage($itemPerPage);
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $itemPerPage);
		if ($aData['iPage'] > $totalPage)
			return array();
		
		$freqs = $paginator;
		$friendUsers = array();
		foreach ($freqs as $freq)
		{
			$friendUser = Engine_Api::_() -> user() -> getUser($freq -> resource_id);
			//photoURL
			$sProfileImage = $friendUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_USER_ICON;
			}
			
			$friendUsers[] = array(
				'iResourceId' => $freq -> resource_id,
				'iUserId' => $freq -> user_id,
				'sFullName' => $friendUser -> getTitle(),
				'UserProfileImg_Url' => $sProfileImage,
				'iTimeStamp'=> time()
			);
		}
		
		return $friendUsers;
	}

    public function getEnabledNotificationTypes(){
        $enabledNotificationTypes = array();
        foreach( Engine_Api::_()->getDbtable('NotificationTypes', 'activity')->getNotificationTypes() as $type ) {
          $enabledNotificationTypes[] = $type->type;
        }
        
        return $enabledNotificationTypes;
    }


	/**
	 * Input data:
	 * + iLastNotificationId: int.
	 *
	 * Output data:
	 * + iNotificationId: int.
	 * + sMessage: string.
	 * + iUserId: int.
	 * + iOwnerUserId: int.
	 * + sFullName: string.
	 * + sType: string.
	 * + iItemId: int.
	 * + sItemType: string
	 * + iIsSeen: int.
	 * + sUserImage: string.
	 * + iTimeStamp: int.
	 * + sTime: string.
	 * + sTimeConverted: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/notification
	 *
	 * @param array $aData
	 * @return array
	 */
	public function fetch($aData)
	{
	    extract($aData);
        
        // Get Viewer
        $viewer = $this->getViewer();
        $iLimit =  @$iLimit?intval($iLimit):20;
        $iMaxId = intval($iMaxId);
        
        if (!$viewer -> getIdentity()){
            return array();
        }
        
        

        $notificationsTable = Engine_Api::_() -> getDbtable('notifications', 'activity');
        
        $notificationsTable->update(array(
                'mitigated'=>1,
            ),array(
                'type!=?'=>'friend_request',
                'mitigated=?'=>0,
                'user_id=?'=>$viewer->getIdentity(),
            ));
            
        // Get notifications
        $select = $notificationsTable 
          -> select() 
          -> where("`user_id` = ?", $viewer -> getIdentity())
          -> where('object_type IN (?)', Engine_Api::_()->getItemTypes())  
          -> where("`read` = 0") 
          -> order('read ASC') 
          -> order('notification_id DESC');
          
        //check notificcation type you want to receive notifications
        $notificationSettings = Zend_Json::decode(Engine_Api::_() -> getApi('settings', 'core')
             -> getSetting('ynmobile_notifications', ''));
        // add friend request to do not fetch list.
        
        
        $notificationSettings[] =  'friend_request';
        
        if($notificationSettings){
            $select -> where("type NOT IN (?)", $notificationSettings);
        }
        
        $select->where('type in (?)', $this->getEnabledNotificationTypes());
        
        if ($iMaxId){
            $select -> where("`notification_id` < ?", $iMaxId);
        }
                
        $select->limit($iLimit);
        
        // echo $select;
        // exit;
        
        return Ynmobile_AppMeta::_export_all($select, array('listing'));
	}

	/**
	 * Input data:
	 * + iNotificationId: int, required.
	 *
	 * Output data:
	 * + iNotificationId: int.
	 * + sMessage: string.
	 * + iUserId: int.
	 * + iOwnerUserId: int.
	 * + sFullName: string.
	 * + sType: string.
	 * + iItemId: int.
	 * + sItemType: string
	 * + iIsSeen: int.
	 * + sUserImage: string.
	 * + iTimeStamp: int.
	 * + sTime: string.
	 * + sTimeConverted: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/detail
	 *
	 * @param array $aData
	 * @return array
	 */
	public function detail($aData)
	{
	    extract($aData);
        
		$iNotificationId = intval(@$iNotificationId);
		
		// Get notifications
		$notiTable = Engine_Api::_() -> getDbTable('notifications', 'activity');
		$update = $notiTable ->findRow($iNotificationId);
        
		if (!$update){
			return array(
				'error_code' => 1,
				'error_message' => "Notification not found!",
			);
		}
        
        return Ynmobile_AppMeta::_export_one($update, $entry);
	}

	/**
	 * Input data:
	 * + iNotificationId: int, optional.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see notification/makeread
	 *
	 * @param array $aData
	 * @return array
	 */
	public function makeread($aData)
	{
	    extract($aData);
        
	    $iNotificationId =  intval(@$iNotificationId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
		$notificationsTable = Engine_Api::_() 
		  -> getDbtable('notifications', 'activity');
		
        $notification = $notificationsTable->findRow($iNotificationId);
        
        if (!$notification){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Notification is not valid!")
            );
        }
        $notification -> read = 1;
        $notification -> save();
        // Commit
        
        return array(
            'error_code' => 0,
            'error_message' => ""
        );
	}

	/**
	 * Input data:
	 * + N/A
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V6.0
	 * @see notification/markreadall
	 *
	 * @param array $aData
	 * @return array
	 */
	public function markreadall($aData)
	{
		$viewer =  $this->getViewer();
        
		if (!$viewer -> getIdentity()){
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to mark read all notifications!")
			);
		}
        
		Engine_Api::_() -> getDbtable('notifications', 'activity')
         -> markNotificationsAsRead($viewer);
    		return array(
    			'error_code' => 0,
    			'error_message' => ""
    		);
	}

}
