<?php

class Ynjobposting_Model_Company extends Core_Model_Item_Abstract {

	protected $_type = 'ynjobposting_company';
    
    public function getTitle() {
        return $this->name;
    }
    /*----- Get Company description Function ----*/
	public function getDescription() 
	{
		$view = Zend_Registry::get('Zend_View');
		$tmp_description = strip_tags($this -> description);
		$description = $view -> string() -> truncate($tmp_description, 150);
		return $description;
	}
	
	public function countFollower()
	{
		$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
		$select = $tableFollow -> select() -> where('company_id = ?', $this -> getIdentity()) -> where('active = 1');
		return count($tableFollow -> fetchAll($select));
	}
	
	public function countJobWithStatus($status)
	{
		$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$select = $tableJob -> select() -> where('company_id = ?', $this -> getIdentity())->where('status =?', $status);
		return count($tableJob->fetchAll($select));
	}
	
	public function checkSponsor()
	{
		$tableIndustryMap = Engine_Api::_()->getDbTable('sponsors', 'ynjobposting');
		$row = $tableIndustryMap -> getSponsorRowByCompanyId($this->getIdentity());
		if($row -> active)
			return true;
		else 
			return false;
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
			throw new Ynjobposting_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynjobposting_company',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$exif = exif_read_data($file);
		$angle = 0;
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
		$image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (feature)
		$image = new Ynjobposting_Api_Image();
		@$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(350, 200) -> write($path . '/fe_' . $name) -> destroy();

		// Resize image (normal)
		$image = new Ynjobposting_Api_Image();
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
		$iFeature = $storage -> create($path . '/fe_' . $name, $params);
		$iSquare = $storage->create($path.'/is_'.$name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iSquare, 'thumb.icon');
		$iMain -> bridge($iFeature, 'thumb.feature');
		
		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/fe_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}
	
	public function setCoverPhoto($photo)
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
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'ynjobposting_company'
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if (function_exists('exif_read_data')) 
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
		$image -> open($file) ;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(1200, 1200) -> write($path . '/m_' . $name) -> destroy();

		$iMain = $storage -> create($path . '/m_' . $name, $params);
		
		// Remove temp files
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> cover_photo = $iMain -> file_id;
		
		$this -> save();

