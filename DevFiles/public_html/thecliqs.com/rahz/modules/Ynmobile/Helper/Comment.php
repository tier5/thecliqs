<?php

class Ynmobile_Helper_Comment extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('comment','ynmobile');
    }
    
    function field_id(){
        $this->data['iCommentId'] =  $this->entry->getIdentity();
    }
    
    function field_content(){
        $this->data['sContent'] = $this->entry->body;
    }
    
    // $fields = explode(',','id,type,content,user,stats');
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_content();
        $this->field_canLike();
        $this->field_canDelete();
        $this->field_timestamp();   
        $this->field_liked();
        $this->field_totalLike();
        $this->field_user();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
