<?php

class Ynmobile_Helper_Network extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('network','ynmobile');
    }
    
    function field_id(){
        $this->data['iNetworkId'] =  $this->entry->getIdentity();
    }
    
    function field_totalMember(){
        $this->data['iMemberCount'] = $this->entry->getMemberCount();
    }
    
    function field_isMember(){
        $this->data['bIsMember'] = $this->entry->membership()->isMember($this->getViewer())?1:0;
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_isMember();
        $this->field_totalMember();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
