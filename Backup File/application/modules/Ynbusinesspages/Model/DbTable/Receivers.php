<?php
class Ynbusinesspages_Model_DbTable_Receivers extends Engine_Db_Table
{
  protected $_name = 'ynbusinesspages_receivers';
  
  public function getReceivers($business_id)
  {
  	$select = $this -> select() -> where('business_id = ?', $business_id);
	return $this -> fetchAll($select);
  }
}