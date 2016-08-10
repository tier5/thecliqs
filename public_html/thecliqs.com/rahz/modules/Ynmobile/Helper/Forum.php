<?php

class Ynmobile_Helper_Forum extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('forum','ynmobile');
    }
    
    function field_id(){
        $this->data['iForumId'] =  $this->entry->getIdentity();
    }
    
    function field_totalTopic(){
        $this->data['iTotalTopic']=  $this->entry->topic_count;
    }    
    
    function field_totalPost(){
        $this->data['iTotalPost'] = $this->entry->post_count;
    }
}
