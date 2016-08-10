<?php

class Ynmobile_Helper_ForumTopic extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('forum','ynmobile');
    }
    
    function field_id(){
        $this->data['iTopicId'] =  $this->entry->getIdentity();
    }
}
