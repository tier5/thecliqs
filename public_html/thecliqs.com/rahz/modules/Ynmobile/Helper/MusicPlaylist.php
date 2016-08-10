<?php

class Ynmobile_Helper_MusicPlaylist extends Ynmobile_Helper_Base{
    
    function field_id(){
        $this->data['iAlbumId'] =  $this->entry->getIdentity();
    }
    
    function field_totalPlay(){
        $this->data['iTotalPlay'] = intval($this->entry->play_count);
    }
    
    function field_totalSong(){
        $table = $this->getWorkingTable('playlistSongs','music');
        
        $select = $table->select()
                ->where('playlist_id=?', $this->entry->getIdentity())
                ;

        $this->data['iTotalSong']  = intval(Zend_Paginator::factory($select)->getTotalItemCount());
    }
    
    function field_songs(){
        $table = Engine_Api::_()->getDbTable('playlistSongs','music');
        
        $select = $table->select()
                ->where('playlist_id=?', $this->entry->getIdentity())
                ->order('order')
                ;
        
        if(empty($this->data['iTotalSong'])){
            $this->data['iTotalSong']  = (int)Zend_Paginator::factory($select)->getTotalItemCount();    
        }
        
        $fields = array('simple_array');

        $this->data['aSongs'] =  Ynmobile_AppMeta::_exports_by_page($select,1,100,$fields);
    }
        
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('album','ynmobile');
    }
    
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        
        $this->field_title();
        $this->field_desc();
        $this->field_stats();
        $this->field_totalSong();
        $this->field_totalPlay();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_user();
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_likes();
        $this->field_songs();
        
    }
    
    
}
