<?php
class Ynlistings_Model_DbTable_Photos extends Engine_Db_Table
{
  protected $_name = 'ynlistings_photos';
  protected $_rowClass = 'Ynlistings_Model_Photo';
  
  public function getPhotoCount()
  {
  	return count($this->fetchAll($this->select()));
  }
}