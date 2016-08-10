<?php
/**
*
* @category   Application_Ynmobile
* @package    Ynmobile
* @copyright  Copyright 2014 YouNet Company
* @license    http://socialengine.younetco.com
* @author     LongL
*/

class Ynmobile_Api_Chat extends Core_Api_Abstract
{
	public function notification($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$viewer->getIdentity())
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing viewer"),
					'error_code' => 1
			);
		}
		
		$whisperTbl = Engine_Api::_()->getDbTable("whispers","chat");
		$whisperName = $whisperTbl->info('name');
		$select = $whisperTbl->select()
		->from($whisperName, array("sender_id" => "$whisperName.sender_id"))
		->where("recipient_id = ?", $viewer->getIdentity())
		->where("`read` = 0")
		->group("sender_id");
		
		$numerOfConversation = count($whisperTbl->fetchAll($select));
		return array(
			'iNotificationAmount' => $numerOfConversation
		);
	}
	
	public function markAsRead($aData)
	{
		extract($aData);
		$iUserId = $iItemId;
		$viewer = Engine_Api::_()->user()->getViewer();
		$whisperTbl = Engine_Api::_()->getDbTable("whispers","chat");
		$whisperName = $whisperTbl->info('name');
		
		$where = array();
		$where[] = $whisperTbl->getAdapter()->quoteInto('recipient_id = ?', $viewer->getIdentity());
		$where[] = $whisperTbl->getAdapter()->quoteInto('sender_id = ?', $iUserId);
		
		$whisperTbl->update(array("read" => 1), $where);
		
		return true;
	}
	
	protected function getNewMessagesUserIds()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$whisperTbl = Engine_Api::_()->getDbTable("whispers","chat");
		$whisperName = $whisperTbl->info('name');
		$select = $whisperTbl->select()
		->from($whisperName, array("sender_id" => "$whisperName.sender_id"))
		->where("recipient_id = ?", $viewer->getIdentity())
		->where("`read` = 0")
		->group("sender_id");
		
		//ONLY GET SENDER
		$whispers = $whisperTbl->fetchAll($select);
		$ids = array();
		foreach ($whispers as $whisper)
		{
			$ids[] = $whisper->sender_id;
		}
		return $ids;
	}
	
	protected function getOnlineUserIds()
	{
		$chatUserTbl = Engine_Api::_()->getDbTable("users","chat");
		$select = $chatUserTbl->select()->where("state > 0");
		$chatUsers = $chatUserTbl->fetchAll($select);
		$ids = array();
		foreach ($chatUsers as $user)
		{
			$ids[] = $user->user_id;
		}
		return $ids;
		
	}
	
	public function getchatlist($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//VIEWER'S FRIEND LIST
		$friends = $viewer->membership()->getMembershipsOf(true);
		$friendArr = array();
		foreach ($friends as $friend)
		{
			$friendArr[$friend->getIdentity()] = $friend;
		}
		
		$newMessUserIds = $this->getNewMessagesUserIds();
		$onlineUserIds = $this->getOnlineUserIds();
		$result = array();

		//PUT USERS THAT HAVE NEW MESSAGE TO RESULT
		if (count($newMessUserIds))
		{
			foreach ($newMessUserIds as $id)
			{
				if (isset($friendArr[$id]))
				{
					$pos = array_search($id, $onlineUserIds);
					$online = 0;
					if( $pos !== FALSE )
					{
						$online = 1;
						unset($onlineUserIds[$pos]);
					}
					
					$sImage = $this->getUserPhoto($friendArr[$id]);
					$result[] = array (
							'iItemId' => $id,
							'sItemType' => 'user',
							'sFullName' => $friendArr[$id]->getTitle(),
							'sImage' => $sImage,
							'bHasNewMessage' => true,
							'sStatus' => ($online) 
								? Zend_Registry::get("Zend_Translate")->_("online")
								: Zend_Registry::get("Zend_Translate")->_("offline"),
					);
					unset($friendArr[$id]);
				}
			}
		}
		
		
		//PUT ONLINE USERS TO RESULT
		if (count($onlineUserIds))
		{
			foreach ($onlineUserIds as $id)
			{
				if (isset($friendArr[$id]))
				{
					$sImage = $this->getUserPhoto($friendArr[$id]);
					$result[] = array (
							'iItemId' => $id,
							'sItemType' => 'user',
							'sFullName' => $friendArr[$id]->getTitle(),
							'sImage' => $sImage,
							'bHasNewMessage' => false,
							'sStatus' => Zend_Registry::get("Zend_Translate")->_("online"),
					);
					unset($friendArr[$id]);
				}
			}
		}
		
		
		foreach ($friendArr as $friend)
		{
			$sImage = $this->getUserPhoto($friend);
			$result[] = array (
					'iItemId' => $friend->getIdentity(),
					'sItemType' => 'user',
					'sFullName' => $friend->getTitle(),
					'sImage' => $sImage,
					'bHasNewMessage' => false,
					'sStatus' => Zend_Registry::get("Zend_Translate")->_("offline"),
			);
		}
		
		return $result;
	} 
	
	protected function getUserPhoto($user)
	{
		$profileimage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($profileimage != "")
		{
			$profileimage = Engine_Api::_() -> ynmobile() -> finalizeUrl($profileimage);
		}
		else
		{
			$profileimage = NO_USER_ICON;
		}
		return $profileimage;
	}
	
	public function getmessages($aData)
	{
		extract($aData);
		if (!isset($sItemType) || $sItemType != 'user')
		{
			$sItemType = "user";
		}
		
		if (!isset($iItemId) || !($iItemId))
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing item id"),
					'error_code' => 1
			);
		}
				
		$user = Engine_Api::_()->getItem("user", $iItemId);
		$userId = $iItemId;
		if (!$user->getIdentity())
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This user is not exsited!"),
					'error_code' => 1
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewerId = $viewer->getIdentity();
		
		if ( !isset($iMessageAmount) || empty($iMessageAmount) || (!$iMessageAmount) )
		{
			$iMessageAmount = 10;
		}
		
		$whisperTbl = Engine_Api::_()->getDbTable("whispers","chat");
		$whereClause = "(sender_id = $userId AND recipient_id = $viewerId) OR (sender_id = $viewerId AND recipient_id = $userId)";
		$select = $whisperTbl->select()
			->where ($whereClause)
			->limit($iMessageAmount)
		;
		
		if ( isset($sAction) && (in_array($sAction, array("new", "more"))) )
		{
			if ($sAction === "new")
			{
				if (isset($iMinId))
					$select = $select->where("whisper_id > ?", $iMinId)->order ("whisper_id ASC");
				else
					$select = $select->order ("whisper_id ASC");
			}
			elseif ($sAction === "more")
			{
				if (isset($iMaxId))
					$select = $select->where("whisper_id < ?", $iMaxId)->order ("whisper_id DESC");
				else
					$select = $select->order ("whisper_id DESC");
			}
		}
		else
		{
			$select -> order ("whisper_id DESC");
		}
		
		$whispers = $whisperTbl->fetchAll($select);
		$result = array();
		foreach ($whispers as $whisper)
		{
			$sender = Engine_Api::_()->getItem('user', $whisper->sender_id);
			$result[] = array(
					'iMessageId' => $whisper->whisper_id,
					'iItemId' => $iItemId,
					'sItemType' => 'user',
					'iSenderId' => $whisper->sender_id,
					'sSenderName' => $sender->getTitle(),
					'sSenderImage' => $this->getUserPhoto($sender),
					'sMessage' => $whisper->body,
					'iTimestamp' => strtotime($whisper->date),
			);
		}
		
		if ($sAction != "new")
		{
			$result = array_reverse($result);
		}
		
		// UPDATE READ status for whisper table
		$this->markAsRead($aData);
		return $result;
	}
	
	public function sendmessage($aData)
	{
		extract($aData);
		
		if (!isset($sItemType) || $sItemType != 'user')
		{
			$sItemType = "user";
		}
		
		// for chat message
		
		// if(rand(1,3) == 2){ // test case can not send data
			// return array(
					// 'error_message' => Zend_Registry::get("Zend_Translate")->_("This message show due to test case only."),
					// 'error_code' => 1
			// );
		// }
		
		if (!isset($iItemId) || !$iItemId)
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing item id"),
					'error_code' => 1
			);
		}
		
		if (!isset($sMessage) || empty($sMessage))
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Chat message can not be empty!"),
					'error_code' => 1
			);
		}
		
		$userTable = Engine_Api::_()->getDbtable('users', 'chat');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// Check for chat user
		$userTable->check($viewer);
		
		// Check for target user
		$targetUserId = (int) $iItemId;
		$targetUser = $userTable->find($targetUserId)->current();
		
		// Do it!
		$censor  = new Engine_Filter_Censor();
		$message = $censor->filter( $sMessage );
		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
		$message = htmlspecialchars($message, ENT_NOQUOTES, 'UTF-8');
		
		if( Engine_String::strlen($message) > 1023 ) {
			$message = Engine_String::substr($message, 0, 1023);
		}
		
		// Start transaction
		$db = $userTable->getAdapter();
		$db->beginTransaction();
		
		try
		{
			// Send message
			if( null !== $targetUser ) 
			{
				$whisperObject = $targetUser->whisper($viewer, $message);
			}
			else
			{
				$whisperTable = Engine_Api::_()->getDbtable('whispers', 'chat');
				$whisperObject = $whisperTable->createRow();
				$whisperObject->setFromArray(array(
						'recipient_id' => $iItemId,
						'sender_id' => $viewer->user_id,
						'body' => $message,
						'date' => date('Y-m-d H:i:s'),
						'read' => 0
				))->save();
			}
			
			$db->commit();
		
			return array(
				'error_code' => 0,
				'message' => Zend_Registry::get("Zend_Translate")->_("Sent chat message successfully!"),
				'iMessageId' => $whisperObject->whisper_id,
				'iItemId' => $iItemId,
				'sItemType' => 'user',
				'iSenderId' => $whisperObject->sender_id,
				'sSenderName' => $viewer->getTitle(),
				'sSenderImage' => $this->getUserPhoto($viewer),
				'sMessage' => $whisperObject->body,
				'iTimestamp' => strtotime($whisperObject->date),
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
	
	public function changestatus($aData)
	{
		extract($aData);
		if (!isset($sStatus))
		{
			return array(
					'error_message' => Zend_Registry::get("Zend_Translate")->_("User status is required!"),
					'error_code' => 1
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$userTable = Engine_Api::_()->getDbtable('users', 'chat');
		
		$sStatus = strtolower($sStatus);
		//ONLINE
		if ($sStatus == 'online')
		{
			$chatUserId = $viewer->getIdentity();
			$chatUser = $userTable->find($chatUserId)->current();
			if( null === $chatUser ) 
			{
				$chatUser = $userTable->createRow();
				$chatUser->user_id = $viewer->getIdentity();
			}
			
			$chatUser->state = 1;
			$chatUser->date = date('Y-m-d H:i:s');
			$chatUser->save();
			
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get("Zend_Translate")->_("Changed status to ONLINE successfully!"),
					'sStatus' => 'online',
			);
		}
		//OFFLINE
		else if ($sStatus == 'offline')
		{
			$chatUserId = $viewer->getIdentity();
			$chatUser = $userTable->find($chatUserId)->current();
			if( null !== $chatUser )
			{
				$chatUser->state = 0;
				$chatUser->date = date('Y-m-d H:i:s');
				$chatUser->save();
			}
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get("Zend_Translate")->_("Changed status to OFFLINE successfully!"),
					'sStatus' => 'offline',
			);
		}
		else
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("User status is invalid!"),
			);
		}
	}
	
	public function ping($aData)
	{
		extract($aData);
		$eventTable = Engine_Api::_()->getDbtable('events', 'chat');
		$roomTable = Engine_Api::_()->getDbtable('rooms', 'chat');
		$roomUserTable = Engine_Api::_()->getDbtable('RoomUsers', 'chat');
		$userTable = Engine_Api::_()->getDbtable('users', 'chat');
		
		// Check viewer
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if(!$viewer || !$viewer->getIdentity()){
			return array('iLastTimeStamp' => time(),
				'aNewMessages' => array()
				);
		}
		
		// Check for chat user
		$user = $userTable->check($viewer);
		$user->setUser($viewer);
		
		// Now get all events
		$lastEventTime = $prevLastEventTime = time();
		if (isset($iLastTimeStamp) && $iLastTimeStamp!= 0)
		{
			$lastEventTime = $prevLastEventTime = $iLastTimeStamp;
		}
		
		if (isset($iGetNewMessages) && $iGetNewMessages )
		{
			$events = array();
			foreach( $this->getChatEvents($viewer, $prevLastEventTime) as $event )
			{
				$eventTemp = $event->toRemoteArray();
				$sender = Engine_Api::_()->getItem('user', $eventTemp['sender_id']);
				$events[] = array(
						'iMessageId' =>  $eventTemp['whisper_id'],
						'sMessage' =>  $eventTemp['body'],
						'iReceiverId' =>  $eventTemp['recipient_id'],
						'iSenderId' =>  $eventTemp['sender_id'],
						'sSenderName' => $sender->getTitle(),
						'sSenderImage' => $this->getUserPhoto($sender),
						'iRecipientId' =>  $eventTemp['recipient_id'],
						'iTimestamp' => strtotime($eventTemp['date'])
				);
				$lastEventTime = strtotime($event->date);
			}
			
			return array(
				'iLastTimeStamp' => $lastEventTime,
				'aNewMessages' => $events
		);
		}
		else
		{
			return array(
				'iLastTimeStamp' => $lastEventTime,
			);
		}
		
	}
	
	public function getChatEvents(User_Model_User $user, $time = null)
	{
		$eventTable = Engine_Api::_()->getDbtable('events', 'chat');
		$select = $eventTable->select()
			->where('user_id = ?', $user->user_id)
			->where('type = ?', 'chat')
			->order('date ASC');
	
		if( null !== $time ) {
			$select->where('date > FROM_UNIXTIME(?)', $time);
		}
	
		return $eventTable->fetchAll($select);
	}
	
	public function getstatus($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$userTable = Engine_Api::_()->getDbtable('users', 'chat');
		$chatUserId = $viewer->getIdentity();
		$chatUser = $userTable->find($chatUserId)->current();
		if( null === $chatUser )
		{
			return array(
				'sStatus' => 'offline',
			);
		}
		else
		{
			/* Actually, we have 4 states
			 * 0 : 'offline',
			 * 1 : 'online',
			 * 2 : 'idle' (user is doing nothing within 60s) 
			 * 3 : 'away'  
			 */
			
			if ($chatUser->state == 0)
			{
				return array(
						'sStatus' => 'offline',
				);
			}
			else //state can be 1 or 2 or 3
			{
				return array(
						'sStatus' => 'online',
				);
			}
		}
	}
	
}

 