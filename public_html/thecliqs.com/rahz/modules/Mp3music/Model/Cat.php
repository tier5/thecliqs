<?php
class Mp3music_Model_Cat extends Core_Model_Item_Abstract
{
   public function getCats($parent_cat)
  {
    $table  = Engine_Api::_()->getDbtable('cats', 'mp3music');
    $select = $table->select()->where('parent_cat = ?', $parent_cat)
                    ->order('title ASC');
    return $table->fetchAll($select);
  }  
   public function setTitle($newTitle)
  {
    $this->title = $newTitle;
    $this->save();
    return $this;
  }
}
