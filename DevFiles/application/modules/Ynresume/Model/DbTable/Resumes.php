<?php
class Ynresume_Model_DbTable_Resumes extends Engine_Db_Table {
    protected $_rowClass = 'Ynresume_Model_Resume';
    
	public function getResume($uid)
	{
		$select = $this -> select() -> where('user_id = ?', $uid) -> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getResumesByIndustry($industry_id) {
		$select = $this -> select() -> where('industry_id = ?', $industry_id);
		return $this -> fetchAll($select);
	}
	
	public function getResumesByType($type, $bool = true)
	{
		$select = $this -> select() -> where("$type = ?", $bool);
		return $this -> fetchAll($select);
	}
	
	public function getAllChildrenIdeasByIndustry($node) {
		$return_arr = array();
		$cur_arr = array();
		$list_industries = array();
		Engine_Api::_() -> getItemTable('ynresume_industry') -> appendChildToTree($node, $list_industries);
		foreach ($list_industries as $industry) {
			$tableResume = Engine_Api::_() -> getItemTable('ynresume_resume');
			$select = $tableResume -> select() -> where('industry_id = ?', $industry -> industry_id);
			$cur_arr = $tableResume -> fetchAll($select);
			if (count($cur_arr) > 0) {
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
	
    public function getResumesPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this -> getResumesSelect($params));
        if(isset($params['page']) && !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if(isset($params['limit']) && !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getResumesSelect($params = array()) {
    	$resumeTbl = Engine_Api::_() -> getItemTable('ynresume_resume');
        $resumeTblName = $resumeTbl -> info('name');

        $educationTbl = Engine_Api::_() -> getDbTable('educations', 'ynresume');
        $experienceTbl = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
        $experienceTblName = $experienceTbl->info('name');
        
        $select = $resumeTbl -> select();

        //Get your location
        $target_distance = $base_lat = $base_lng = "";
        if (isset($params['lat']))
        	$base_lat = $params['lat'];
        if (isset($params['long']))
        	$base_lng = $params['long'] + 0.000001;

        //Get target distance in miles
        if (isset($params['within']))
        $target_distance = $params['within'];
        else {
            $target_distance = 50;
        }

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select -> from("$resumeTblName as resume", array("resume.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( resume.latitude ) ) * cos( radians( resume.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( resume.latitude ) ) ) ) AS distance"));
            $select -> where("resume.latitude <> ''");
            $select -> where("resume.longitude <> ''");

        } else {
            $select -> from("$resumeTblName as resume", "resume.*");
        }
        
        //search by name
        if (isset($params['title']) && $params['title'] != '') {
            $select->where('resume.name LIKE ?', '%'.$params['title'].'%');
        }
        
        //search by headline        
        if (isset($params['headline']) && $params['headline'] != '') {
            $select->where('resume.headline LIKE ?', '%'.$params['headline'].'%');
        }
        
        //search by industry_id
        if (isset($params['industry_id']) && $params['industry_id'] != 'all') {
            $select->where('resume.industry_id = ?', $params['industry_id']);
        }
        
        //search by job title
        if (isset($params['job_title']) && $params['job_title'] != '') {
            $experienceSelect = $experienceTbl->select()->where('title LIKE ?', '%'.$params['job_title'].'%');
            $experiences = $experienceTbl->fetchAll($experienceSelect);
            $ids = array();
            foreach ($experiences as $experience) {
                array_push($ids, $experience->resume_id);
            }
            if (empty($ids)) array_push($ids, 0);
            $search = $params['job_title'];
            $ids_str = implode(',',$ids);
            $select->where("(title LIKE '%{$search}%') OR (resume_id IN ({$ids_str}))");
        }

        //search by Company
        if (isset($params['company']) && $params['company'] != '') {
            $experienceSelect = $experienceTbl->select()->where('company LIKE ?', '%'.$params['company'].'%');
            $experiences = $experienceTbl->fetchAll($experienceSelect);
            $ids = array();
            foreach ($experiences as $experience) {
                array_push($ids, $experience->resume_id);
            }
            
            if (Engine_Api::_()->hasModuleBootstrap('ynbusinesspages')) {
                $businessTbl = Engine_Api::_()->getDbTable('business', 'ynbusinesspages');
                $businessTblName = $businessTbl->info('name');
                $experienceSelect = $experienceTbl->select()->setIntegrityCheck(false);
                $experienceSelect->from("$experienceTblName as experience","experience.*");
                $experienceSelect->joinLeft("$businessTblName as business", "business.business_id = experience.business_id","");
                $experienceSelect->where('business.name LIKE ?', '%'.$params['company'].'%');
                $experiences = $experienceTbl->fetchAll($experienceSelect);
                foreach ($experiences as $experience) {
                    array_push($ids, $experience->resume_id);
                }
            }
            
            if (empty($ids)) array_push($ids, 0);
            $search = $params['company'];
            $ids_str = implode(',',$ids);
            $select->where("(company LIKE '%{$search}%') OR (resume_id IN ({$ids_str}))");
        }
        
        //search by School
        if (isset($params['school']) && $params['school'] != '') {
            $educationSelect = $educationTbl->select()->where('title LIKE ?', '%'.$params['school'].'%');
            $educations = $educationTbl->fetchAll($educationSelect);
            $ids = array();
            foreach ($educations as $education) {
                array_push($ids, $education->resume_id);
            }
            if (empty($ids)) array_push($ids, 0);
            $select->where('resume_id IN (?)', $ids);
        }
		
		if(isset($params['featured']) && $params['featured'] != '')	{
			$select->where('featured = ?', 1);
		}
		
		//Order by filter
		if (isset($params['order'])) {
	        if (empty($params['direction'])) {
	            $params['direction'] = ($params['order'] == 'resume.name') ? 'ASC' : 'DESC';
	        }
            $select->order($params['order'].' '.$params['direction']);
		}
		else {
	        if (!empty($params['direction'])) {
	            $select->order('resume.resume_id'.' '.$params['direction']);
	        }
			else{
				$select->order('resume.resume_id DESC');
			}
	    }
		
        // my favourite
        if(isset($params['favouriter_id']) && isset($params['favourite'])) {
            $favouriteTable = Engine_Api::_() -> getDbTable('favourites', 'ynresume');
            $resumeIds = array();
            foreach($favouriteTable -> getFavouriteResumes($params['favouriter_id']) as $resume) {
                $resumeIds[] = $resume -> resume_id;
            }
            if(!$resumeIds) {
                $resumeIds[] = 0;
            }
            $select -> where("resume.resume_id IN (?)", $resumeIds);
        }
        
        // my save
        if(isset($params['saver_id']) && isset($params['save'])) {
            $saveTable = Engine_Api::_() -> getDbTable('saves', 'ynresume');
            $resumeIds = array();
            foreach($saveTable -> getSavedResumes($params['saver_id']) as $resume) {
                $resumeIds[] = $resume -> resume_id;
            }
            if(!$resumeIds) {
                $resumeIds[] = 0;
            }
            $select -> where("resume.resume_id IN (?)", $resumeIds);
        }
        
        // Order
        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select -> having("distance <= $target_distance");
            $select -> order("distance ASC");
        }
        
		if (!isset($params['search']) || !$params['search'] != '')
		{
			$select->where('search = ?', 1);
		}
		$select->where('active = ?', 1);
        return $select;
    }
    
}