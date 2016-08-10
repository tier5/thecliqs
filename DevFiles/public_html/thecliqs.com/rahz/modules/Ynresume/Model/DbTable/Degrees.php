<?php

class Ynresume_Model_DbTable_Degrees extends Engine_Db_Table {
	public function getAllDegress() {
		return $this -> fetchAll($this -> select());
	}
	
    public function getDegreeById($id = 0) {
        $select = $this->select()->where('degree_id = ?', $id);
        return $this->fetchRow($select);
    }
}
