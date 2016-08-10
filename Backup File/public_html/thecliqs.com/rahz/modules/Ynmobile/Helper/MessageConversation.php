<?php

class Ynmobile_Helper_MessageConversation extends Ynmobile_Helper_Base{
    
    function field_id(){
        $this->data['iConversationId'] = $this->entry->getIdentity();
    }
    
    function field_body(){
        $this->data['sBody'] =  nl2br(html_entity_decode($this->entry -> body));
    }
    
    
    function field_listing(){
        $this->field_id();
        $this->field_title();
        $this->field_type();
        
        
        $conversation  = $this->entry;
        
        $recipient = $conversation -> getRecipientInfo($viewer);
        
        $this->data['bIsRead'] = ($recipient->inbox_read == '1') ? 1:0;
        $this->data['sResourceType'] = $conversation->resource_type;
    }
}
