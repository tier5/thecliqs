<?php

class Ynmobile_Helper_Notification extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('notification','ynmobile');
    }
    
    function field_id(){
        $this->data['iNotificationId']= $this->entry->notification_id;
    }
    
    function field_subject(){
        $subject = $this->entry -> getSubject();
        
        $this->data['oSubject'] =  array(
            'id'=>$subject->getIdentity(),
            'type'=>$subject->getType(),
            'title'=>$subject->getTitle(),
            'href'=>$subject->getHref(),
            'img'=>$this->finalizeUrl($subject->getPhotoUrl('thumb.icon')),
        );
    }
    
    
    function field_info(){
        $update = $this->entry;
        
        
        $this->data['sType'] = $update->type;
        $this->data['sMessage'] = $update -> getContent();
        $this->data['iItemId']= $update->object_id;
        $this->data['sItemType'] =  $update->object_type;
        $this->data['bIsSeen']  = $update->read?1:0;
        
        
        
        if ($update -> object_type == 'activity_action'){
                
            $this->data['aParams'] = $update->params || array();
            
        }elseif ($update -> object_type == 'core_comment'){
            
            $comment = $update->getObject();
            
            $this->data['sResourceType'] = $comment->resource_type;
            $this->data['iResourceId'] = $comment->resource_id;
        
            //WHEN THE RESOURCE IS A PHOTO. WE NEED THE ALBUM ID
            if ( $comment->resource_type == 'album_photo' 
                || $comment->resource_type == 'advalbum_photo' )
            {
                $resource = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
                $parent = $resource->getParent();
                if ($parent)
                {
                    $this->data['sParentType'] = $parent->getType();
                    $this->data['iParentId'] = $parent->getIdentity();
                }
            }
        }
        else if ($update -> object_type == 'activity_comment')
        {
            $comment = $update->getObject();
            $this->data['sResourceType'] = 'activity_action';
            $this->data['iResourceId'] = $comment->resource_id;
        }
    }

    public function field_parent()
    {
        try{
            
            $object  =  $this->entry->getObject();
            
            if($object && $object->getParent()){
                return $this->data['oParent'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();    
            }else{
                $this->data['oParent'] = array();
            }
            
                
        }catch(Exception $ex){
            
        }
        
    }
    
    function field_listing(){
        /**
         * 
         */
        $this->field_id();
        $this->field_user();
        $this->field_timestamp();
        $this->field_subject();
        $this->field_info();
        $this->field_object();
        $this->field_parent();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
