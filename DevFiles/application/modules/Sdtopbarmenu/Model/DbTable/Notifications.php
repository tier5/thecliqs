<?php
class Sdtopbarmenu_Model_DbTable_Notifications extends Engine_Db_Table
{
   public function notificationsOnlys() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$tableNotifications = Engine_Api::_()->getDbTable('notifications', 'activity');
		$select = $tableNotifications->select()
						->where("`user_id` = ?", $viewer->getIdentity())
						->where("`type` <> 'friend_request'")
						->where("`read` = ?", 0)
						->where("`type` <> 'message_new'")
						->order('read ASC')
						->order('notification_id DESC');
		return $tableNotifications->fetchAll($select);				
   }
   public function messageOnlys() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$tableNotifications = Engine_Api::_()->getDbTable('notifications', 'activity');
		$select = $tableNotifications->select()
						->where("`user_id` = ?", $viewer->getIdentity())
						->where("`type` <> 'friend_request'")
						->where("`read` = ?", 0)
						->where("`type` = 'message_new'")
						->order('read ASC')
						->order('notification_id DESC');
		return $tableNotifications->fetchAll($select);				
	}
    public function friendrequestOnlys() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$tableNotifications = Engine_Api::_()->getDbTable('notifications', 'activity');
		$select = $tableNotifications->select()
						->where("`user_id` = ?", $viewer->getIdentity())
						->where("`type` = 'friend_request'")
						->where("`read` = ?", 0)
						->where("`type` <> 'message_new'")
						->order('read ASC')
						->order('notification_id DESC');
		return $tableNotifications->fetchAll($select);				
	}
}
?>