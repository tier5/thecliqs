<?php
class Ynresume_Model_DbTable_Projects extends Engine_Db_Table 
{
    protected $_rowClass = 'Ynresume_Model_Project';
    
    public function getProjectsByResumeId($resume_id) 
    {
        $select = $this->select()->where('resume_id = ?', $resume_id);
        return $this->fetchAll($select);
    }
}
