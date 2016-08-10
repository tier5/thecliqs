<?php

class Ynmobile_Helper_Album extends Ynmobile_Helper_Base{
    
    
   function getYnmobileApi(){
       return Engine_Api::_()->getApi('photo','ynmobile');
   }
   
   function field_id(){
       $this->data['iAlbumId'] =  $this->entry->getIdentity();
   }
   
   function field_totalPhoto(){
       $this->data['iTotalPhoto'] =  intval($this->entry->count());
   }
   
   
   function field_listing(){
       $this->field_id();
       $this->field_type();
       $this->field_title();
       $this->field_totalPhoto();
       $this->field_imgNormal();
       $this->field_imgFull();
       $this->field_totalLike();
       $this->field_user();
   }
   
   function field_detail(){
       $this->field_listing();
       $this->field_desc();
       $this->field_likes();
       $this->field_canLike();
       $this->field_canComment();
       $this->field_canEdit();
       $this->field_canDelete();
   }
}
