<?php
class Ynresume_Model_DbTable_Awards extends Engine_Db_Table {
    protected $_rowClass = 'Ynresume_Model_Award';
    
    public function getAwardsByResumeId($resume_id) {
        $select = $this->select()->where('resume_id = ?', $resume_id)
        ->order('date_year DESC')
        ->order('date_month DESC');
        return $this->fetchAll($select);
    }
}
