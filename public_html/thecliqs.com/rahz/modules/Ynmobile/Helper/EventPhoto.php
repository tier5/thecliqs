<?php

class Ynmobile_Helper_EventPhoto extends Ynmobile_Helper_Photo{
    function field_parent(){
        $this->data['iParentId'] = $this->entry->event_id;
        $this->data['sParentType'] = 'event';
    }
}
