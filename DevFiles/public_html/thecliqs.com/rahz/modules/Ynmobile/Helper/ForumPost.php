<?php

class Ynmobile_Helper_ForumPost extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('forum','ynmobile');
    }
    
    function field_id(){
        $this->data['iPostId'] =  $this->entry->getIdentity();
    }
    
}
