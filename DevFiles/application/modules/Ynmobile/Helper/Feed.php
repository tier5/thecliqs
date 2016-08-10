<?php

class Ynmobile_Helper_Feed extends Ynmobile_Helper_Base{
    
    protected $_fields = array('id','title','type');
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('feed','ynmobile');
    }
    
    function field_id(){
        return $this->data['iActionId'] =  $this->entry->getIdentity();
    }
    
    function field_content(){
        return $this->data['sContent'] = $this->entry->body;
    }
    
    function field_params(){
        try{
            $this->data['sParams'] = $this->entry->params;
            
            $params = $this->entry->params;;
            if($this->entry->params == "[]"){
                $params = array();
            }
            $this->data['oParams'] =  array_merge(array('count'=>0), $params);
            
        }catch(Exception $ex){
            
        }
        
    }
    
    function field_parent(){
        try{
            $object  =  $this->entry->getObject()->getParent();
        
        if(!$object){
            return $this->data['oParent'] = array();
        }
        
        $this->data['oParent'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();    
        }catch(Exception $e){
            
        }
        
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_stats();
        $this->field_type();
        $this->field_params();
        $this->field_content();
        $this->field_attachments();
        $this->field_item();
        $this->field_user();
        $this->field_subject();
        $this->field_parent();
        $this->field_object();
        $this->field_owner();
        $this->field_canDelete();
		$this->field_ext();
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_likes();
    }
    
    
    function field_canShare(){
        //Sharable
        $bCanShare = false;
        $action    = $this->entry;
        $oViewer =  $this->getViewer();
        
        if ($action->getTypeInfo()->shareable && $oViewer->getIdentity()){
            if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($firstAttachment = $action->getFirstAttachment()))
                $bCanShare = true;
            elseif($action->getTypeInfo()->shareable == 2)
                $bCanShare = true;
            elseif($action->getTypeInfo()->shareable == 3)
                $bCanShare = true;
            elseif($action->getTypeInfo()->shareable == 4)
                $bCanShare = true;
        }
        
        $this->data['bCanShare'] =  $bCanShare?1:0;
    }
    
    function field_timestamp(){
        if(isset($this->entry->date)){
            $this->data['iTimeStamp'] =  strtotime($this->entry -> date);    
        }
        
    }
    
    function field_canComment(){
        $action =  $this->entry;
        $object =  $action->getObject();
        
        $canComment = ($action -> getTypeInfo() -> commentable && Engine_Api::_() -> authorization() -> isAllowed($object, null, 'comment'));
        
        $this->data['bCanComment'] =  $canComment?1:0;
        $this->data['bCanLike'] =  $canComment?1:0;
    }
    
    function field_stats(){
        $this->field_actionType();
        $this->field_canShare();
        $this->field_canComment();
        $this->field_totalComment();
        $this->field_totalLike();
        $this->field_liked();
        $this->field_timestamp();
    }   
    
    function field_actionType(){
        $this->data['sActionType'] =  $this->entry->type;
    } 
    function field_attachments(){
        $this->data['aAttachments'] = array();
        $action =  $this->entry;
        
        if ($action -> getTypeInfo() -> attachable && $action -> attachment_count > 0)
        {
            
            foreach ($action->getAttachments() as $index=>$attachment)
            {
                $item  = $attachment->item;
                
                $att  =  $this->getWorkingItem($item->getType(), $item->getIdentity());
                
                if(!$att){
                    continue;
                }
                
				if($index ==0 && Engine_Api::_()->hasModuleBootstrap('ynfeed')){
					$this->data['iTotalShare'] = count(Engine_Api::_ ()->ynfeed ()->getShareds($action -> getIdentity(), $att->getType(), $att->getIdentity()));
				}
				
                $this->data['aAttachments'][] =  Ynmobile_AppMeta::_export_one($item, array('as_attachment'));
            }
        }
    }
    
    function field_ext(){
    	
		if(!Engine_Api::_()->hasModuleBootstrap('ynfeed')) return;
    	
    	$subject =  null;
		$enableComposer = false;
		$viewer =  $this->getViewer();
		$iActionId =  $this->entry->action_id;
		$action = $this->entry;
		$actionSubject  = $this->entry->getSubject();
		
		
		
		
		$saveFeedTable =  Engine_Api::_() -> getDbTable('saveFeeds', 'ynfeed');
		$optionFeedTable  = $optionFeedTable =  Engine_Api::_() -> getDbTable('optionFeeds', 'ynfeed');
		
		$locked = $optionFeedTable -> getOptionFeed($actionSubject, $iActionId, 'lock')?1:0;
		$saved = $saveFeedTable -> getSaveFeed($viewer, $iActionId)?1:0;
		$enabled_comment = $optionFeedTable -> getOptionFeed($actionSubject, $iActionId, 'comment')?0:1;
		
		$allow_delete = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity_userdelete');
		$allowSaveFeed = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeed_savefeed', true);
		$activity_moderate = Engine_Api::_() -> getDbtable('permissions', 'authorization') -> getAllowed('user', $viewer -> level_id, 'activity');
		
		
		if(Engine_Api::_()->core()->hasSubject()){
			$subject = Engine_Api::_() -> core() -> getSubject();
		}
		
		if(! $subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))){
			if(Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'user', 'status')){
				$enableComposer =true;	
			}
		}else if($subject){
			if(Engine_Api::_() -> authorization() -> isAllowed($subject, $viewer, 'comment')){
				$enableComposer = true;
			}
		}
		
		$isOwner = $viewer->isSelf($this->entry->getSubject())?1:0;
		
		$this->data['bCanDelete']=  ($activity_moderate ||
			($viewer->getIdentity() == $activity_group) ||
			($allow_delete && (
			 	'user'== $action->subject_type && $action->subject_id == $viewer->getIdentity()) || 
			 	'user' == $action->object_type && $action->object_id == $viewer->getIdentity() 
			))?1:0;
			
		
		$this->data['bIsOwner'] = $isOwner?1:0;
		
		$this->data['bCanComment'] = $locked ? $isOwner : ($enabled_comment?$this->data['bCanComment']:$isOwner);
		$this->data['bCanLike'] = $locked ? $isOwner : ($enabled_comment? $this->data['bCanComment']:$isOwner);
		$this->data['bCanShare'] =  $locked ? ($isOwner && $this->data['bCanShare']?1:0) : ($this->data['bCanShare']);
		
		$this->data['bCanEnableNotification'] = 1;
		$this->data['bCanEnableComment'] = $isOwner?1:0;
		$this->data['bCanEnableLock'] = $isOwner?1:0;
		$this->data['bCanSave']= (! $subject && $allowSaveFeed)?1:0;
		
		
		if($isOwner){
			$this->data['bEnabledNotification'] =  ($optionFeedTable -> getOptionFeed($viewer, $iActionId, 'notification'))?0:1;	
		}else{
			$this->data['bEnabledNotification'] =  ($optionFeedTable -> getOptionFeed($viewer, $iActionId, 'notification'))?1:0;
		}
		
		$this->data['bEnabledComment'] = $enabled_comment;
		$this->data['bEnabledLock'] = $locked;
		$this->data['bIsSaved'] = $saved;
		
		
		
		$this->data['bCanEdit']=  ($enableComposer && in_array($action -> type, array('status', 'post', 'post_self')))? 1:0; 
		$this->data['bCanHide']= $this->data['bCanHideAll'] =  $viewer->isSelf($actionSubject)?0:1; // 1: show "i don't want to see this"
		
		$tagFriendTable  =  Engine_Api::_()->getDbtable('tagfriends','ynfeed');
		
		$aTagFriendIds = $tagFriendTable->getAdapter()->fetchCol( $tagFriendTable->select()
                    ->from($tagFriendTable, 'friend_id')
                    ->where('action_id = ?', $iActionId)
                    );
					
		$userTable = Engine_Api::_()->getItemTable('user');
				
		if($aTagFriendIds){
			$this->data['aTagFriends'] =  Ynmobile_AppMeta::_export_all($userTable->select()->where('user_id  IN (?)', $aTagFriendIds), array('simple_array'));
		}else{
			$this->data['aTagFriends'] = array();
		}
		
		$map =  Engine_Api::_()->getDbtable('maps','ynfeed')->getMapByAction($iActionId);
		if($map){
			$this->data['aMap'] =  array(
				'id'=>$map->getIdentity(),
				'type'=>$map->getType(),
				'title'=>$map->getTitle(),
				'lat'=>$map->latitude,
				'lon'=>$map->longitude,
				'iActionId'=>$map->action_id,
				'user_id'=>$map->user_id,
			);
		}
		
		if(!isset($this->data['iTotalShare'])){
			$this->data['iTotalShare'] = count(Engine_Api::_ ()->ynfeed () -> getShareds($iActionId, "", 0));
		}
		
		
    }
    
    function field_item(){
        $object  =  $this->entry->getObject();
        
        if(!$object){
            return $this->data['oItem'] = array();
        }
        
        return $this->data['oItem'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();
    }
    
    
}
