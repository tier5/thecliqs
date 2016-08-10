<?php
class Ynresume_Model_Resume extends Core_Model_Item_Abstract {
	protected $_type = 'ynresume_resume';
	protected $_owner_type = 'user';

	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynresume_specific',
			'reset' => true,
			'resume_id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	
	public function getBadge()
	{
		$table = Engine_Api::_() -> getDbTable('badges', 'ynresume');
		$badges = $table->fetchAll($table->select()->order('order ASC'));
		$isReached = false;
		$isPassed = true;
		foreach($badges as $badge)
		{
			switch ($badge -> condition) {
				case 'view':
					$value = $badge -> value;
					if($this -> view_count >= $value)
					{
						$isReached  = true;
					}
					break;
				case 'completeness':
					$arr = unserialize($badge->value);
					foreach($arr as $type)
					{
					    if (strpos($type, 'field_') !== FALSE) {
					    	$arr = explode('_', $type);
							
							$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynresume_resume');
							if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
								$profileTypeField = $topStructure[0] -> getChild();
								$formArgs = array(
									'topLevelId' => $profileTypeField -> field_id, 
									'topLevelValue' => 1,
									'heading' => $arr[1],
								);
							}
							 $form = new Ynresume_Form_Custom_Create( array(
								'formArgs' => $formArgs,
								'item' => $this,
							));
							if (!$form -> isValid($form -> getValues())) {
								$isPassed = false;
								break;
							}
							
						}
						else
						{
							switch ($type) {
								case 'photo':
									if($this -> photo_id == 0)
									{
										$isPassed = false;
										break;
									}
									break;
								case 'general_info':
									if(empty($this -> title))
									{
										$isPassed = false;
										break;
									}
									break;
								case 'summary':
									if(empty($this -> summary))
									{
										$isPassed = false;
										break;
									}
									break;
								case 'experience':
									if(!$this -> hasExperience())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'education':
									if(!$this -> hasEducation())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'certification':
									if(!$this -> hasCertification())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'language':
									if(!$this -> hasLanguage())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'skill':
									if(!$this -> hasSkill())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'publication':
									if(!$this -> hasPublication())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'project':
									if(!$this -> hasProject())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'honor_award':
									if(!$this -> hasAwards())
                                    {
                                        $isPassed = false;
                                        break;
                                    }
									break;	
								case 'course':
									if(!$this -> hasCourse())
									{
										$isPassed = false;
										break;
									}
									break;
								case 'contact':
									if(empty($this -> email))
									{
										$isPassed = false;
										break;
									}
									break;	
							}
						}
					}
					if($isPassed)
					{
						return $badge;
					}
					break;
				case 'endorsements':
					
					break;
				case 'recommendations':
					
					break;		
			}
			if($isReached)
			{
				return $badge;
			}
		}
		return false;
	}
	
	public function delete() {
		//TODO delete all stuffs belong to resume
		
		//remove pending recommendations
		Engine_Api::_()->getDbTable('recommendations', 'ynresume')->removeRecommendationsOfReceiver($this->user_id);		
		 $owner = $this -> getOwner();
		 if($this -> photo_id)
		 {
		 	$fileTable = Engine_Api::_() -> getDbTable('files', 'storage');
			$filePhoto = $fileTable -> getFile($this -> photo_id);
			if( $filePhoto instanceof Storage_Model_File )
			{
				$owner -> setPhoto($filePhoto);
			}
		 }
		 parent::delete();
	}
	
	public function getIndustry() {
        $industry = Engine_Api::_()->getItem('ynresume_industry', $this->industry_id);
        if ($industry) {
            return $industry;
        }
    }
	
    public function getSlug($str = NULL, $maxstrlen = 64) {
        $str = $this -> getTitle();
        if (strlen($str) > 32)
        {
            $str = Engine_String::substr($str, 0, 32) . '...';
        }
        $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = trim($str, '-');
        if (!$str)
        {
            $str = '-';
        }
        return $str;
    }
    
	public function hasExperience() {
        $table = Engine_Api::_()->getDbTable('experiences','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
    public function hasAwards() {
        $table = Engine_Api::_()->getDbTable('awards','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $results = $table->fetchAll($select);
        return (count($results)) ? true : false;
    }
	
	public function hasEducation() {
        $table = Engine_Api::_()->getDbTable('educations','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
	
	public function hasCertification() {
        $table = Engine_Api::_()->getDbTable('certifications','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
	public function hasPublication() {
        $table = Engine_Api::_()->getDbTable('publications','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
     
	public function hasSkill() {
        $table = Engine_Api::_()->getDbTable('SkillMaps','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
	public function hasLanguage() {
        $table = Engine_Api::_()->getDbTable('languages','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
	public function hasProject() {
        $table = Engine_Api::_()->getDbTable('projects','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
	public function hasCourse() {
        $table = Engine_Api::_()->getDbTable('courses','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
	public function hasSaved($user_id = null) {
        if ($user_id == null) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }
        $table = Engine_Api::_()->getDbTable('saves','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity())->where('user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
	
	public function hasFavourited($user_id = null) {
        if ($user_id == null) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }
        $table = Engine_Api::_()->getDbTable('favourites','ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity())->where('user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
	
    public function getTitle() {
        if(isset($this->name)) {
            return $this->name;
        }
        return null;
    }
    
    public function getJobTitle() {
        if(isset($this->title)) {
            return $this->title;
        }
        return null;
    }
    
    public function getCompany() {
        if(isset($this->company)) {
            return $this->company;
        }
        return null;
    }
    
    public function getSummary() {
        if(isset($this->summary)) {
            return strip_tags($this->summary);
        }
        return null;
    }
    
    public function getDescription() {
        if(isset($this->description)) {
            return $this->description;
        }
        return null;
    }
    
	
	public function setPhoto($photo)
    {
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynresume_Model_Exception('invalid argument passed to setPhoto');
		}
	
		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynresume_resume',
			'parent_id' => $this -> getIdentity(),
		);
	
		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if(function_exists('exif_read_data'))
		{
			$exif = exif_read_data($file);
			if (!empty($exif['Orientation']))
			{
				switch($exif['Orientation'])
				{
					case 8 :
						$angle = 90;
						break;
					case 3 :
						$angle = 180;
						break;
					case 6 :
						$angle = -90;
						break;
				}
			}
		}	
		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();
	
		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(400, 400) -> write($path . '/p_' . $name) -> destroy();
	
		// Resize image (normal)
		$image = Engine_Image::factory();
		@$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(140, 105) -> write($path . '/in_' . $name) -> destroy();
	
		// Resize image (icon)
	   $image = Engine_Image::factory();
	   $image->open($file);
	   
	   $size = min($image->height, $image->width);
	   $x = ($image->width - $size) / 2;
	   $y = ($image->height - $size) / 2;
	
	   $image->resample($x, $y, $size, $size, 48, 48)
	     ->write($path.'/is_'.$name)
	     ->destroy();
	
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iSquare = $storage->create($path.'/is_'.$name, $params);
	
		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');
		
		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/is_' . $name);
	
		$this -> photo_id = $iMain -> file_id;
		$this -> save();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer -> photo_id = $iMain -> file_id;
		$viewer -> save();
		
		return $this;
    }

    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
    
    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit'); 
    }

    function isDeletable() {
    	return $this->authorization()->isAllowed(null, 'delete');
    }

    public function skills()
    {
    	return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('skills', 'ynresume'));
    }
    
    public function skipedByUser($user)
    {
    	$skipTbl = Engine_Api::_()->getDbTable('skips', 'ynresume');
    	$select = $skipTbl -> select()
    	->where ("user_id = ? ", $user->getIdentity())
    	->where ("resume_id = ? ", $this->getIdentity())
    	->where ("value = 1 ")
    	->limit(1);
    	$row = $skipTbl -> fetchRow($select);
    	return (!is_null($row)); 
    }
    
    public function setSkip($user)
    {
    	$skipTbl = Engine_Api::_()->getDbTable('skips', 'ynresume');
    	$skipTbl -> setSkip ($this, $user);
    }
    
    public function resetSkip()
    {
    	$table = $this -> getTable();
    	$adapter = $table -> getDefaultAdapter();
    	$sql = "update `engine4_ynresume_skips` set `value` = 0";
    	$adapter -> query($sql); 
    }
    
    function getMediaType() {
        return 'resume';
    }
    
    function getOrder() {
        $table = Engine_Api::_()->getDbTable('resumeorder', 'ynresume');
        $select = $table->select()->where('resume_id = ?', $this->getIdentity());
        $order = $table->fetchRow($select);
        return $order;
    }
    
    function getAllExperience() {
        return Engine_Api::_()->getDbTable('experiences', 'ynresume')->getExperiencesByResumeId($this->getIdentity());
    }

    function getAllEducation() {
        return Engine_Api::_()->getDbTable('educations', 'ynresume')->getEducationsByResumeId($this->getIdentity());
    }
    
    function getAllAwards() {
        return Engine_Api::_()->getDbTable('awards', 'ynresume')->getAwardsByResumeId($this->getIdentity());
    }
    
	function getAllPublication() {
        return Engine_Api::_()->getDbTable('publications', 'ynresume')->getPublicationsByResumeId($this->getIdentity());
    }
    
	function getAllProject() {
        return Engine_Api::_()->getDbTable('projects', 'ynresume')->getProjectsByResumeId($this->getIdentity());
    }
    
	function getAllCertification() {
        return Engine_Api::_()->getDbTable('certifications', 'ynresume')->getCertificationsByResumeId($this->getIdentity());
    }
    
	function getAllLanguage() {
        return Engine_Api::_()->getDbTable('languages', 'ynresume')->getLanguagesByResumeId($this->getIdentity());
    }
    
	function getAllCourse() {
        return Engine_Api::_()->getDbTable('courses', 'ynresume')->getCoursesByResumeId($this->getIdentity());
    }
    
    function getAllSkills($onlyEndorsedUser = true)
    {
		$owner = $this -> getOwner();
		$skillTbl = Engine_Api::_()->getDbtable('skills', 'ynresume');
		$totalSkills = $skillTbl->getSkillsByUser($this, $owner);
		$arr = array();
		foreach ($totalSkills as $skill)
		{
			$item = $skill -> toArray();
			$item['endorses'] = $skill -> getEndorsedUsers($this, $onlyEndorsedUser);
			if (count($item['endorses']))
			{
				foreach ($item['endorses'] as $endorse)
				{
                    $item['endorsed_user_ids'][] = $endorse -> user_id;
				}
			}
			else 
			{
				$item['endorsed_user_ids'] = array();
			}
			$arr[] = $item;
		}
		return $arr;
    }



    public function addSection($section, $params) {
        if (!$section || !$params) {
            return false;
        }
        switch ($section) {
            case 'experience':
                $table = Engine_Api::_()->getDbTable('experiences', 'ynresume');
                if (isset($params['current']) && $params['current']) {
                    $params['end_year'] = null;
                    $params['end_month'] = null;
                }
                if (isset($params['business_id'])) {
                	if ($params['business_id'] == '0' || !Engine_Api::_()->hasItemType('ynbusinesspages_business')) {
                		$params['business_id'] = null;
                	}
					else {
						$business = Engine_Api::_()->getItem('ynbusinesspages_business', $params['business_id']);
						if (!$business || ($business->getTitle() != $params['company'])) {
							$params['business_id'] = null;
						}
					}
                }
				$stripTagKeys = array('title', 'company', 'description', 'location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
                
            case 'education':
                $table = Engine_Api::_()->getDbTable('educations', 'ynresume');
				$stripTagKeys = array('title', 'study_field', 'grade', 'activity', 'description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
                
            case 'honor_award':
                $table = Engine_Api::_()->getDbTable('awards', 'ynresume');
                if (isset($params['date_month']) && $params['date_month'] == '0') {
                    $params['date_month'] = null;
                }
                if (isset($params['date_year']) && $params['date_year'] == '0000') {
                    $params['date_year'] = null;
                }
                if (isset($params['occupation']) && $params['occupation']) {
                    $occupation = explode('-', $params['occupation']);
                    $params['occupation_type'] = $occupation[0];
                    $params['occupation_id'] = $occupation[1];
                }
				$stripTagKeys = array('title', 'issuer');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
			
            case 'certification':
                $table = Engine_Api::_()->getDbTable('certifications', 'ynresume');
                $stripTagKeys = array('name', 'authority', 'license_number', 'url');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                break;    

            case 'language':
                $langTbl = Engine_Api::_()->getDbTable('languages', 'ynresume');
                $stripTagKeys = array('name', 'proficiency');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                $item = $langTbl->createRow();
                $item->resume_id = $this->getIdentity();
                $item->setFromArray($params);
                $item->save();
                break; 
			
            case 'course':
                $courseTbl = Engine_Api::_()->getDbTable('courses', 'ynresume');
                if (isset($params['associated']) && !empty($params['associated']))
                {
                	$tempArr = explode('::', $params['associated']);
                	if (count($tempArr) == 2)
                	{
                		$params['associated_type'] = $tempArr[0];
                		$params['associated_id'] = $tempArr[1];
                	}
                }
                $item = $courseTbl->createRow();
                $item->resume_id = $this->getIdentity();
                $stripTagKeys = array('name', 'number', 'associated');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                $item->setFromArray($params);
                $item->save();
                break;

			case 'project':
                if (isset($params['occupation']) && !empty($params['occupation']))
                {
                	$tempArr = explode('::', $params['occupation']);
                	if (count($tempArr) == 2)
                	{
                		$params['occupation_type'] = $tempArr[0];
                		$params['occupation_id'] = $tempArr[1];
                	}
                }
                $stripTagKeys = array('name', 'occupation', 'url', 'description');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                $this -> saveProject($params);
                break;
                
			case 'summary':
                $this -> summary = $params['summary'];
				$this -> save();
                break;
				
			case 'contact':
				if(!empty($params['birth_day']))
				{
	                $date = new Zend_Date(strtotime($params['birth_day']));
					$params['birth_day'] = $date -> get('YYYY-MM-dd HH:mm:ss');
				}
				$params['nationality'] = strip_tags($params['nationality']);
				$params['phone'] = strip_tags($params['phone']);
				$params['email'] = strip_tags($params['email']);
				$params['location'] = strip_tags($params['location']);
                $this -> setFromArray($params);
				$this -> save();
                break;	
			
			case 'publication':
        		if(!empty($params['publication_year']) && !empty($params['publication_month']))
				{
	                //$date = new Zend_Date(strtotime($params['publication_date']));
                	//$params['publication_date'] = $date -> get('YYYY-MM-dd HH:mm:ss');
                    $params['publication_date'] = "{$params['publication_year']}-{$params['publication_month']}-01";
				}
                $stripTagKeys = array('title', 'publisher', 'url', 'description');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
				$this -> savePublication($params);
                break;
					
        };
        if ($table) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $item = $table->createRow();
                $item->resume_id = $this->getIdentity();
                $item->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $item->setFromArray($params);
                $item->save();
                
                if (isset($params['photo_ids'])) {
                    $photo_ids = trim($params['photo_ids']);
                    $photo_ids = explode(' ', $photo_ids);
                    Engine_Api::_()->getItemTable('ynresume_photo')->updatePhotoParent($photo_ids, $item);
                }
                $db->commit();
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
    
	public function saveProject($params, $item = null)
    {
    	$table = Engine_Api::_()->getDbTable('projects', 'ynresume');
                
		// Insert new publication
		$db = Engine_Db_Table::getDefaultAdapter();
	    $db->beginTransaction();
	    try 
	    {
	    	if (is_null($item))
	    	{
	    		$item = $table->createRow();	
	    	}
			
            $item->resume_id = $this->getIdentity();
            $item->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $item->setFromArray($params);
            $item->save();
               
            // Insert 
            if (isset($params['memberValues']) && $params['memberValues'] != '')
            {
            	$ids = explode(',', $params['memberValues']);
            	if (count($ids))
            	{
            		$i = 0;
            		foreach ($ids as $id)
            		{
            			$i++;
            			if (is_numeric($id))
            			{
            				$user = Engine_Api::_()->user()->getUser($id);
            				if (!$user -> getIdentity())
            					break;
            				$item -> addMember ($user, $i);
            			}
            			else 
            			{
            				$item -> addMember ($id, $i);
            			}
            		}
            	}
            }
            
	    	if (isset($params['photo_ids'])) {
				$photo_ids = trim($params['photo_ids']);
                $photo_ids = explode(' ', $photo_ids);
                Engine_Api::_()->getItemTable('ynresume_photo')->updatePhotoParent($photo_ids, $item);
			}
            $db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			throw $e;
	    }
    }
    
    public function savePublication($params, $item = null)
    {
    	$table = Engine_Api::_()->getDbTable('publications', 'ynresume');
                
		// Insert new publication
		$db = Engine_Db_Table::getDefaultAdapter();
	    $db->beginTransaction();
	    try 
	    {
	    	if (is_null($item))
	    	{
				$item = $table->createRow();
	    	}
            $item->resume_id = $this->getIdentity();
            $item->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $item->setFromArray($params);
            $item->save();
               
            // Insert 
            if (isset($params['authorValues']) && $params['authorValues'] != '')
            {
            	$ids = explode(',', $params['authorValues']);
            	if (count($ids))
            	{
            		$i = 0;
            		foreach ($ids as $id)
            		{
            			$i++;
            			if (is_numeric($id))
            			{
            				$user = Engine_Api::_()->user()->getUser($id);
            				if (!$user -> getIdentity())
            					break;
            				$item -> addAuthor ($user, $i);
            			}
            			else 
            			{
            				$item -> addAuthor ($id, $i);
            			}
            		}
            	}
            }
            
	    	if (isset($params['photo_ids'])) {
				$photo_ids = trim($params['photo_ids']);
                $photo_ids = explode(' ', $photo_ids);
                Engine_Api::_()->getItemTable('ynresume_photo')->updatePhotoParent($photo_ids, $item);
			}
            $db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			throw $e;
	    }
    }
    
    public function editSection($section, $params) {
        if (!$section || !$params || !isset($params['item_id'])) {
            return false;
        }
        switch ($section) {
            case 'experience':
                $item = Engine_Api::_()->getItem('ynresume_experience', $params['item_id']);
                if (isset($params['current']) && $params['current']) {
                    $params['end_year'] = null;
                    $params['end_month'] = null;
                }
                if (isset($params['business_id'])) {
                	if ($params['business_id'] == '0' || !Engine_Api::_()->hasItemType('ynbusinesspages_business')) {
                		$params['business_id'] = null;
                	}
					else {
						$business = Engine_Api::_()->getItem('ynbusinesspages_business', $params['business_id']);
						if (!$business || ($business->getTitle() != $params['company'])) {
							$params['business_id'] = null;
						}
					}
                }
				$stripTagKeys = array('title', 'company', 'description', 'location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
                
            case 'education':
                $item = Engine_Api::_()->getItem('ynresume_education', $params['item_id']);
				$stripTagKeys = array('title', 'study_field', 'grade', 'activity', 'description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
                
            case 'honor_award':
                $item = Engine_Api::_()->getItem('ynresume_award', $params['item_id']);
                if (isset($params['date_month']) && $params['date_month'] == '0') {
                    $params['date_month'] = null;
                }
                if (isset($params['date_year']) && $params['date_year'] == '0000') {
                    $params['date_year'] = null;
                }
                if (isset($params['occupation']) && $params['occupation']) {
                    $occupation = explode('-', $params['occupation']);
                    $params['occupation_type'] = $occupation[0];
                    $params['occupation_id'] = $occupation[1];
                }
				$stripTagKeys = array('title', 'issuer');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
				
			case 'certification':
                $item = Engine_Api::_()->getItem('ynresume_certification', $params['item_id']);
                $stripTagKeys = array('name', 'authority', 'license_number', 'url');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                break;                
                
            case 'language':
                $item = Engine_Api::_()->getItem('ynresume_language', $params['item_id']);
                $stripTagKeys = array('name', 'proficiency');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                break;

			case 'course':
                $item = Engine_Api::_()->getItem('ynresume_course', $params['item_id']);
                $stripTagKeys = array('name', 'number', 'associated');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
        		if (isset($params['associated']) && !empty($params['associated']))
                {
                	$tempArr = explode('::', $params['associated']);
                	if (count($tempArr) == 2)
                	{
                		$params['associated_type'] = $tempArr[0];
                		$params['associated_id'] = $tempArr[1];
                	}
                }
                break;

			case 'summary':
                $this -> summary = $params['summary'];
				$this -> save();
                break;	
				
			case 'contact':
				$date = new Zend_Date(strtotime($params['birth_day']));
				$params['birth_day'] = $date -> get('YYYY-MM-dd HH:mm:ss');
				$params['nationality'] = strip_tags($params['nationality']);
				$params['phone'] = strip_tags($params['phone']);
				$params['email'] = strip_tags($params['email']);
				$params['location'] = strip_tags($params['location']);
                $this -> setFromArray($params);
				$this -> save();
                break;	

			case 'publication':
                $item = Engine_Api::_()->getItem('ynresume_publication', $params['item_id']);
                //$date = new Zend_Date(strtotime($params['publication_date']));
                //$params['publication_date'] = $date -> get('YYYY-MM-dd HH:mm:ss');
                if (!empty($params['publication_year']) && !empty($params['publication_month']))
                    $params['publication_date'] = "{$params['publication_year']}-{$params['publication_month']}-01";
                $item -> setFromArray($params);
                $item -> save();
		    	$authors = $item -> getAuthorObjects();
		    	foreach ($authors as $author){
		    		$author -> delete();
		    	}
                $stripTagKeys = array('title', 'publisher', 'url', 'description');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                $this -> savePublication($params, $item);
                break;
                
			case 'project':
                $item = Engine_Api::_()->getItem('ynresume_project', $params['item_id']);
                $item -> setFromArray($params);
                $item -> save();
		    	$members = $item -> getMemberObjects();
		    	foreach ($members as $member){
		    		$member -> delete();
		    	}
                $stripTagKeys = array('name', 'occupation', 'url', 'description');
                foreach ($stripTagKeys as $key) {
                    if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
                }
                $this -> saveProject($params, $item);
                break;
            
        };
        if ($item) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $item->setFromArray($params);
                $item->save();
                $db->commit();
                if (isset($params['photo_ids'])) {
                    $ids_b = Engine_Api::_()->getItemTable('ynresume_photo')->getPhotoIdsItem($item);
                    Engine_Api::_()->getItemTable('ynresume_photo')->resetPhotoItem($this, $item);
                    $photo_ids = trim($params['photo_ids']);
                    $photo_ids = explode(' ', $photo_ids);
                    Engine_Api::_()->getItemTable('ynresume_photo')->updatePhotoParent($photo_ids, $item);
                    $ids_a = Engine_Api::_()->getItemTable('ynresume_photo')->getPhotoIdsItem($item);
                    $diff = array_diff($ids_b, $ids_a);
                    foreach ($diff as $id) {
                        Engine_Api::_()->ynresume()->deletePhoto($id);
                    }
                }
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
    
    public function getOwnerSkillMaps()
    {
    	$owner = $this -> getOwner();
    	$select = $this -> skills() -> getSkillMapSelect($owner);
    	$skillmapTbl = $this -> skills() -> getMapTable();
		return $skillmapTbl -> fetchAll($select);    	
    }
    
    public function removeSection($section, $params) {
        if (!$section || !$params || !isset($params['item_id'])) {
            return false;
        }
        switch ($section) {
            case 'experience':
                $item = Engine_Api::_()->getItem('ynresume_experience', $params['item_id']);
                Engine_Api::_()->getDbTable('recommendations', 'ynresume')->removeRecommendationsByItem('experience', $item->getIdentity());
                break;
                
            case 'education':
                $item = Engine_Api::_()->getItem('ynresume_education', $params['item_id']);
                Engine_Api::_()->getDbTable('recommendations', 'ynresume')->removeRecommendationsByItem('education', $item->getIdentity());
                break;
                
			case 'certification':
                $item = Engine_Api::_()->getItem('ynresume_certification', $params['item_id']);
                break;
                
            case 'honor_award':
                $item = Engine_Api::_()->getItem('ynresume_award', $params['item_id']);
                break;
                
			case 'summary':
                $this -> summary = "";
				$this -> save();
                break;
                
			case 'publication':
                $item = Engine_Api::_()->getItem('ynresume_publication', $params['item_id']);
                $item -> removeAllAuthors();
                break;

			case 'project':
                $item = Engine_Api::_()->getItem('ynresume_project', $params['item_id']);
                $item -> removeAllMembers();
                break;
                
			case 'language':
                $item = Engine_Api::_()->getItem('ynresume_language', $params['item_id']);
                break;
			
			case 'course':
                $item = Engine_Api::_()->getItem('ynresume_course', $params['item_id']);
                break;
        };
        if ($item) {
            $item->delete();
        }
    }
    
    public function getOccupations() {
        $view = Zend_Registry::get('Zend_View');
        $experience = $this->getAllExperience();
        $education = $this->getAllEducation();
        $occupations = array();
        $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
        foreach ($experience as $item) {
            $business = ($business_enable && $item->business_id) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
            $company = ($business) ? $business->getTitle() : $item->company;
            $occupation = array(
                'type' => 'experience',
                'item_id' => $item->getIdentity(),
                'id' => 'experience-'.$item->getIdentity(),
                'title' => $item->title.' '.$view->translate('at').' '.$company,
                'item_position' => $item->title,
                'item_title' => $company,
            );
            array_push($occupations, $occupation);
        }

        foreach ($education as $item) {
            $occupation = array(
                'type' => 'education',
                'item_id' => $item->getIdentity(),
                'id' => 'education-'.$item->getIdentity(),
                'title' => $view->translate('Student').' '.$view->translate('at').' '.$item->title,
                'item_position' => $view->translate('Student'),
                'item_title' => $item->title,
            );
            array_push($occupations, $occupation);
        }
        
        return $occupations;
    }
    
    public function getAvailableOccupations($giver_id) {
        $view = Zend_Registry::get('Zend_View');
        $experience = $this->getAllExperience();
        $education = $this->getAllEducation();
        $table = Engine_Api::_()->getDbTable('recommendations', 'ynresume');
        $notAvailableExperience = $table->getExperienceIds($giver_id, $this->user_id);
        $notAvailableEducation = $table->getEducationIds($giver_id, $this->user_id);
        $occupations = array();
        $business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
        foreach ($experience as $item) {
            if (!in_array($item->getIdentity(), $notAvailableExperience)) {
                $business = ($business_enable && $item->business_id) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
                $company = ($business) ? $business->getTitle() : $item->company;
                $occupation = array(
                    'type' => 'experience',
                    'item_id' => $item->getIdentity(),
                    'id' => 'experience-'.$item->getIdentity(),
                    'title' => $item->title.' '.$view->translate('at').' '.$company,
                    'item_position' => $item->title,
                    'item_title' => $company,
                );
                array_push($occupations, $occupation);
            }
        }

        foreach ($education as $item) {
            if (!in_array($item->getIdentity(), $notAvailableEducation)) {
                $occupation = array(
                    'type' => 'education',
                    'item_id' => $item->getIdentity(),
                    'id' => 'education-'.$item->getIdentity(),
                    'title' => $view->translate('Student').' '.$view->translate('at').' '.$item->title,
                    'item_position' => $view->translate('Student'),
                    'item_title' => $item->title,
                );
                array_push($occupations, $occupation);
            }
        }
        
        return $occupations;
    }

    public function isEndorseNotify()
    {
    	$endorseNotifyTbl = Engine_Api::_() -> getDbTable('EndorseNotify', 'ynresume');
    	return $endorseNotifyTbl -> needNotify($this);
    }

    public function getCourseAssociatedAssoc()
    {
    	$education = $this -> getAllEducation();
    	$experience = $this -> getAllExperience();
    	$result = array();
    	foreach ($education as $edu){
    		$key = 'ynresume_education::' . $edu -> getIdentity();
    		$result[$key] = $edu -> title;
    	}
    	foreach ($experience as $exp){
    		$key = 'ynresume_experience::' . $exp -> getIdentity();
    		$result[$key] = $exp -> title;
    	}
        $result['ynresume_education::0'] = Zend_Registry::get("Zend_Translate")->_('Others');
    	return $result;
    }
    
    public function getProjectOccupationAssoc()
    {
    	return $this -> getCourseAssociatedAssoc(); 
    }
    
    public function renderText($forWord = false)
    {
    	$resumeOrderTbl = Engine_Api::_() -> getDbTable('resumeorder', 'ynresume');
    	$resumeOrderSelect = $resumeOrderTbl -> select() -> where("resume_id = ?", $this -> getIdentity()) -> limit(1);
    	$resumeOrder = $resumeOrderTbl -> fetchRow($resumeOrderSelect);
    	if (is_null($resumeOrder))
    	{
    		$resumeOrder = array(
    			"summary",
    			"experience",
    			"skill",
	    		"education",
	    		"publication",
	    		"language",
	    		"certification",
	    		"honor_award",
	    		"course",
    			"project",
    			"contact"
    		);
    	}
    	else 
    	{
    		$resumeOrder = $resumeOrder -> order;
    	}
    	$text = "";
        if (!$forWord)
        {
            $text .= $this -> renderProfileInfo();
        }
    	//$text .= $this -> renderSummary();
    	foreach ($resumeOrder as $sectionName)
    	{
    		$method = 'render' . ucfirst($sectionName);
    		if (!method_exists($this, $method))
    		{
    			continue;
    		}
    		$text .= $this->{$method}();
    	}
    	return $text;
    }
    
    public function renderHeader($text)
    {
    	return "<h2>{$text}</h2>";
    }
    
    public function renderProfileInfo()
    {
    	$text = "";
    	$translate = Zend_Registry::get("Zend_Translate");
    	$user = Engine_Api::_()->user() -> getUser($this -> user_id);
    	if ($user -> getIdentity() == 0 || is_null($user))
    	{
    		return '';
    	}
    	
    	//image url
    	$file = Engine_Api::_()->getItemTable('storage_file')->getFile($user->photo_id, 'thumb.profile');
    	$imageUrl = $file -> getHref();

    	if ($imageUrl)
    	{
            if (!preg_match("~^(?:f|ht)tps?://~i", $imageUrl)) {
                $imageUrl = Engine_Api::_()->ynresume()->getFullUrl($imageUrl);
            }
    		$imageUrl = "<img src='{$imageUrl}' />";
    	}
    	else 
    	{
    		$imageUrl = '';
    	}
    	
    	//industry
    	$industry = $this -> getIndustry();
    	$industryTitle = '';
    	if(!empty($industry))
    	{
    		$industryTitle = "<div>{$industry->getTitle()}</div>";
    	}
    	
    	//current experience
    	$tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
    	$currentExp = '';
		$currentExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), true, 3);
		if(count($currentExperiences) > 0)
		{
			$experiences = array();
			foreach ($currentExperiences as $experience){
				$experiences[] = $experience -> company;
			}
			$currentExp = "<div>" . $translate -> _('Current').": ".implode(", ", $experiences) . "</div>";
		}
    	
		//previous experience
		$previousExp  = '';
   	 	$previousExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), false, 3);
		if(count($previousExperiences) > 0)
		{
			$experiences_arr = array();
			foreach ($previousExperiences as $experience){
				$experiences_arr[] = $experience -> company;
			}
			$previousExp = "<div>" . $translate -> _('Previous').": ".implode(", ", $experiences_arr). "</div>";
		}
		
		//education
		$educationStr = '';
    	$tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
		$educations = $tableEducations -> getEducationsByResumeId($this -> getIdentity(), 3);
		if(count($educations) > 0)
		{
			$educations_arr = array();
			foreach ($educations as $education){
				$educations_arr[] = $education -> title;
			}
			$educationStr = "<div>" . $translate -> _('Education').": ".implode(", ", $educations_arr). "</div>";
		}
		
    	$text .= <<<EOF
	<table style="width: 100%;">
		<tr>
			<td style="width: 210px">
				{$imageUrl}
			</td>
			<td>
				<h3>{$user -> getTitle()}</h3>
				<div>{$this -> headline}</div>
				<div>{$this -> location}</div>
				{$industryTitle}
				{$currentExp}
				{$previousExp}
				{$educationStr}
			</td>
		</tr>
	</table>
EOF;
    	return $text;
    }

    public function renderProfileInfoForWord()
    {
        $text = "";
        $translate = Zend_Registry::get("Zend_Translate");
        $user = Engine_Api::_()->user() -> getUser($this -> user_id);
        if ($user -> getIdentity() == 0 || is_null($user))
        {
            return '';
        }

        //industry
        $industry = $this -> getIndustry();
        $industryTitle = '';
        if(!empty($industry))
        {
            $industryTitle = "{$industry->getTitle()}";
        }

        //current experience
        $tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
        $currentExp = '';
        $currentExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), true, 3);
        if(count($currentExperiences) > 0)
        {
            $experiences = array();
            foreach ($currentExperiences as $experience){
                $experiences[] = $experience -> company;
            }
            $currentExp = $translate -> _('Current').": ".implode(", ", $experiences);
        }

        //previouse experience
        $previousExp  = '';
        $previousExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), false, 3);
        if(count($previousExperiences) > 0)
        {
            $experiences_arr = array();
            foreach ($previousExperiences as $experience){
                $experiences_arr[] = $experience -> company;
            }
            $previousExp = $translate -> _('Previous').": ".implode(", ", $experiences_arr);
        }

        //education
        $educationStr = '';
        $tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
        $educations = $tableEducations -> getEducationsByResumeId($this -> getIdentity(), 3);
        if(count($educations) > 0)
        {
            $educations_arr = array();
            foreach ($educations as $education){
                $educations_arr[] = $education -> title;
            }
            $educationStr = $translate -> _('Education').": ".implode(", ", $educations_arr);
        }

        return array(
            $user -> getTitle(),
            $this -> headline,
            $this -> location,
            $industryTitle,
            $currentExp,
            $previousExp,
            $educationStr,
        );
    }

    public function renderSummary()
    {
    	if (!$this -> summary)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Summary');
    	$text .= strip_tags($this -> summary);
    	return $text;
    }
    
	public function renderExperience()
    {
    	$items = $this -> getAllExperience();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Experience');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
	public function renderSkill()
    {
    	$skills = $this -> getAllSkills();
		if (count($skills) <= 0) 
    	{
    	    return '';
    	}
    	$text = $this -> renderHeader('Skills');
		$text .= "<div>";
		$i = 0;
    	foreach ($skills as $skill)
    	{
    		if ($i > 0) 
    			$text .= ", ";
    		$text .= "<span><b>{$skill['text']}</b></span>";
    		$i++;
    	}
    	$text .= "</div>";
    	return $text;
    }
    
	public function renderEducation()
    {
    	$items = $this -> getAllEducation();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Education');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
	public function renderPublication()
    {
    	$items = $this -> getAllPublication();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Publication');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
	public function renderLanguage()
    {
    	$items = $this -> getAllLanguage();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Language');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
    public function renderCertification()
    {
    	$table = Engine_Api::_()->getItemTable('ynresume_certification');
    	$items = $table -> getCertificationsByResumeId($this ->getIdentity());
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Certification');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
	public function renderHonor_award()
    {
    	$items = $this -> getAllAwards();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Honors & Awards');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
	public function renderCourse()
    {
    	$items = $this -> getAllCourse();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Courses');
        $translate = Zend_Registry::get("Zend_Translate");
        $resume = Engine_Api::_()->getItem('ynresume_resume', $this->resume_id);
        $education = $resume -> getAllEducation();
        $experience = $resume -> getAllExperience();
        $text .= "<div>";
        $courseTbl = Engine_Api::_()->getItemTable('ynresume_course');
        if (count($education))
        {
            foreach ($education as $edu)
            {
                $courses = $courseTbl -> getCoursesByEducation($edu);
                if (count($courses))
                {
                    $text .= "<h4>{$edu -> title}</h4>";
                    foreach ($courses as $item)
                    {
                        $text .= <<<EOF
<div>
	<span>{$item -> name}</span> |
	<span>{$item -> number}</span>
</div>
EOF;
                    }
                }
            }
        }
        if (count($experience))
        {
            foreach ($experience as $exp)
            {
                $courses = $courseTbl -> getCoursesByExperience($exp);
                if (count($courses))
                {
                    $text .= "<h4>{$exp -> title}</h4>";
                    foreach ($courses as $item)
                    {
                        $text .= <<<EOF
<div>
	<span>{$item -> name}</span> |
	<span>{$item -> number}</span>
</div>
EOF;
                    }
                }
            }
        }

        $otherCourses = $courseTbl -> getOtherCourses($resume);
        if (count($otherCourses))
        {
            $text .= "<h4>{$translate->_("Others")}</h4>";
            foreach ($otherCourses as $item)
            {
                $text .= <<<EOF
<div>
	<span>{$item -> name}</span> |
	<span>{$item -> number}</span>
</div>
EOF;
            }
        }

        $text.= "</div>";
    	return $text;
    }
    
	public function renderProject()
    {
    	$items = $this -> getAllProject();
    	if (count($items) == 0)
    	{
    		return '';
    	}
    	$text = $this -> renderHeader('Projects');
    	foreach ($items as $item)
    	{
    		$text .= $item -> renderText();
    	}
    	return $text;
    }
    
    public function renderContact()
    {
    	$translate = Zend_Registry::get("Zend_Translate");
    	$birthDayObject = null;
		if (!is_null($this -> birth_day) && !empty($this -> birth_day) && $this -> birth_day) 
		{
			$birthDayObject = new Zend_Date(strtotime($this -> birth_day));	
		}
		
		$text = $this -> renderHeader('Contact Information');
		$text .= "<div>";
		
    	if(!is_null($birthDayObject))
		{
			$date = date('M d Y', $birthDayObject -> getTimestamp());
			$text .= "<div><b>{$translate->_("Date of Birth")}</b> {$date}</div>";
		}
    	
    	if(!is_null($this->gender))
		{
			$gender = ($this->gender)? $translate->_("Male") : $translate-> _("Female");
			$text .= "<div><b>{$translate->_("Gender")}</b> {$gender}</div>";
		}
		
    	if(!is_null($this->marial_status))
		{
			$marial_status = ($this->marial_status)? $translate->_("Single") : $translate->_("Married");
			$text .= "<div><b>{$translate->_("Marital Status")}</b> {$marial_status}</div>";
		}
        
    	if(!is_null($this->nationality))
		{
			$text .= "<div><b>{$translate->_("Marital Nationality")}</b> {$this->nationality}</div>";
		}
    	
    	if(!is_null($this->email))
		{
			$text .= "<div><b>{$translate->_("Email")}</b> {$this->email}</div>";
		}
        
    	if(!is_null($this->phone))
		{
			$text .= "<div><b>{$translate->_("Phone Number")}</b> {$this->phone}</div>";
		}
    	$text .= "</div>";
    	return $text;
    }

	public function getRichContent($view = false, $params = array()) {
			
		$zend_View = Zend_Registry::get('Zend_View');
	    // $view == false means that this rich content is requested from the activity feed
	    if($view == false){
	    	$content = '<div id="ynresume_cover_wrapper">	
				<div id="cover_photo" class="ynresume-cover-photo">'.Engine_Api::_()->ynresume()->getPhotoSpan($this, 'thumb.main').'</div>
					<div class="ynresume-cover-content">	
						<div class="ynresume-cover-description">
							<div class="ynresume-cover-description-title">'.$this->name.'</div>
							<div class="ynresume-cover-description-position">'.$this->headline.'</div>
							<div class="ynresume-cover-description-subline">';
			if ($this->location) {
				$content .= '<span class="ynresume-cover-location1"><i class="fa fa-map-marker"></i> '.$this -> location.'</span>';    
			}
						
			$industry = $this -> getIndustry();			
			if(!empty($industry)) {
				$content .= '<span><i class="fa fa-folder-open"></i> '.$industry->getTitle().'</span>';
			}				
			$content .=			'</div>		
								<div class="ynresume-cover-description-subline">';			
	
			$tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
			$currentExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), true, 3);
			if(count($currentExperiences) > 0) {
				$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
				$experiences = array();
				foreach ($currentExperiences as $experience){
					$business = null; 
	                if ($experience->business_id) {
	                    $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
	                }
					if ($business && !$business->deleted) {
						$experiences[] = $business -> getTitle();
					}else{
						$experiences[] = $experience -> company;
					}
				}
				$content .= 		'<div class="ynresume-cover-description-info"><i class="fa fa-briefcase"></i><label>'.$zend_View -> translate('Current').'</label><span>'.implode(", ", $experiences).'</span></div>';
			}
	
			$previousExperiences = $tableExperiences -> getExperiencesByResumeId($this -> getIdentity(), false, 3);
			if(count($previousExperiences) > 0) {
				$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
				$experiences_arr = array();
				foreach ($previousExperiences as $experience){
					$business = null; 
                    if ($experience->business_id) {
                        $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
                    }
					if ($business && !$business->deleted) {
						$experiences_arr[] = $business -> getTitle();
					}else{
						$experiences_arr[] = $experience -> company;
					}
				}
				$content .=			'<div class="ynresume-cover-description-info"><i class="fa fa-history"></i><label>'.$zend_View->translate('Previous').'</label><span>'.implode(", ", $experiences_arr).'</span></div>';
			}
	
			$tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
			$educations = $tableEducations -> getEducationsByResumeId($this -> getIdentity(), 3);
			if(count($educations) > 0) {
				$educations_arr = array();
				foreach ($educations as $education){
					$educations_arr[] = $education -> title;
				}
				$content .=			'<div class="ynresume-cover-description-info"><i class="fa fa-graduation-cap"></i><label>'.$zend_View -> translate('Education').'</label><span>'.implode(", ", $educations_arr).'</span></div>';
			}
			$content .=			'</div>
							</div>
						</div>
					</div>';
			return $content;
	    }
  	}

	//HOANGND get feature expiration date
	function getFeatureExpirationDate() {
		if (!$this->feature_expiration_date) return null;
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->feature_expiration_date));
        $date->setTimezone($timezone);
        return $date;
    }
}

