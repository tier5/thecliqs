<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Message.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Service_Message extends Ynmobile_Service_Base
{

    protected $module =  'messages';
    protected $mainItemType =  'messages';
    
    
    function fetch($aData){
        $filter  = isset($aData['filter'])?$aData['filter']:'inbox';

        if($filter== 'sent'){
            return $this->sent($aData);
        }

        return $this->inbox($aData);
    }

    /**
     * Input data:
     * + sUserIds: string, required. Ex: "5,4,6"
     * + sSubject: string, required.
     * + sText: string, required.
     * + attachmentData: array optional
     *
     * Output data:
     * + result: bool.
     * + error_code: int.
     * + iItemId: int.
     * + error_message: string.
     *
     * @see Mobile - API phpFox/Api V1.0
     * @see message/compose
     *
     * @see Mail_Service_Process
     * @param array $aData
     * @return array
     */
    public function compose($aData)
    {
        // Get params
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $maxRecipients = 10;

        Engine_Api:: _() ->core()->setSubject($viewer);

        // Process
        $db = Engine_Api::_() -> getDbtable('messages', 'messages') -> getAdapter();
        $db -> beginTransaction();
        try
        {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = isset($aData['attachmentData']) ? $aData['attachmentData'] : null;
            if (!empty($attachmentData) && !empty($attachmentData['type']))
            {
                $type = $attachmentData['type'];

                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data)
                {
                    if (!empty($data['composer'][$type]))
                    {
                        $config = $data['composer'][$type];
                    }
                }
                if ($config)
                {
                    $plugin = Engine_Api::_() -> loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin -> $method($attachmentData);
                    $parent = $attachment -> getParent();
                    if ($parent -> getType() === 'user')
                    {
                        $attachment -> search = 0;
                        $attachment -> save();
                    }
                    else
                    {
                        $parent -> search = 0;
                        $parent -> save();
                    }
                }
            }
            
            $recipientsUsers = null;
            $recipients = null;
            if (isset($aData['sParentType']) && isset($aData['iParentId']))
            {
                $toObject = Engine_Api::_()->getItem($aData['sParentType'], $aData['iParentId']);
                if( $toObject instanceof Core_Model_Item_Abstract &&
                        method_exists($toObject, 'membership') )
                {
                    $recipientsUsers = $toObject->membership()->getMembers();
                    $recipients = $toObject;
                }
            }
            else
            {
                $recipients = preg_split('/[,. ]+/', $aData['sUserIds']);
                // clean the recipients for repeating ids
                // this can happen if recipient is selected and then a friend list is selected
                $recipients = array_unique($recipients);
                // Slice down to 10
                $recipients = array_slice($recipients, 0, $maxRecipients);
                // Get user objects
                $recipientsUsers = Engine_Api::_() -> getItemMulti('user', $recipients);
                // Validate friends
                if ('friends' == Engine_Api::_() -> authorization() -> getPermission($viewer, 'messages', 'auth'))
                {
                    foreach ($recipientsUsers as &$recipientUser)
                    {
                        if (!$viewer -> membership() -> isMember($recipientUser))
                        {
                            return array(
                                    'result' => FALSE,
                                    'error_code' => 1,
                                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('One of the members specified is not in your friends list.')
                            );
                        }
                    }
                }
            }
            
            if (is_null($recipientsUsers) || is_null($recipients))
            {
                return array(
                        'error_code' => 2,
                        'error_message' => Zend_Registry::get('Zend_Translate') -> _('Can not find the recipients.')
                );
            }

            $aData['sText'] = html_entity_decode($aData['sText'], ENT_QUOTES, 'UTF-8');
            // Create conversation
            $conversation = Engine_Api::_() -> getItemTable('messages_conversation') -> send($viewer, $recipients, $aData['sSubject'], $aData['sText'], $attachment);

            // Send notifications
            foreach ($recipientsUsers as $user)
            {
                if ($user -> getIdentity() == $viewer -> getIdentity())
                {
                    continue;
                }
                Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $conversation, 'message_new');
            }

            // Increment messages counter
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('messages.creations');

            // Commit
            $db -> commit();
            return array(
                'result' => TRUE,
                'iItemId' => $conversation -> conversation_id
            );
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
                'error_debug'=> $e->getMessage(),
            );
        }
    }

    /**
     * Input data:
     * + iItemId: int, required.
     * + sText: string, required.
     *
     * Output data:
     * + result: bool.
     * + error_code: int.
     * + error_message: string.
     *
     * @see Mobile - API SE/Api V1.0
     * @see message/reply
     *
     * @param array $aData
     * @return array
     */
    public function reply($aData)
    {
        $iItemId = isset($aData['iItemId']) ? $aData['iItemId'] : 0;
        if ($iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Get conversation info
        $conversation = Engine_Api::_() -> getItem('messages_conversation', $iItemId);

        // Make sure the user is part of the conversation
        if (!$conversation || !$conversation -> hasRecipient($viewer))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $recipients = $conversation -> getRecipients();
        if (!$conversation -> locked)
        {
            $db = Engine_Api::_() -> getDbtable('messages', 'messages') -> getAdapter();
            $db -> beginTransaction();
            try
            {
                // Try attachment getting stuff
                $attachment = null;
                $attachmentData = isset($aData['attachmentData']) ? $aData['attachmentData'] : null;
                if (!empty($attachmentData) && !empty($attachmentData['type']))
                {
                    $type = $attachmentData['type'];
                    $config = null;
                    foreach (Zend_Registry::get('Engine_Manifest') as $data)
                    {
                        if (!empty($data['composer'][$type]))
                        {
                            $config = $data['composer'][$type];
                        }
                    }
                    if ($config)
                    {
                        $plugin = Engine_Api::_() -> loadClass($config['plugin']);
                        $method = 'onAttach' . ucfirst($type);
                        $attachment = $plugin -> $method($attachmentData);
                        
                        $parent = $attachment -> getParent();
                        if ($parent -> getType() === 'user')
                        {
                            $attachment -> search = 0;
                            $attachment -> save();
                        }
                        else
                        {
                            $parent -> search = 0;
                            $parent -> save();
                        }
                    }
                }

                $aData['sText'] = html_entity_decode($aData['sText'], ENT_QUOTES, 'UTF-8');
                $conversation -> reply($viewer, $aData['sText'], $attachment);

                // Send notifications
                foreach ($recipients as $user)
                {
                    if ($user -> getIdentity() == $viewer -> getIdentity())
                    {
                        continue;
                    }
                    Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $conversation, 'message_new');
                }

                // Increment messages counter
                Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('messages.creations');

                $db -> commit();
                return array(
                    'result' => TRUE,
                    'iItemId' => $conversation -> conversation_id
                );
            }
            catch( Exception $e )
            {
                $db -> rollBack();
                return array(
                    'error_code' => 1,
                    'error_message' => $e->getMessage(),
                );
            }
        }
    }

    /**
     * Delete conversation from data.
     *
     * Input data:
     * + iItemId: int, required.
     *
     * Output data:
     * + result: bool.
     * + error_code: int.
     * + error_message: string.
     *
     * @see Mobile - API SE/Api V1.0
     * @see message/delete
     *
     * @param array $aData
     * @return array
     */
    public function delete($aData)
    {
        $iItemId = isset($aData['iItemId']) ? $aData['iItemId'] : 0;
        if ($iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this item!"),
                'result' => 0
            );
        }
        $viewer_id = $viewer -> getIdentity();
        $db = Engine_Api::_() -> getDbtable('messages', 'messages') -> getAdapter();
        $db -> beginTransaction();
        try
        {
            $recipients = Engine_Api::_() -> getItem('messages_conversation', $iItemId) -> getRecipientsInfo();
            foreach ($recipients as $r)
            {
                if ($viewer_id == $r -> user_id)
                {
                    $r -> inbox_deleted = true;
                    $r -> outbox_deleted = true;
                    $r -> save();
                }
            }
            $db -> commit();
            return array('result' => TRUE);
        }
        catch (Exception $e)
        {
            $db -> rollback();
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!")
            );
        }
    }

    /**
     * Input data:
     * + iPage: int, optional.
     * + iLimit: int, optional.
     * + sSearch: string
     *
     * Output data:
     * iConversationId: int
     * iOwnerId: int
     * sOwnerUserName: string
     * sOwnerFullName: string
     * sOwnerImage: string
     * iOwnerLevelId: int
     * iViewerId: int
     * sViewerUserName: string
     * sViewerFullName: string
     * sViewerImage: string
     * iViewerLevelId: int
     * sTitle: string
     * sBody: string
     * sTime: string
     * sTimeConverted: string

     * @see Mobile - API SE/Api V1.0
     * @see message/inbox
     *
     * @param array $aData
     * @return array
     */
    public function inbox($aData)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get inbox message!"),
                'result' => 0
            );
        }
        $rName = Engine_Api::_() -> getDbtable('recipients', 'messages') -> info('name');
        $cTable = Engine_Api::_() -> getDbtable('conversations', 'messages');
        $cName = $cTable -> info('name');
        $select = Engine_Api::_() -> getDbtable('conversations', 'messages') -> select() -> from($cName) -> joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null) -> where("`{$rName}`.`user_id` = ?", $viewer -> getIdentity()) -> where("`{$rName}`.`inbox_deleted` = ?", 0) -> order(new Zend_Db_Expr('inbox_updated DESC'));

        if (isset($aData['sSearch']))
        {
            $select -> where("`{$cName}`.`title` like ?", "%" . $aData['sSearch'] . "%");
        }

        //starting paging
        $paginator = Zend_Paginator::factory($select);

        //Set current page
        if (!empty($aData['iPage']))
        {
            $paginator -> setCurrentPageNumber($aData['iPage'], 1);
        }

        //Item per page
        $itemPerPage = (isset($aData['iLimit']) && ((int)$aData['iLimit'] > 0)) ? $aData['iLimit'] : 5;
        $paginator -> setItemCountPerPage($itemPerPage);
        
        $totalPage = (integer) ceil($paginator->getTotalItemCount() / $itemPerPage);
        if ($aData['iPage'] > $totalPage)
            return array();
        
        $conversations = $paginator;

        $aMessages = array();
        foreach ($conversations as $conversation)
        {
//          if ($conversation->resource_type == 'group')
//          {
//              continue;
//          }
            
            $message = $conversation -> getInboxMessage($viewer);
            $recipient = $conversation -> getRecipientInfo($viewer);
            $resource = "";
            $sender = "";
            if ($conversation -> hasResource() && ($resource = $conversation -> getResource()))
            {
                $sender = $resource;
            }
            else
            if ($conversation -> recipients > 1)
            {
                $sender = $viewer;
            }
            else
            {
                foreach ($conversation->getRecipients() as $tmpUser)
                {
                    if ($tmpUser -> getIdentity() != $viewer -> getIdentity())
                    {
                        $sender = $tmpUser;
                    }
                }
            }
            if ((!isset($sender) || !$sender) && $viewer -> getIdentity() !== $conversation -> user_id)
            {
                $sender = Engine_Api::_() -> user() -> getUser($conversation -> user_id);
            }
            if (!isset($sender) || !$sender)
            {
                $sender = new User_Model_User( array());
            }
            if ($sender -> getIdentity())
            {
                //photoURL
                $sViewerImage = $viewer -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
                if ($sViewerImage != "")
                {
                    $sViewerImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sViewerImage);
                }
                else
                {
                    $sViewerImage = NO_USER_ICON;
                }

                $sOwnerImage = $sender -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
                if ($sOwnerImage != "")
                {
                    $sOwnerImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sOwnerImage);
                }
                else
                {
                    if ($conversation->resource_type == "group" || $conversation->resource_type == "advgroup")
                        $sOwnerImage = NO_GROUP_ICON;
                    else
                        $sOwnerImage = NO_USER_ICON;
                }

                //userName
                $sViewerUsername = $viewer -> username;
                if (!trim($sViewerUsername))
                    $sViewerUsername = $viewer -> getIdentity();

