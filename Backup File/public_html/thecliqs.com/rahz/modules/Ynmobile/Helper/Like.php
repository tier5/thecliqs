<?php

class Ynmobile_Helper_Like extends Ynmobile_Helper_Base{
    
    function field_id(){
        $this->data['iLikeId']  = $this->entry->getIdentity();
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_user();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
