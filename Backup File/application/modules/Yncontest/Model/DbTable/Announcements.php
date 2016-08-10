<?php
class Yncontest_Model_DbTable_Announcements extends Engine_Db_Table
{
  protected $_name = 'yncontest_announcements';
  protected $_rowClass = 'Yncontest_Model_Announcement';
  
	public function getAnnouncementByContestId($contestId){
		$select = $this->select()->where('contest_id = ?', $contestId)->limit(1);
		$result = $this->fetchRow($select);
		return $result;		
	}
}