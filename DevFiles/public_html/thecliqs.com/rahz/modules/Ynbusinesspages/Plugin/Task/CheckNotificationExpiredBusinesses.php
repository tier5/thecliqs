<?php
class Ynbusinesspages_Plugin_Task_CheckNotificationExpiredBusinesses extends Core_Plugin_Task_Abstract {
	public function execute() {
        $now = date("Y-m-d H:i:s");
        $notiTbl = Engine_Api::_()->getDbTable('renewals', 'ynbusinesspages');
        $rows = $notiTbl->fetchAll($notiTbl->select()->where('notified = ?', 0));
        $notices = array();
        foreach ($rows as $row) {
             $notices[$row->business_id] = '1 '.$row->time;
        }
        $ids = array_keys($notices);
        if (count($ids)) {
            $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
            $select = $table->select()
                ->where('business_id IN (?)', $ids)
                ->where('status = ?', 'published');
            $businesses = $table->fetchAll($select);
            $notifiedIds = array();
            foreach ($businesses as $business) {
                $expiredDate = date_create($business->expiration_date);
                $noticeDate = date_sub(date_create($now), date_interval_create_from_date_string($notices[$business->getIdentity()]));
                if ($expiredDate <= $noticeDate) {
                    array_push($notifiedIds, $business->getIdentity());
                    $owner = $business -> getOwner();
                    $notifyApi -> addNotification($owner, $owner, $business, 'ynbusinesspages_business_noticeexpired', array('time' => $notices[$business->getIdentity()]));
                }
            }
            
            if (count($notifiedIds)) {
                $string = implode(',', $notifiedIds);
                $notiTbl->update('notified = 1', "business_id IN ($string)");
            }
        }
    }
}