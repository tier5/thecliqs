<?php
class Mp3music_Model_Rating extends Core_Model_Item_Abstract
{
  public function checkUservote($song_id,$user_id)
  {
    $table  = Engine_Api::_()->getDbtable('ratings', 'mp3music');
    $select = $table->select()
                    ->where('item_id = ?',$song_id)
                    ->where('user_id = ?',$user_id);
    return $table->fetchAll($select);
  }  
  public function getVotes($song_id)
  {
    $table  = Engine_Api::_()->getDbtable('ratings', 'mp3music');
    $select = $table->select()
                    ->where('item_id = ?',$song_id);
    return $table->fetchAll($select);
  }  
}