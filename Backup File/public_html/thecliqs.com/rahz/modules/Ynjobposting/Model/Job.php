<?php

class Ynjobposting_Model_Job extends Core_Model_Item_Abstract {
    
    protected $_type = 'ynjobposting_job';
    protected $_parent_type = 'ynjobposting_company';
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getCompany() {
        return Engine_Api::_()->getItem('ynjobposting_company', $this->company_id);
    }
    
    public function getIndustry() {
        return Engine_Api::_()->getItem('ynjobposting_industry', $this->industry_id);
    }
	
    /*----- Get Job Description Function ----*/
	public function getDescription() 
	{
		$view = Zend_Registry::get('Zend_View');
		$tmp_description = strip_tags($this -> description);
		$description = $view -> string() -> truncate($tmp_description, 150);
		return $description;
	}
    public function isFeatured() {
        $table = Engine_Api::_()->getDbTable('features', 'ynjobposting');
        $select = $table->select()
            ->where('job_id = ?', $this->getIdentity())
            ->where('active = ?', 1);
        $feature = $table->fetchRow($select);
        if ($feature) return true; else return false;
    }
    
    public function isFeaturable() {
        $feature = $this->getFeature();
        if (!$feature) return false;
        $now = new Zend_Date();
        if (is_null($feature->expiration_date)) {
            if ($this->status == 'pending') {
                return false;
            }
            else {
                return true;
            }
        }
        $expiration_date = new Zend_Date($feature->expiration_date);
        return ($expiration_date >= $now) ? true : false;
    }
    
