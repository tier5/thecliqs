<?php
class Ynlistings_Model_DbTable_Posts extends Engine_Db_Table
{
  protected $_name = 'ynlistings_posts';
  protected $_rowClass = 'Ynlistings_Model_Post';
  
  public function getPostsCount()
  {
  	return count($this->fetchAll($this->select()));
  }  
}