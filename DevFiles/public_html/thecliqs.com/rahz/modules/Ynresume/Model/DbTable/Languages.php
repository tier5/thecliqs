<?php
class Ynresume_Model_DbTable_Languages extends Engine_Db_Table 
{
    protected $_rowClass = 'Ynresume_Model_Language';
    
    public function getLanguagesByResumeId($resume_id) 
    {
        $select = $this->select()->where('resume_id = ?', $resume_id);
        return $this->fetchAll($select);
    }
}
