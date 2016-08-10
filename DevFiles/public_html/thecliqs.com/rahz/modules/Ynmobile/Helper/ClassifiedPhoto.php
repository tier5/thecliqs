<?php

class Ynmobile_Helper_ClassifiedPhoto extends Ynmobile_Helper_Photo{
    function field_parent(){
        $this->data['iParentId'] = $this->entry->classified_id;
        $this->data['sParentType'] = 'classified';
    }
}
