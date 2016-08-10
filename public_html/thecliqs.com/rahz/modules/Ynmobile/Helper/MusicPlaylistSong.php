<?php

class Ynmobile_Helper_MusicPlaylistSong extends Ynmobile_Helper_Base{
    
    function field_simple_array(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_totalPlay();
        $this->field_path();
    }
    
    function field_id(){
        $this->data['iSongId'] = $this->entry->getIdentity();
    }
    
    function field_totalPlay(){
        if(isset($this->entry->play_count)){
            $this->data['iTotalPlay'] = intval($this->entry->play_count);    
        }
    }
    
    function field_path(){
        $this->data['sSongPath'] =  $this->finalizeUrl($this->entry->getFilePath());
    }
    
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_totalPlay();
        $this->field_path();
    }
    
    function field_detail(){
        $this->field_listing();
    }
}
