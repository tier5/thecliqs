<?php
class Ynresume_Model_DbTable_Publications extends Engine_Db_Table 
{
    protected $_rowClass = 'Ynresume_Model_Publication';
    
    public function getPublicationsByResumeId($resume_id) 
    {
        $select = $this->select()->where('resume_id = ?', $resume_id);
        return $this->fetchAll($select);
    }
}
