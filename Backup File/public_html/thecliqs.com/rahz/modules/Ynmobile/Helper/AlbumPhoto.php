<?php

class Ynmobile_Helper_AlbumPhoto extends Ynmobile_Helper_Photo{
    
    function field_parent(){
        $this->data['iParentId'] = $this->entry->album_id;
        $this->data['sParentType'] = 'album';
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_parent();
        $this->field_title();
        $this->field_desc();
        $this->field_stats();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_user();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