    public function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit');
    }
    
    public function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete');
    }
    
    public function isEndable() {
        return $this->authorization()->isAllowed(null, 'end');
    }
    
    public function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }
    
    public function isCommentable() {
        return $this->authorization()->isAllowed(null, 'comment');
    }
    
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }
    
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }
    
    public function delete() {
        //delete actions and attachments
        $streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
        $streamTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynjobposting_job")');
        $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
        $activityTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynjobposting_job")');
        $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
        $attachmentTbl->delete('(`id` = '.$this -> getIdentity().' AND `type` = "ynjobposting_job")');
        $this->changeStatus('deleted');
        $this->feature(0, false);
    }
    
    public function changeStatus($status) {
       $db = Engine_Db_Table::getDefaultAdapter();
       $db->beginTransaction();
       try {
           $this->status = $status;
           $this->save();
           $db->commit();
       }
       catch (Exception $e) {
           $db->rollBack();
           throw $e;
       }
    }
    
    public function tags() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
    }
    
    public function getPhotos() {
        $photos = array();
        $photo_ids = $this->photo_ids;
        foreach ($photo_ids as $photo_id) {
            $photo = array(
                'id' => $photo_id,
                'url' => Engine_Api::_()->ynjobposting()->getPhoto($photo_id),
                'isDefault' => ($photo_id == $this->default_photo) ? 1 : 0
            );
            array_push($photos, $photo);
        }
        return $photos;
    }

    public function getFeature() {
        $table = Engine_Api::_()->getDbTable('features', 'ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity());
        return $table->fetchRow($select);
    }
    
    public function getInfo()
    {
    	$jobinfoTbl = Engine_Api::_()->getDbTable('jobinfos', 'ynjobposting');
    	$select  = $jobinfoTbl -> select() -> where ("job_id = ?", $this->getIdentity());
    	return $jobinfoTbl -> fetchAll($select);
    }
    
    public function getPhotoUrl($type = null)
    {
    	$company  = $this -> getCompany();
        if ($company)
    	   return $company -> getPhotoUrl($type);
        else return null;
    }
    
    public function isOwner(Core_Model_Item_Abstract $owner = null) {
        if ($owner == null)
            $owner = Engine_Api::_()->user()->getViewer();
        return ($this->user_id == $owner->getIdentity()) ? true : false;
    }
	public function getSalary()
    {
    	$view = Zend_Registry::get('Zend_View');
    	$translate = Zend_Registry::get("Zend_Translate");
    	if (is_null($this->salary_from) && is_null($this->salary_to))
    	{
    		return Zend_Registry::get("Zend_Translate")->_("negotiable"); 
    	}
    	else 
    	{
    		if (!is_null($this->salary_from) && is_null($this->salary_to))
    		{
    			return Zend_Registry::get("Zend_Translate")->_("From") . $view -> locale() -> toCurrency($this->salary_from, $this->salary_currency);
    		}
	    	if (is_null($this->salary_from) && !is_null($this->salary_to))
    		{
    			return Zend_Registry::get("Zend_Translate")->_("To") . $view -> locale() -> toCurrency($this->salary_to, $this->salary_currency);
    		}
    		if (!is_null($this->salary_from) && !is_null($this->salary_to))
    		{
    			return $view -> locale() -> toCurrency($this->salary_from, $this->salary_currency) . ' - ' . $view -> locale() -> toCurrency($this->salary_to, $this->salary_currency);
    		}
    	}
    }

	public function getSubmissionForm() {
        $company = $this->getCompany();
        if ($company) {
            return $company->getSubmissionForm();
        }
        else {
            return null;
        }
    }
    
    public function hasApplied($user_id = null) {
        if ($user_id == null) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }
        $table = Engine_Api::_()->getDbTable('jobapplies','ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity())->where('user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
    public function hasSaved($user_id = null) {
        if ($user_id == null) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }
        $table = Engine_Api::_()->getDbTable('savejobs','ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity())->where('user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        return ($row) ? true : false;
    }
    
    public function isDraft() {
        return ($this->status == 'draft') ? true : false;
    }
    
    public function isPublished() {
        return ($this->status == 'published') ? true : false;
    }
    
    public function isExpired() {
        return ($this->status == 'expired') ? true : false;
    }
    
    public function isEnded() {
        return ($this->status == 'ended') ? true : false;
    }

    public function isDeleted() {
        return ($this->status == 'deleted') ? true : false;
    }
    
    public function removeSaveJob($user_id = null) {
        if ($user_id == null) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $user_id = $viewer->getIdentity();
        }
        $table = Engine_Api::_()->getDbTable('savejobs','ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity())->where('user_id = ?', $user_id);
        $row = $table->fetchRow($select);
        if ($row) {
            $row->delete();
        }
    }
    
    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynjobposting_job',
            'controller' => 'jobs',
            'reset' => true,    
            'action' => 'view',
            'id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
    }
    
    public function getLevel() {
    	$view = Zend_Registry::get('Zend_View');
		$tableLevel = Engine_Api::_() -> getDbTable('joblevels', 'ynjobposting');
        $levels = $tableLevel -> getJobLevelArray();
		if(!empty($levels[$this->level]))
		{
			return $view -> translate($levels[$this->level]);
		}
		else {
			return $view -> translate('Unknown');
		}
        
    }
    
    public function getJobType() {
    	$view = Zend_Registry::get('Zend_View');
        $tableType = Engine_Api::_() -> getDbTable('jobtypes', 'ynjobposting');
        $types = $tableType -> getJobTypeArray();	
		if(!empty($types[$this->type]))
		{
			return $view -> translate($types[$this->type]);
		}
		else {
			return $view -> translate('Unknown');
		}
    }
    
    public function feature($value = 1, $noti = false) {
        $table = Engine_Api::_()->getDbTable('features', 'ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity());
        $feature = $table->fetchRow($select);
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if ($feature) $feature->delete();
            if ($value) {
                $feature = $table->createRow();
                $feature->job_id = $this->getIdentity();
                $feature->active = 1;
                $feature->save();
                if ($noti) {
                    $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                    $notifyApi -> addNotification($this->getOwner(), $this, $this, 'ynjobposting_job_featured');
                }
            }
            else if ($noti) {
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                $notifyApi -> addNotification($this->getOwner(), $this, $this, 'ynjobposting_job_unfeatured');
            }
            $this->featured = $value;
            $this->save();
            $db->commit();    
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function getAllSaved() {
        $table = Engine_Api::_()->getDbTable('savejobs', 'ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity());
        return $table->fetchAll($select);
    }
    
    public function getAllApplied() {
        $table = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
        $select = $table->select()->where('job_id = ?', $this->getIdentity());
        return $table->fetchAll($select);
    }
}
