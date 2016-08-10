<?php

class Ynmobile_Helper_GroupPhoto extends Ynmobile_Helper_Photo{
    function field_parent(){
        $this->data['iParentId'] = $this->entry->group_id;
        $this->data['sParentType'] = 'group';
    }
}
