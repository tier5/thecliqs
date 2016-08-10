<?php
class Ynresume_Model_DbTable_Educations extends Engine_Db_Table {
    protected $_rowClass = 'Ynresume_Model_Education';
    
    public function getEducationsByResumeId($resume_id, $limit = false) {
        $select = $this->select()->where('resume_id = ?', $resume_id)->order('attend_from DESC')->order('attend_to DESC');
        
        if ($limit) {
            $select->limit($limit);
        }
        
        return $this->fetchAll($select);
    }
}
