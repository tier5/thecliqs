<?php

class Ynbusinesspages_Model_DbTable_Favourites extends Engine_Db_Table
{
	public function getFavouriteBusiness($business_id, $uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid)
                ->where('business_id = ?', $business_id);
        return $this->fetchRow($select);
    }
	
	public function getFavouriteBusinesses($uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
    }

    public function getUsersFavourite($business_id) {
        $select = $this->select();
        $select->where('business_id= ?', $business_id);
        return $this->fetchAll($select);
    }
}
