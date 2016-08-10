<?php
class Ynjobposting_Api_Core extends  Core_Api_Abstract {
	const IMAGE_WIDTH = 720;
    const IMAGE_HEIGHT = 720;

    // const FEATURE_WIDTH = 455;
    // const FEATURE_HEIGHT = 312;
    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 105;
    
	public function generateEmailAlert($toSendJobIds, $email)
	{
		$html = "";
		$view = Zend_Registry::get('Zend_View');
		$html =  $view -> partial('_job_alert.tpl', 'ynjobposting', array(
			'toSendJobIds' => $toSendJobIds,
			'email' => $email,
		));
		if(!empty($toSendJobIds))
			return $html;
		else {
			return null;
		}
	}
	
	public function sendJobAlertMail()
	{
		$tableAlert = Engine_Api::_() -> getItemTable('ynjobposting_alert');
		$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$list_emails = $tableAlert -> getEmails();
		foreach ($list_emails as $email_row)
		{
			$rows = $tableAlert -> getRowsByEmail($email_row -> email);
			$list_jobId = array();
			foreach($rows as $record)
			{
				$params['industry_id'] = $record['industry_id'];
				$params['level'] = $record['level_id'];
				$params['type'] = $record['type_id'];
				$params['status'] = 'published';
				$params['salary_from'] = $record['salary'];
				$params['salary_currency'] = $record['currency'];
				$list_jobs = $tableJob -> fetchAll($tableJob -> getJobsSelect($params));
				foreach($list_jobs as $job)
				{
					if(!in_array($job -> getIdentity(), $list_jobId))
					{
						array_push($list_jobId, $job->getIdentity());
					}
				}
			}
			//check which job is already sent
			$tableSentJob = Engine_Api::_() -> getItemTable('ynjobposting_sentjob');
			$listSentJob = $tableSentJob -> getJobIdsByEmail($email_row -> email);
			$toSendJobIds = array_diff($list_jobId, $listSentJob);
			//send mail
			$html = Engine_Api::_() -> ynjobposting() -> generateEmailAlert($toSendJobIds, $email_row -> email);
			$sendTo = $email_row -> email;
		  	$params = array();
			if(!empty($html))
			{
				foreach($toSendJobIds as $toSendJobId)
				{
					$sentJobRow = $tableSentJob -> createRow();
					$sentJobRow -> job_id = $toSendJobId;
					$sentJobRow -> email = $email_row -> email;
					$sentJobRow -> save();
				}
				Engine_Api::_()->getApi('mail','ynjobposting')->send($sendTo, 'ynjobposting_jobalert',$params, $html);
			}
		}
	}
	
	public function notifyFollower($job_id)
	{
		$job = Engine_Api::_() -> getItem('ynjobposting_job', $job_id);
		$company = $job -> getCompany();
		//send notification to follower
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$owner = $job -> getOwner();
		// get follower
		$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
		$list_follow = $tableFollow -> getFollowByCompanyId($job -> company_id);
		foreach($list_follow as $row)
		{
			$person = Engine_Api::_()->getItem('user', $row -> user_id);
			$notifyApi -> addNotification($person, $owner, $job, 'ynjobposting_job_follow');
		}
		
		//send notifications end add activity on feed
 		$viewer = Engine_Api::_()->user()->getViewer();
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$notifyApi -> addNotification($owner, $owner, $job, 'ynjobposting_job_approve');
		//send notice to admin
		$list_admin = Engine_Api::_()->user()->getSuperAdmins();
		foreach($list_admin as $admin)
		{
			$notifyApi -> addNotification($admin, $company, $job, 'ynjobposting_job_create');
		}
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
		$action = $activityApi->addActivity($owner, $job, 'ynjobposting_job_create');
		if($action) {
			$activityApi->attachActivity($action, $job);
		}
	}
	
