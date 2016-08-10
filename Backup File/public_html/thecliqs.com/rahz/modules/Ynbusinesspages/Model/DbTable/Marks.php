<?php
class Ynbusinesspages_Model_DbTable_Marks extends Engine_Db_Table
{
  	protected $_name = 'ynbusinesspages_announcement_marks';	
	
	public function markAnnouncement($params = array()){
		$db = $this->getAdapter();
	    $db->beginTransaction();
	
	    try {
	      // Create
	      $row = $this->createRow();
	      $row->setFromArray($params);
	      $row->save();
	      // Commit
	      $db->commit();
		}
		catch(exception $e)
		{
			return false;
		}
		return true;
	}
}