//              $sOwnerUsername = $sender -> username;
//              if (!trim($sOwnerUsername))
//                  $sOwnerUsername = $sender -> getIdentity();
                
                $sTimeStamp = strtotime($message -> date);
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

                ((isset($message) && '' != ($title = trim($message -> getTitle()))) || (isset($conversation) && '' != ($title = trim($conversation -> getTitle()))) || $title = Zend_Registry::get('Zend_Translate') -> _('No Subject'));

                $aMessages[] = array(
                    'iConversationId' => $conversation -> conversation_id,
                    'iOwnerId' => $sender -> getIdentity(),
//                  'sOwnerUserName' => $sOwnerUsername,
                    'sOwnerFullName' => $sender -> getTitle(),
                    'sOwnerImage' => $sOwnerImage,
//                  'iOwnerLevelId' => $sender -> level_id,
                    'iViewerId' => $viewer -> getIdentity(),
                    'sViewerUserName' => $sViewerUsername,
                    'sViewerFullName' => $viewer -> getTitle(),
                    'sViewerImage' => $sViewerImage,
                    'iViewerLevelId' => $viewer -> level_id,
                    'sTitle' => $title,
                    'sBody' => nl2br(html_entity_decode($message -> body)),
                    'sTime' => $sTime,
                    'bIsRead' => ($recipient->inbox_read == '1') ? true : false,
                    'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($sTimeStamp),
                    'iTimeStamp' => $sTimeStamp, 
                    'sResourceType' => $conversation->resource_type,
                );
            }
        }
        return $aMessages;
    }

    /**
     * Input data:
     * + iItemId: int, required.
     *
     * Output data:
     * iMessageId: int
     * iUserId: int
     * sUserName: string
     * sFullName: string
     * sImage: string
     * sBody: string
     * sTime: string
     * sTimeConverted: string
     * aAttachments: array()

     * @see Mobile - API SE/Api V1.0
     * @see message/inbox
     *
     * @param array $aData
     * @return array
     */
    public function detail($aData)
    {
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Get conversation info
        $conversation = Engine_Api::_() -> getItem('messages_conversation', $iItemId);

        // Make sure the user is part of the conversation
        if (!$conversation || !$conversation -> hasRecipient($viewer))
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $messages = $conversation -> getMessages($viewer);
        $aResult = array();
        foreach ($messages as $message)
        {
            $user = Engine_Api::_() -> user() -> getUser($message -> user_id);
            //photoURL
            $sUserImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
            if ($sUserImage != "")
            {
                $sUserImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImage);
            }
            else
            {
                $sUserImage = NO_USER_ICON;
            }

            //userName
            $sUsername = $user -> username;
            if (!trim($sUsername))
                $sUsername = $user -> getIdentity();

            $sTimeStamp = strtotime($message -> date);
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
            $aAttachments = array();
            $attachment_type =  $message -> attachment_type;
            
            if($attachment_type  == 'album_photo'){
                if(!Engine_Api::_()->hasItemType($attachment_type) && Engine_Api::_()->hasItemType('advalbum_photo')){
                    $attachment_type = 'advalbum_photo';
                }
            }else if ($attachment_type  == 'advalbum_photo'){
                if(!Engine_Api::_()->hasItemType($attachment_type) && Engine_Api::_()->hasItemType('album_photo')){
                    $attachment_type = 'album_photo';
                }
            }

            if (!empty($message -> attachment_type) && null !== ($attachment = Engine_Api::_() -> getItem($attachment_type, $message -> attachment_id)))
            {
                $aAttachments['sModule'] = $attachment -> getModuleName();
                $aAttachments['sTitle'] = $attachment -> getTitle();
                $aAttachments['sDescription'] = $attachment -> getDescription();
                $aAttachments['iId'] = $attachment -> getIdentity();
                $aAttachments['sType'] = $attachment -> getType();
                $sLink = $attachment -> getHref();
                $sLink = Engine_Api::_() -> ynmobile() -> finalizeUrl($sLink);
                $aAttachments['sLink_Url'] = $sLink;
                if (null !== $attachment -> getPhotoUrl())
                {
                    $aAttachments['sUrl'] = Engine_Api::_() -> ynmobile() -> finalizeUrl($attachment -> getPhotoUrl());
                }
                else
                if ($message -> attachment_type == "music_playlist_song")
                {
                    $aAttachments['sUrl'] = Engine_Api::_() -> ynmobile() -> finalizeUrl($attachment -> getFilePath());
                }
            }
            $aResult[] = array(
                'iConversationId' => $conversation -> getIdentity(),
                'sConversationTitle' => $conversation -> getTitle(),
                'iMessageId' => $message -> message_id,
                'iUserId' => $user -> getIdentity(),
                'sUserName' => $sUsername,
                'sFullName' => $user -> getTitle(),
                'sImage' => $sUserImage,
                'sBody' => nl2br(html_entity_decode($message -> body)),
                'sTime' => $sTime,
                'iTimeStamp'=>$sTimeStamp,
                'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($sTimeStamp),
                'aAttachments' => $aAttachments
            );
        }
        $conversation -> setAsRead($viewer);
        return $aResult;
    }

    /**
     * Input data:
     * + iPage: int, optional.
     * + iLimit: int, optional.
     * + sSearch: string
     *
     * Output data:
     * iConversationId: int
     * iOwnerId: int
     * sOwnerUserName: string
     * sOwnerFullName: string
     * sOwnerImage: string
     * iOwnerLevelId: int
     * iViewerId: int
     * sViewerUserName: string
     * sViewerFullName: string
     * sViewerImage: string
     * iViewerLevelId: int
     * sTitle: string
     * sBody: string
     * sTime: string
     * sTimeConverted: string

     * @see Mobile - API SE/Api V1.0
     * @see message/sent
     *
     * @param array $aData
     * @return array
     */
    public function sent($aData)
    {
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get outbox message!"),
                'result' => 0
            );
        }
        $rName = Engine_Api::_() -> getDbtable('recipients', 'messages') -> info('name');
        $cTable = Engine_Api::_() -> getDbtable('conversations', 'messages');
        $cName = $cTable -> info('name');
        $select = Engine_Api::_() -> getDbtable('conversations', 'messages') -> select() -> from($cName) -> joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null) -> where("`{$rName}`.`user_id` = ?", $viewer -> getIdentity()) -> where("`{$rName}`.`outbox_deleted` = ?", 0) -> order(new Zend_Db_Expr('outbox_updated DESC'));

        if (isset($aData['sSearch']))
        {
            $select -> where("`{$cName}`.`title` like ?", "%" . $aData['sSearch'] . "%");
        }

        //starting paging
        $paginator = Zend_Paginator::factory($select);

        //Set current page
        if (!empty($aData['iPage']))
        {
            $paginator -> setCurrentPageNumber($aData['iPage'], 1);
        }

        //Item per page
        $itemPerPage = (isset($aData['iLimit']) && ((int)$aData['iLimit'] > 0)) ? $aData['iLimit'] : 5;
        $paginator -> setItemCountPerPage($itemPerPage);
        
        $totalPage = (integer) ceil($paginator->getTotalItemCount() / $itemPerPage);
        if ($aData['iPage'] > $totalPage)
            return array();
        
        $conversations = $paginator;
        
        $aMessages = array();
        
        foreach ($conversations as $conversation)
        {
//          if ($conversation->resource_type == 'group')
//          {
//              continue;
//          }
            $message = $conversation -> getOutboxMessage($viewer);
            $resource = "";
            $sender = "";
            if ($conversation -> hasResource() && ($resource = $conversation -> getResource()))
            {
                $sender = $resource;
            }
            else
            if ($conversation -> recipients > 1)
            {
                $sender = $viewer;
            }
            else
            {
                foreach ($conversation->getRecipients() as $tmpUser)
                {
                    if ($tmpUser -> getIdentity() != $viewer -> getIdentity())
                    {
                        $sender = $tmpUser;
                    }
                }
            }
            
            if ((!isset($sender) || !$sender) && $viewer -> getIdentity() !== $conversation -> user_id)
            {
                $sender = Engine_Api::_() -> user() -> getUser($conversation -> user_id);
            }
            if (!isset($sender) || !$sender)
            {
                $sender = new User_Model_User( array());
            }
            if ($sender -> getIdentity())
            {
                //photoURL
                $sViewerImage = $viewer -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
                if ($sViewerImage != "")
                {
                    $sViewerImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sViewerImage);
                }
                else
                {
                    $sViewerImage = NO_USER_ICON;
                }

                $sOwnerImage = $sender -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
                if ($sOwnerImage != "")
                {
                    $sOwnerImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sOwnerImage);
                }
                else
                {
                    $sOwnerImage = NO_USER_ICON;
                }

                
                //userName
                $sViewerUsername = $viewer -> username;
                if (!trim($sViewerUsername))
                    $sViewerUsername = $viewer -> getIdentity();
                
