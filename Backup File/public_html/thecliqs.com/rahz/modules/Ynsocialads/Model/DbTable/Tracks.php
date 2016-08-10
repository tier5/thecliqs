<?php
 
class Ynsocialads_Model_DbTable_Tracks extends Engine_Db_Table {
  protected $_rowClass = 'Ynsocialads_Model_Track';
  
  public function checkExistTrack($date, $ad_id)
  {
  	$select = $this->select()->where('date = ?', $date) -> where('ad_id = ?', $ad_id) -> limit(1);
	if($track  = $this->fetchRow($select))
	{
		return $track;
	}
	else {
		return false;
	}
  }
}
