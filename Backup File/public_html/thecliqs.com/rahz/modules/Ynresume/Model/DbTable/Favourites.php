<?php

class Ynresume_Model_DbTable_Favourites extends Engine_Db_Table
{
	public function getFavouriteResume($resume_id, $uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid)
                ->where('resume_id = ?', $resume_id)
				->limit(1);
        return $this->fetchRow($select);
    }
	
	public function getFavouriteResumes($uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
    }

    public function getUsersFavourite($resume_id) {
        $select = $this->select();
        $select->where('resume_id= ?', $resume_id);
        return $this->fetchAll($select);
    }
}
