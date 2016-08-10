<?php

class Ynmobile_Helper_Checkin extends Ynmobile_Helper_Base{
    
    function field_id(){
    }
    
    function field_as_attachment(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_latlon();
    }
    
    function field_latlon(){
        $this->data['fLatitude'] =  $this->entry->latitude;
        $this->data['fLongitude'] = $this->entry->longitude;
    }
}
