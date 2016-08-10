<?php
class Mp3music_Model_SingerType extends Core_Model_Item_Abstract
{
   public function getSingerTypes()
  {
    $table  = Engine_Api::_()->getDbtable('singerTypes', 'mp3music');
    $select = $table->select()
                    ->order('singertype_id ASC');
    return $table->fetchAll($select);
  }
   public function getSingers()
  {
    $table  = Engine_Api::_()->getDbtable('singers', 'mp3music');
    $select = $table->select()
                    ->where('singer_type = ?', $this->getIdentity())
                   ;
    return $table->fetchAll($select);
  } 
   public function setTitle($newTitle)
  {
    $this->title = $newTitle;
    $this->save();
    return $this;
  } 
}
