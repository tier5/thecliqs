<?php

class Ynmobile_Helper_Mp3MusicAlbum extends Ynmobile_Helper_MusicPlaylist{
    
    function field_totalSong(){
        $table = $this->getWorkingTable('albumSongs','music');
        
        $select = $table->select()
                ->where('album_id=?', $this->entry->getIdentity())
                ;

        $this->data['iTotalSong']  = (int)Zend_Paginator::factory($select)->getTotalItemCount();
    }
    
    function field_songs(){
        
        $table = $this->getWorkingTable('albumSongs','mp3music');
        
        $select = $table->select()
                ->where('album_id=?', $this->entry->getIdentity())
                ->order('order')
                ;
        
        if(empty($this->data['iTotalSong'])){
            $this->data['iTotalSong']  = (int)Zend_Paginator::factory($select)->getTotalItemCount();    
        }
        
        $fields = array('simple_array');

        $this->data['aSongs'] =  Ynmobile_AppMeta::_exports_by_page($select,1,100,$fields);
    }
}
