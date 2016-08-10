<?php
class Ynresume_Model_DbTable_Courses extends Engine_Db_Table 
{
    protected $_rowClass = 'Ynresume_Model_Course';
    
    public function getCoursesByResumeId($resume_id) 
    {
        $select = $this->select()->where('resume_id = ?', $resume_id);
        return $this->fetchAll($select);
    }
    
	public function getCoursesByEducation($education) 
    {
        $select = $this->select()
        ->where('associated_id = ?', $education -> getIdentity())
        ->where('associated_type = ?', 'ynresume_education')
        ;
        return $this->fetchAll($select);
    }
    
	public function getCoursesByExperience($experience) 
    {
        $select = $this->select()
        ->where('associated_id = ?', $experience -> getIdentity())
        ->where('associated_type = ?', 'ynresume_experience')
        ;
        return $this->fetchAll($select);
    }

    public function getOtherCourses($resume)
    {
        if (is_null($resume))
        {
            return array();
        }
        $select = $this->select()
            ->where('resume_id  = ?', $resume -> getIdentity())
            ->where('associated_id = 0')
        ;
        return $this->fetchAll($select);
    }
}
