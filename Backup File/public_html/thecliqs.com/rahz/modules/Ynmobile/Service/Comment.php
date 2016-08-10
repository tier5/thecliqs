<?php

class Ynmobile_Service_Comment extends Ynmobile_Service_Base{
    
    protected $module = 'activity';
    protected $mainItemTyp = 'activity_comment';
    
    /**
     * Input data:
     * + iItemId: int, required.
     * + sItemType: string, required.
     * + iCommentId: int, required.
     *
     * Output data:
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see comment/delete
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function remove($aData)
    {
        extract($aData);
        
        
        $viewer = $this -> getViewer();
        $iItemId = intval(@$iItemId);
        $sItemType = @$sItemType;
        
        $oItem = $this -> getWorkingItem($sItemType, $iItemId);
        
        if (!$oItem){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _('No comment or wrong parent'),
            );

        }
        
        
        

        try
        {
            $resource_type = 'activity_action';
            if(isset($oItem->resource_type)){
                $resource_type= $oItem->resource_type;
            }
            $oParent = Engine_Api::_()->getItem($resource_type, $oItem->resource_id);
            
            if(!$oParent){
                return array(
                    'error_code' => 1,
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('No Item or wrong parent'),
                );
            }

            $oParent->comments()->removeComment($iItemId);
            
            return array(
                'error_code' => 0,
                'error_message' => '',
            );
        }

        catch( Exception $e )
        {
            return array(
                //'error_message' => Zend_Registry::get('Zend_Translate') -> _('Comment does not exist or has been deleted!'),
                'error_message' => 'Can not remove this comment!',
                'debug_mesasge'=> $e->getMessage(), 
                'oItem'=> $oItem->toArray(),
                'error_code' => 1,
            );
        }
    }
    
    
    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + sText: string, optional
     *
     * Output data:
     * + iLastId: int
     * + error_code: int.
     * + error_message: string.
     * + result: int.
     *
     * @see Mobile - API SE/Api V1.0
     * @see comment/add
     *
     * @global string $token
     * @param array $aData
     * @return array
     */
    public function add($aData)
    {
        extract($aData);
        
        $sItemType = @$sItemType;
        $iItemId  = intval($iItemId);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sText = trim(@$sText);
        $oItem = $this -> getWorkingItem($sItemType, $iItemId);
		
		 if(!$oItem){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing comment target"),
            );
        }
		
        if (!$viewer -> getIdentity() )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to comment on this item!")
            );
        }
		
		if($sItemType != 'activity_action' && !Engine_Api::_() -> authorization() -> isAllowed($oItem, null, 'comment'))
		{
			return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to comment on this item!")
            );
		}
        
        if (!method_exists($oItem, 'comments')){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("This object does not support to comment!"),
            );
        }

        if (!$sText){
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Add some text to your comment!"),
            );
        }
        
        $comments = $oItem->comments();
        
        try
        {
            $comment = $comments -> addComment($viewer, $sText);
            $iLastId = $comment -> comment_id;
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            
            if($sItemType == 'activity_action')
            {
                $oItemOwner = Engine_Api::_() -> getItemByGuid($oItem -> subject_type . "_" . $oItem -> subject_id);
            }
            else
            {
                $oItemOwner = $oItem -> getOwner('user');
            }
            

            // Activity
            $action = $activityApi -> addActivity($viewer, $oItem, 'comment_' . $oItem -> getType(), '', array(
                'owner' => $oItemOwner -> getGuid(),
                'body' => $sText
            ));

            // Add notification for owner (if user and not viewer)
            if ($oItemOwner -> getType() == 'user' && $oItemOwner -> getIdentity() != $viewer -> getIdentity())
            {
                $notifyApi -> addNotification($oItemOwner, $viewer, $oItem, 'commented', array('label' => $oItem -> getShortType()));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            $notifyUsers = (method_exists($oItem, 'comments')) ? ($oItem -> comments() -> getAllCommentsUsers()) : (Engine_Api::_() -> getDbtable('comments', 'core') -> getAllCommentsUsers($oItem));

            foreach ($notifyUsers as $notifyUser)
            {
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $oItemOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser -> getIdentity();

                $notifyApi -> addNotification($notifyUser, $viewer, $oItem, 'commented_commented', array('label' => $oItem -> getShortType()));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            $allLikeUsers = (method_exists($oItem, 'likes')) ? ($oItem -> likes() -> getAllLikesUsers()) : (Engine_Api::_() -> getDbtable('likes', 'core') -> getAllLikesUsers($oItem));

            foreach ($allLikeUsers as $notifyUser)
            {
                // Skip viewer and owner
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $oItemOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser -> getIdentity(), $commentedUserNotifications))
                    continue;

                $notifyApi -> addNotification($notifyUser, $viewer, $oItem, 'liked_commented', array('label' => $oItem -> getShortType()));
            }

            // Increment comment count
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.comments');
            
            return Ynmobile_AppMeta::_export_one($comment, array('listing'));
        }

        catch( Exception $e )
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
                'result' => 0,
                'error_debug'=> $e->getMessage()
            );
        }
    }

    /**
     * Input data:
     * + sItemType: string, required.
     * + iItemId: int, required.
     * + iLastCommentIdViewed: int, optional.
     * + iLimit: int, optional.
     *
     * Output data:
     * + iLikeId: int
     * + iUserId: int
     * + sFullName: string
     * + sImage: string
     *
     * @see Mobile - API phpFox/Api V1.0
     * @see comment/listallcomments
     *
     * @param array $aData
     * @return array
     */
    public function listallcomments($aData)
    {
        extract($aData);
        
        $amountOfComment  = $amountOfComment?intval($amountOfComment):3; 
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        $iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
        
        if (!$sItemType || !$iItemId)
        {
            return array(
                'error_code' => 1,
                'error_elements' => 'sItemType or iItemId',
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        
        $lastCommentIdViewed = isset($aData['iLastCommentIdViewed']) ? (int)$aData['iLastCommentIdViewed'] : 0;
        $amountOfComment = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 20;
        
        if (empty($sItemType) || $iItemId < 1)
        {
            return array(
                'error_code' => 1,
                'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter(s) is not valid!")
            );
        }
        
        $oItem = Engine_Api::_() -> getItem($sItemType, $iItemId);
        
        if(!$oItem) return array();
        
        $proxy = $oItem->comments();
        $table = $proxy->getCommentTable();
        $select = $proxy->getCommentSelect();
        $select -> order ("comment_id DESC");
        
        if ($lastCommentIdViewed)
        {
            $select -> where('comment_id < ?', $lastCommentIdViewed);
        }

        $select -> limit($amountOfComment);
        
        if(empty($fields)) $fields =  'listing';
        
        $fields = explode(',', $fields);
        
        return Ynmobile_AppMeta::_export_all($select, $fields);
    }
    
}