	public function buyCompany($company_id, $number_sponsor_day = 0)
	{
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$now =  date("Y-m-d H:i:s");
		$sponsorTable =  Engine_Api::_() -> getItemTable('ynjobposting_sponsor');
		$featured = 0;
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $company_id);
		$owner = $company -> getOwner();
		if($number_sponsor_day)
		{
			$featured = 1;
		}
		if($featured)
		{
	     	//active sponsor
	     	$sponsorTable =  Engine_Api::_() -> getItemTable('ynjobposting_sponsor');
			$sponsorRow  = $sponsorTable -> getSponsorRowByCompanyId($company->getIdentity());
			
			if($number_sponsor_day == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($number_sponsor_day." ".$type));
			if(!empty($sponsorRow)) //used to sponsor company
			{
				if($sponsorRow -> active == 1)
				{
					$expiration_date = date_add(date_create($sponsorRow->expiration_date),date_interval_create_from_date_string($number_sponsor_day." ".$type));
				}
				$sponsorRow -> modified_date = $now;
				$sponsorRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$sponsorRow -> period = $sponsorRow -> period + $number_sponsor_day;
				$sponsorRow -> active = 1;
				$sponsorRow -> save();
				$company -> sponsored = 1;
				$company -> save();  
			}
			else //first time
			{
				$sponsorRow = $sponsorTable -> createRow();
				$sponsorRow -> company_id = $company -> getIdentity();
				$sponsorRow -> user_id = $company->user_id;
				$sponsorRow -> creation_date = $now;
				$sponsorRow -> modified_date = $now;
				$sponsorRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$sponsorRow -> active = 1;
				$sponsorRow -> period = $number_sponsor_day;
				$sponsorRow -> save();
				$company -> sponsored = 1;
				$company -> save();
			}
			//add notice
			$notifyApi -> addNotification($owner, $company, $company, 'ynjobposting_company_sponsored');
		}
	}
	
	public function buyJob($job_id, $package_id = 0, $number_feature_day = 0)
	{
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$isExpired = false;
		$now =  date("Y-m-d H:i:s");
		$featured = 0;
		$job = Engine_Api::_() -> getItem('ynjobposting_job', $job_id);
		$owner = $job -> getOwner();
		if($number_feature_day)
		{
			$featured = 1;
		}
		if($featured)
		{
			$autoapproved = 0;
			if (Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'autoapprove') -> checkRequire())
			{
				$autoapproved = 1;
			}
	     	//active feature
	     	$featureTable =  Engine_Api::_() -> getItemTable('ynjobposting_feature');
			$featureRow  = $featureTable -> getFeatureRowByJobId($job->getIdentity());
			
			if($number_feature_day == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($number_feature_day." ".$type));
			if(!empty($featureRow)) //used to feature job
			{
				if($featureRow -> active == 1)
				{
					$expiration_date = date_add(date_create($featureRow->expiration_date),date_interval_create_from_date_string($number_feature_day." ".$type));
					$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
					$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_featured');
				}
				$featureRow -> modified_date = $now;
				if($autoapproved)
				{
					if($featureRow -> active == 0)
					{
						//just for expiration_date is null
						$total_number_day = $featureRow->period + $number_feature_day;
						$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($total_number_day." ".'days'));
						$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
						$featureRow -> active = 1;
						$job -> featured = 1;
						$job -> save();
						$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_featured');
					}
					//set expiration_date for job if user do not buy feature 
					if(!$package_id)
					{
						// already publish at least one time
						if($job -> status == 'pending')
						{
							$status = 'published';
							$job -> approved_date = $now;
							$total_number_day = $job->number_day;
							if($total_number_day == 1)
							{
								$type = 'day';
							}
							else 
							{
								$type = 'days';
							}
							$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($total_number_day." ".'days'));
							$job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
							$job -> save();
							 //add notice
	    					Engine_Api::_() -> ynjobposting() -> notifyFollower($job -> getIdentity());
						}
					}
				}
				$featureRow -> period = $featureRow -> period + $number_feature_day;
				$featureRow -> save();  
			}
			else //first time
			{
				$featureRow = $featureTable -> createRow();
				$featureRow -> job_id = $job -> getIdentity();
				$featureRow -> user_id = $job->user_id;
				$featureRow -> creation_date = $now;
				$featureRow -> modified_date = $now;
				if($autoapproved)
				{
					$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
					$featureRow -> active = 1;
					$job -> featured = 1;
					$job -> save();
					$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_featured');
				}	
				$featureRow -> period = $number_feature_day;
				$featureRow -> save();  
			}
		}
		//add days
		if($package_id)
		{
			$package = Engine_Api ::_() -> getItem('ynjobposting_package', $package_id);
			if($package->valid_amount == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$status = 'pending';
			if($job -> status == 'published')
			{
				$status = 'published';
			}
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($package->valid_amount." ".$type));
			//renew job
			if($job -> status != 'draft' && $job -> status != 'pending')
			{
				$job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				if($job -> status == 'expired')
				{
					$status = 'published';
					$isExpired = true;
				}
			}
			// job is already published and now add more
			if($job -> status == 'published' || $job -> status == 'ended')
			{
				$expiration_date = date_add(date_create($job->expiration_date),date_interval_create_from_date_string($package->valid_amount." ".$type));
				$job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			}
			//allow auto approved
			if (Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynjobposting_job', null, 'autoapprove') -> checkRequire())
			{
				//when job is draft(first time published)
				if($job -> status == 'draft')
				{
					$status = 'published';
					$job -> approved_date = $now;
					$job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				}
				// already publish at least one time
				elseif($job -> status == 'pending')
				{
					$status = 'published';
					$job -> approved_date = $now;
					$total_number_day = $job->number_day + $package -> valid_amount;
					$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($total_number_day." ".'days'));
					$job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				}
				//set expiration_date for feature if user do not buy feature 
				if(!$featured)
				{
					$featureTable =  Engine_Api::_() -> getItemTable('ynjobposting_feature');
					$featureRow  = $featureTable -> getFeatureRowByJobId($job->getIdentity());
					if($featureRow)
					{
						//just for expiration_date is null
						if($featureRow -> active == 0)
						{
							if($featureRow->period == 1)
							{
								$type = 'day';
							}
							else 
							{
								$type = 'days';
							}
							$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($featureRow->period." ".$type));
							$featureRow -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");;
							$featureRow -> active = 1;
							$featureRow -> save();
							$job -> featured = 1;
							$job -> save();
							$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_featured');
						}
					}
				}
			}
			$job -> number_day = $job -> number_day + $package -> valid_amount;
			$job -> status = $status;
			$job -> save();  
			if($status == 'published' && !$isExpired)
			{
				//add notice
    			Engine_Api::_() -> ynjobposting() -> notifyFollower($job -> getIdentity());
			}
		}
	}
	
	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}

	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			if (in_array($gateway -> title, array(
				'Authorize.Net',
				'iTransact'
			)))
			{
				$class = str_replace('Ynpayment', 'Ynjobposting', $gateway -> plugin);
			}
			else
			{
				$class = str_replace('Payment', 'Ynjobposting', $gateway -> plugin);
			}

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}
	
	public function checkYouNetPlugin($name) {
		$table = Engine_Api::_ ()->getDbTable ( 'modules', 'core' );
		$select = $table->select ()->where ( 'name = ?', $name )->where ( 'enabled  = 1' );
		$result = $table->fetchRow ( $select );
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function typeCreate($label) {
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynjobposting_company');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynjobposting_company', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynjobposting_company');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynjobposting_company');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynjobposting_company');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
    
    public function createPhoto($photo) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
        }
        else if( is_array($photo) && !empty($photo['tmp_name']) ) {
            $file = $photo['tmp_name'];
        } 
        else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        }
        else {
            throw new Ynjobposting_Model_Exception('invalid argument passed to setPhoto');
        }

        $name = basename($file);
        
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'user',
            'parent_id' => $viewer->getIdentity(),
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(720, 720)
            ->write($path.'/m_'.$name)
            ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(200, 400)
            ->write($path.'/p_'.$name)
            ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(140, 160)
            ->write($path.'/in_'.$name)
            ->destroy();

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
        $iMain = $storage->create($path.'/m_'.$name, $params);
        $iProfile = $storage->create($path.'/p_'.$name, $params);
        $iIconNormal = $storage->create($path.'/in_'.$name, $params);
        $iSquare = $storage->create($path.'/is_'.$name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path.'/p_'.$name);
        @unlink($path.'/m_'.$name);
        @unlink($path.'/in_'.$name);
        @unlink($path.'/is_'.$name);

        return $iMain->file_id;
    }

    public function getPhoto($id) {
        if ($id) {
            $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($id, 'thumb.profile');
            if( !$photo ) {
                return 'application/modules/Ynjobposting/externals/images/no_image.png';
            }
            return $photo->map();
        }
        return 'application/modules/Ynjobposting/externals/images/no_image.png';
    }
    
    public function getMyVideos() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_() -> hasItemType('video')) {
            return array();
        }
        $table = Engine_Api::_()->getItemTable('video');
        $select = $table->select()->where('owner_type = ?', 'user')->where('owner_id = ?', $viewer->getIdentity());
        $rows = $table->fetchAll($select);
        $videos = array();
        foreach ($rows as $row) {
            $videos[$row->video_id] = $row->title;
        }
        return $videos;
    }
    
    public function createFile($file) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $params = array(
            'parent_type' => 'user',
            'parent_id' => $viewer->getIdentity(),
        );
        // Save
        $storage = Engine_Api::_() -> storage();
        // Store
        $aMain = $storage -> create($file, $params);
        // Remove temp files
        return $aMain -> file_id;
    }
    
    public function convertToUserTimezone($datetime) {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($datetime));
        $date->setTimezone($timezone);
        return $date;
    }

    function getPhotoSpan($item, $type = null)
  	{
  		if (!is_null($type))
  		{
  			$photoUrl = $item->getPhotoUrl($type);
  		}
  		else
  		{
  			$photoUrl = $item->getPhotoUrl();
			if (!$photoUrl)
			{
				$photoUrl = $item->getPhotoUrl('thumb.profile');
			}
  		}

  		// set default photo
  		if (!$photoUrl)
		{
			$view = Zend_Registry::get("Zend_View");
			$photoUrl = $view->baseUrl().'/application/modules/Ynjobposting/externals/images/default_company.png';
		}
  		return '<a href = "'.$item -> getHref().'" title = "'.$item -> getTitle().'"><span class="ynjobposting-item-photo-cover" style="background-image:url('.$photoUrl.');"></span></a>';
  	}
    
    public function getFromDaySearch($day) {
        $day = $day . " 00:00:00";
        $user_tz = date_default_timezone_get();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer -> getIdentity())
        {
            $user_tz = $viewer -> timezone;

        }
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($user_tz);
        $start = strtotime($day);
        date_default_timezone_set($oldTz);
        $fromdate = date('Y-m-d H:i:s', $start);
        return $fromdate;
    }
    
    function getCurrentHost() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);
        // use port if non default
        $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';
        $path = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'default');
        $path = str_replace("index.php/", "", $path);
        $currentHostSite = $protocol . $parts['host'] . $port;
        return $currentHostSite;
    }
}
