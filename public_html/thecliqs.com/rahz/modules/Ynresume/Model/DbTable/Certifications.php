<?php
class Ynresume_Model_DbTable_Certifications extends Engine_Db_Table
{
	protected $_rowClass = 'Ynresume_Model_Certification';
    
    public function getCertificationsByResumeId($resume_id) 
    {
        $select = $this->select()->where('resume_id = ?', $resume_id);
        return $this->fetchAll($select);
    }
}
