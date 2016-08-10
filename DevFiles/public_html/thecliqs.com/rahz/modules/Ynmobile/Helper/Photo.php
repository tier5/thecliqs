<?php

class Ynmobile_Helper_Photo extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
       return Engine_Api::_()->getApi('photo','ynmobile');
   }
   
    
    function field_id(){
        $this->data['iPhotoId']  = $this->entry->getIdentity();
    }
    
    function field_albumId(){
        if(isset($this->entry->album_id)){
            $this->data['iAlbumId']  = $this->entry->album_id;    
        }else{
            $this->data['iAlbumId'] = 0;
        }
    }
    
    function field_parent(){
        
    }
    
    function field_as_attachment(){
        parent::field_as_attachment();
        $this->field_parent();
    }
}