//              $sOwnerUsername = $sender -> username;
//              if (!trim($sOwnerUsername))
//                  $sOwnerUsername = $sender -> getIdentity();
                
                                
                $sTimeStamp = strtotime($message -> date);
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
                ((isset($message) && '' != ($title = trim($message -> getTitle()))) || (isset($conversation) && '' != ($title = trim($conversation -> getTitle()))) || $title = $this -> translate('No Subject'));
                $aMessages[] = array(
                    'iConversationId' => $conversation -> conversation_id,
                    'iOwnerId' => $sender -> getIdentity(),
                    'sOwnerFullName' => $sender -> getTitle(),
                    'sOwnerImage' => $sOwnerImage,
                    'iViewerId' => $viewer -> getIdentity(),
                    'sViewerUserName' => $sViewerUsername,
                    'sViewerFullName' => $viewer -> getTitle(),
                    'sViewerImage' => $sViewerImage,
                    'iViewerLevelId' => $viewer -> level_id,
                    'sTitle' => $title,
                    'sBody' => nl2br(html_entity_decode($message -> body)),
                    'sTime' => $sTime,
                    'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($sTimeStamp),
                    'iTimeStamp' => $sTimeStamp,
                    'sResourceType' => $conversation->resource_type,
                );
            }
            
            
            
            
        }
        return $aMessages;
    }

    /**
     * Input data:
     * + iItemId: int, required.
     *
     * Output data:
     * + result: int.
     * + error_code: int.
     * + error_message: string.
     *
     * @see Mobile - API SE/Api V1.0
     * @see message/markread
     *
     * @param array $aData
     * @return array
     */
    public function markread($aData)
    {
        $iConversationId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        if ($iConversationId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to set markread this item!"),
                'result' => 0
            );
        }
        $conversation = Engine_Api::_() -> getItem('messages_conversation', $iConversationId);
        if (!$conversation)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        $conversation -> setAsRead($viewer);
        return array('result' => 1);
    }

    public function conversation_detail($aData)
    {
        extract($aData);
        if (!isset($iConversationId))
        {
            return array(
                    'error_code' => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing conversation id!")
            );
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $conversation = Engine_Api::_()->getItem('messages_conversation', $iConversationId);
        $recipients = $conversation->getRecipients();
        $users = array();
        if (count($recipients)){
            foreach($recipients as $recipient)
            {
                $users[] = array(
                    'iUserId' => $recipient->getIdentity(),
                    'sUserName' => $recipient->getTitle(),
                );
            }
        }
        
        $bCanReply = false;
        if( empty($conversation->resource_type) ) 
        {
            $blocked = false;
            $blocker = "";
            
            $viewer_blocked = false;
            $viewer_blocker = "";
            
            foreach($recipients as $recipient)
            {
                if ($viewer->isBlockedBy($recipient))
                {
                    $blocked = true;
                    $blocker = $recipient;
                }
                elseif ($recipient->isBlockedBy($viewer))
                {
                    $viewer_blocked = true;
                    $viewer_blocker = $recipient;
                }
            }
            
            if( (!$blocked && !$viewer_blocked) || (count(recipients) > 1))
            {
                $bCanReply = true;
            }
        }
        
        if ($conversation->locked)
        {
            $bCanReply = false;
        }
        
        $result = array(
            'sTitle' => $conversation->getTitle(),
            'aRecipients' => $users,
            'bCanReply' => $bCanReply,
        );
        return $result; 
    }
    
}
