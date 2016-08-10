<?php

class Ynmobile_Helper_Link extends Ynmobile_Helper_Base{
    
    function field_as_attachment(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_desc();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_original();
        $this->field_href();
    }
    
    function field_original(){
        $this->data['sOriginalHostName'] = parse_url($this->entry->uri,PHP_URL_HOST);
    }
}

