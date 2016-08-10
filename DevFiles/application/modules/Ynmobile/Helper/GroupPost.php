<?php

class Ynmobile_Helper_ForumPost extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('forum','ynmobile');
    }
    
    function field_id(){
        $this->data['iPostId'] =  $this->entry->getIdentity();
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_title();
        $this->field_user();
        $this->field_content();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
