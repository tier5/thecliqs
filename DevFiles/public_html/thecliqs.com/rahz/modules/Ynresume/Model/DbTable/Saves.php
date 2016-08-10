<?php

class Ynresume_Model_DbTable_Saves extends Engine_Db_Table {
	public function getSaveRow($user_id, $resume_id) {
		$select = $this -> select() 
						-> where('user_id = ?', $user_id)
						-> where('resume_id = ?', $resume_id)
						-> limit(1);
		return $this -> fetchRow($select);
	}
    
    public function getSavedResumes($uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
    }
}