		return $this;
	}
    
    public function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit');
    }

    public function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete');
    }
    
    public function isClosable() {
        return $this->authorization()->isAllowed(null, 'close');
    }
    
    public function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }
    
    public function isCommentable() {
        return $this->authorization()->isAllowed(null, 'comment');
    }
    
    public function isSponsorable() {
        return $this->authorization()->isAllowed(null, 'sponsor');
    }
    
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }
    
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }
    
    public function getWebsite(){
    	if (trim($this->website) == '')
    	{
    		return '';
    	}
    	$url = $this->website;
    	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
	        $url = "http://" . $url;
	    }
    	return $url;
    }
    
    public function getJobs($status = false)
    {
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$select = $jobTbl -> select() -> where("company_id = ?", $this->getIdentity())->where('status <> ?', 'deleted');
    	if ($status == true){
    		$select->where("status = ?", 'published');
    	}
    	return $jobTbl -> fetchAll($select);
    }
    
    public function getIndustries()
    {
    	$mapTbl = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
    	$industrieMaps = $mapTbl -> getIndustriesByCompanyId($this->getIdentity());
    	$industryIds = array();
    	foreach($industrieMaps as $map){
    		$industryIds[] = $map -> industry_id;
    	}
    	$industryTbl = Engine_Api::_()->getItemTable('ynjobposting_industry');
    	$select = $industryTbl -> select() -> where("industry_id IN (?)", $industryIds);
    	return $industryTbl -> fetchAll($select);
    }
    
	public function getJobsWithStatus($status = 'published')
    {
    	$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	$select = $jobTbl -> select() -> where("company_id = ?", $this->getIdentity())->where('status <> ?', 'deleted');
    	if (isset($status) && 
    	in_array($status, array('draft', 'pending', 'denied', 'published', 'ended', 'expired')))
    	{
    		$select->where("status = ?", $status);
    	}
    	return $jobTbl -> fetchAll($select);
    }
    
	public function getJobIds($status = 'published')
    {
    	$jobs = $this -> getJobsWithStatus($status);
    	$ids = array();
    	foreach ($jobs as $job) {
    		$ids[] = $job -> getIdentity();
    	}
    	return $ids;
    }
    
    public function getHref($params = array())
    {
    	$params = array_merge(array(
			'route' => 'ynjobposting_extended',
    		'controller' => 'company',
   			'reset' => true,	
			'action' => 'detail',
		    'id' => $this->getIdentity(),
    	), $params);
    	$route = $params['route'];
    	$reset = $params['reset'];
    	unset($params['route']);
    	unset($params['reset']);
    	return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }
    
    public function getSize()
    {
    	$str = "";
    	if ($this -> from_employee)
    	{
    		$str .= $this -> from_employee;
    	}
    	if ($this -> to_employee)
    	{
    		$str .= " - ";
    		$str .= $this -> to_employee;
    	}
    	$str .= " " . Zend_Registry::get("Zend_Translate")->_("employees");
    	return $str;
    }
    
    public function followerCount()
    {
    	$followTbl = Engine_Api::_()->getItemTable('ynjobposting_follow');
    	$select = $followTbl -> select() 
    	-> from ($followTbl -> info('name'), new Zend_Db_Expr('COUNT(*) AS count'))
    	-> where ('active = 1');				
    	return $select->query()->fetchColumn(0);
    }
    
    public function getIndustry($breadCrumChar = " | ", $seperateChar = "/")
    {
    	$view = Zend_Registry::get("Zend_View");
    	$industries = $this -> getIndustries();
    	
    	$arr = array();
		foreach($industries as $industry)
		{
			$str = '';
			$i = 0;
			if($industry) 
			{
				foreach($industry->getBreadCrumNode() as $node)
				{
					if($node -> industry_id != 1) 
					{
						if($i != 0) 
						{
							$str .= $seperateChar;	
						}
		        		$i++; 
		        		$str .=  $view->htmlLink($node->getHref(), $view->translate($node->shortTitle()), array());
		        	}
				}
	         	if($industry -> parent_id != 0 && $industry -> parent_id  != 1) 
	         	{
					$str .= $seperateChar;
				}
	         	$str .= $view->htmlLink($industry->getHref(), $industry->title);
	        }
	        $arr[] = $str;
		}
		return implode($breadCrumChar, $arr);
    }

	public function getSubmissionForm() {
        $table = Engine_Api::_()->getDbTable('submissions', 'ynjobposting');
        $select = $table->select()->where('company_id = ?', $this->getIdentity());
        $row = $table->fetchRow($select);
        return $row;
    }
    
    public function getSubmissionQuestionFields() {
        $fieldMetaTbl = new Ynjobposting_Model_DbTable_Meta();
        $questionFields = $fieldMetaTbl -> getFields($this);
        return $questionFields;
    }
    
    public function delete() {
        //delete actions and attachments
        $streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
        $streamTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynjobposting_company")');
        $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
        $activityTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynjobposting_company")');
        $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
        $attachmentTbl->delete('(`id` = '.$this -> getIdentity().' AND `type` = "ynjobposting_company")');
    }
    
    public function addDefaultSubmissionForm()
    {
    	$fieldMetaTbl= new Ynjobposting_Model_DbTable_Meta();
	  	$customFields = $fieldMetaTbl -> getFields($this);
	  	$values = Array ( 
			'form_title' => Zend_Registry::get("Zend_Translate") -> _("YNJOBPOSTING_SUBMISSION_FORM_TITLE"),
			'form_description' =>  Zend_Registry::get("Zend_Translate") -> _("YNJOBPOSTING_SUBMISSION_FORM_DESCRIPTION"),
			'show_company_logo' => 1,
			'show_job_title' => 1,
			'allow_video' => 1,
			'company_id' => $this -> getIdentity(),
			'user_id' => $this -> user_id,
		);
		$submissionTbl = Engine_Api::_()->getItemTable('ynjobposting_submission');
		$submission = $submissionTbl -> createRow();
		$submission -> setFromArray($values);
        $submission -> save();
    }
}
