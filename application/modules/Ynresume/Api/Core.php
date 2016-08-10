<?php
class Ynresume_Api_Core extends  Core_Api_Abstract {
    
    protected $_sections = array(
        'photo' => 'Photo',
        'general_info' => 'General Information',
        'summary' => 'Summary',
        'experience' => 'Experience',
        'education' => 'Education',
        'certification' => 'Certifications',
        'language' => 'Language',
        'skill' => 'Skills',
        'publication' => 'Publications',
        'recommendation' => 'Recommendations',
        'project' => 'Projects',
        'honor_award' => 'Honors & Awards',
        'course' => 'Courses',
        'contact' => 'Contact Information'
    );
    
    protected $_sectionsAddBtn = array(
        'photo' => 'Add Photo',
        'summary' => 'Add Summary',
        'experience' => 'Add Position',
        'education' => 'Add School',
        'certification' => 'Add Certifications',
        'language' => 'Add Language',
        'skill' => 'Add Skills',
        'publication' => 'Add Publications',
        'project' => 'Add Projects',
        'honor_award' => 'Add Honors & Awards',
        'course' => 'Add Courses',
        'contact' => 'Add Contact Information',
    );
    
    protected $_sectionsIconClass = array(
        'photo' => 'fa fa-picture-o',
        'summary' => 'fa fa-life-ring',
        'experience' => 'fa fa-briefcase',
        'education' => 'fa fa-graduation-cap',
        'certification' => 'fa fa-certificate',
        'language' => 'fa fa-language',
        'skill' => 'fa fa-cogs',
        'publication' => 'fa fa-newspaper-o',
        'project' => 'fa fa-clipboard',
        'honor_award' => 'fa fa-flag-checkered',
        'course' => 'fa fa-book',
        'contact' => 'fa fa-info-circle',
        'recommendation' => 'fa fa-thumb-tack'
    );
    
    protected $_relationships = array(
        'report','manage','senior_by','senior_to','same_group','diff_group','diff_company','be_client','client','teacher','mentor','together'
    );
    
	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}
	
    function isMobile()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent))
            {
                return true;
            }
            return false;
         }
         else
         {
            return false;
         }
    }
    
	public function featureResume($resume_id, $feature_day_number)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $resume_id);
		if($resume)
		{
			if($feature_day_number == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($feature_day_number." ".$type));
			if($resume -> featured) //used to feature resume
			{
				$expiration_date = date_add(date_create($resume->feature_expiration_date),date_interval_create_from_date_string($feature_day_number." ".$type));
				$resume -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			}
			else //first time or renew
			{
				$resume -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$resume -> featured = true;
			}
			$resume -> modified_date = $now;
			$resume -> save();
		}
	}
	
	public function serviceResume($resume_id, $service_day_number)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$resume = Engine_Api::_() -> getItem('ynresume_resume', $resume_id);
		if($resume)
		{
			if($service_day_number == 1)
			{
				$type = 'day';
			}
			else 
			{
				$type = 'days';
			}
			$now =  date("Y-m-d H:i:s");
			$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($service_day_number." ".$type));
			if($resume -> serviced) //used to service resume
			{
				$expiration_date = date_add(date_create($resume->service_expiration_date),date_interval_create_from_date_string($service_day_number." ".$type));
				$resume -> service_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
			}
			else //first time or renew
			{
				$resume -> service_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
				$resume -> serviced = true;
			}
			$resume -> modified_date = $now;
			$resume -> save();
		}
	}
	
	public function getPhoto($photo_id, $type)
	{
		$storage_fileTbl = Engine_Api::_() -> getItemTable('storage_file');
		$select = $storage_fileTbl -> select() 
								   -> where('parent_file_id = ?', $photo_id)
								   -> where('type = ?', $type)
								   -> limit(1);
		return $storage_fileTbl -> fetchRow($select) -> storage_path;
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
				$class = str_replace('Ynpayment', 'Ynresume', $gateway -> plugin);
			}
			else
			{
				$class = str_replace('Payment', 'Ynresume', $gateway -> plugin);
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
	
	public function typeCreate($label) {
		
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynresume_resume');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynresume_resume', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynresume_resume');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynresume_resume');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynresume_resume');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
    
    public function getThemeIconLink($theme) {
        return 'application/modules/Ynresume/externals/images/'.$theme.'.png';
    }
    
    public function getAllSections() {
        return $this->_sections;    
    }
    
    public function getAllSectionsAndGroups() {
        $result = $this->_sections;
        $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
		$resume = new Ynresume_Model_Resume(array());        
        foreach ($headings as $heading) {
        	$type = Engine_Api::_()->getApi('fields','ynresume')->getFieldTypeStr($resume);
        	if(Engine_Api::_()->getApi('fields','ynresume')->checkHasQuestion($type, $heading -> field_id, 1, 1))
            	$result['field_'.$heading->field_id] = 'Add '.$heading->label;
        }
        return $result;     
    }
    
    public function getSectionLabel($key) {
        $view = Zend_Registry::get('Zend_View');
        $sections = $this->_sections;
        if (isset($sections[$key])) {
            return $view->translate($sections[$key]);
        }
        if (strpos($key, 'field_') !== FALSE) {
            $arr = explode('_', $key);
            $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
            foreach($headings as $heading) {
                if ($arr[1] == $heading->field_id) {
                    return $view->translate($heading->label);
                }
            }
            
        }
    }
    
    public function getSectionsAddBtn() {
        return $this->_sectionsAddBtn;    
    }
    
    public function getSectionsIconClass() {
        return $this->_sectionsIconClass;    
    }
    
    public function getSectionIconClass($key) {
        $class = $this->_sectionsIconClass;
        if (isset($class[$key])) {
            return $class[$key];
        }
        if (strpos($key, 'field_') !== FALSE) {
            $arr = explode('_', $key);
            $headings = Engine_Api::_()->getApi('fields', 'ynresume')->getHeading();
            foreach($headings as $heading) {
                if ($arr[1] == $heading->field_id) {
                    return $view->translate($heading->label);
                }
            }
            
        }
    }
    
    public function getUserResume($user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        if (!$user_id) return false;
        return Engine_Api::_()->getItemTable('ynresume_resume')->getResume($user_id);
    }

    function getPhotoSpan($item, $type = null) {
        if (!is_null($type)) {
            $photoUrl = $item->getPhotoUrl($type);
        }
        else {
            $photoUrl = $item->getPhotoUrl();
            if (!$photoUrl) {
                $photoUrl = $item->getPhotoUrl('thumb.main');
            }
        }

        // set default photo
        if (!$photoUrl) {
            $view = Zend_Registry::get("Zend_View");
            $photoUrl = $view->baseUrl().'/application/modules/Ynresume/externals/images/nophoto_resume_thumb_profile.png';
		}

        return '<a href = "'.$item -> getHref().'" title = "'.$item -> getTitle().'"><span class="ynresume-photo-span" style="background-image:url('.$photoUrl.');"></span></a>';
    }
    
    function renderSection($section, $resume, $params = array()) {
        $view = Zend_Registry::get('Zend_View');
        $sections = $this->_sections;
        if (isset($sections['general_info'])) unset($sections['general_info']);
        if (array_key_exists($section, $sections)) {
            if (isset($params['save']) && $params['save']) {
                if (isset($params['item_id']) && $params['item_id']) {
                    $resume->editSection($section, $params);
                }
                else {
                    $resume->addSection($section, $params);
                }
            }
            
            if (isset($params['remove']) && $params['remove']) {
                $resume->removeSection($section, $params);
            }
            
            $view->section = $section;
            $view->resume = $resume;
            $view->params = $params;
            return $view -> render('_section_'.$section.'.tpl');
        }
        else if (strpos($section, 'field_') !== FALSE){
            $arr = explode('_', $section);
            $heading = Engine_Api::_()->getApi('fields', 'ynresume')->getHeadingById($arr[1]);
            $view->heading = $heading;
            $view->resume = $resume;
            $view->params = $params;
            return $view -> render('_custom_group.tpl');
        }
        else return '';
    }

    function getOccupations($user_id = null) {
        $resume = $this->getResumeByUserId($user_id);
        if ($resume) {
            return $resume->getOccupations();
        }
        else {
            return array();
        }
    }
    
    function getAvailableOccupations($receiver_id, $giver_id) {
        $resume = $this->getResumeByUserId($receiver_id);
        if ($resume) {
            return $resume->getAvailableOccupations($giver_id);
        }
        else {
            return array();
        }
    }
    
    function getResumeByUserId($user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        return Engine_Api::_()->getItemTable('ynresume_resume')->getResume($user_id);
    }
    
    function hasRecommended($occupation, $receiver, $giver) {
        $user = Engine_Api::_()->user()->getUser($giver->getIdentity());
        $level_id = 5;
        if ($user->getIdentity()) $level_id = $giver->level_id;
        $can_recommend = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', $level_id, 'recommend')->checkRequire();
        if (!$can_recommend) {
            return array(
                'status' => true,
                'message' => 'can_not_recommend'
            );
            
        }
        $arr = explode('-', $occupation);
        $type = $arr[0];
        $id = intval($arr[1]);
        $table = Engine_Api::_()->getDbTable('recommendations', 'ynresume');
        $select = $table->select()
            ->where('receiver_position_type = ?', $type)
            ->where('receiver_position_id = ?', $id)
            ->where('receiver_id = ?', $receiver->getIdentity())
            ->where('giver_id = ?', $giver->getIdentity());
        $result = $table->fetchRow($select);
        $return = array();
        $return['status'] = ($result) ? true : false;
        if ($return['status']) {
            $return['message'] = ($result->status == "ask") ? 'already_ask' : 'already_recommend';
        }
        return $return;
    }
    
    public function getRelationships() {
        return $this->_relationships;    
    }
    
    public function getRecommendationsOfOccupation($type, $id, $user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        return Engine_Api::_()->getDbTable('recommendations', 'ynresume')->getRecommendationsOfOccupation($type, $id, $user_id);
    }
    
    public function getShowRecommendationsOfOccupation($type, $id, $user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        return Engine_Api::_()->getDbTable('recommendations', 'ynresume')->getShowRecommendationsOfOccupation($type, $id, $user_id);
    }
    
    public function countRecommendations($recommendations) {
        $result = array(
            'show' => array(),
            'hide' => array()
        );
        foreach ($recommendations as $recommendation) {
            if ($recommendation->show) {
                $result['show'][] = $recommendation;
            }
            else {
                $result['hide'][] = $recommendation;
            }
        }
        
        return $result;
    }
    
    public function getHref($user = null) {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        $resume = $this->getResumeByUserId($user->getIdentity());
        return ($resume) ? $resume->getHref() : $user->getHref();
    }
    
    public function getPosition($type, $id) {
        $view = Zend_Registry::get('Zend_View');
        $item = Engine_Api::_()->getItem('ynresume_'.$type, $id);
        if (!$item) return null;
        if ($type == 'experience') {
            $business = ($business_enable && $item->business_id) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
            $company = ($business) ? $business->getTitle() : $item->company;
            return $item->title.' '.$view->translate('at').' '.$company;
        }
        else {
            return $view->translate('Student').' '.$view->translate('at').' '.$item->title;
        }    
    }
    
    public function getPosition2($type, $id) {
    	$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
        $view = Zend_Registry::get('Zend_View');
        $item = Engine_Api::_()->getItem('ynresume_'.$type, $id);
        if (!$item) return null;
        if ($type == 'experience') {
            $business = ($business_enable && $item->business_id) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
            $company = ($business) ? $business->getTitle() : $item->company;
            return array($item->title, $company);
        }
        else {
            return array($view->translate('Student'), $item->title);
        }    
    }
    
    public function getPlace($type, $id) {
    	$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
        $view = Zend_Registry::get('Zend_View');
        $item = Engine_Api::_()->getItem('ynresume_'.$type, $id);
        if (!$item) return null;
        if ($type == 'experience') {
            $business = ($business_enable && $item->business_id) ? Engine_Api::_()->getItem('ynbusinesspages_business', $item->business_id) : null;
            $company = ($business) ? $business->getTitle() : $item->company;
            return $company;
        }
        else {
            return $item->title;
        }    
    }
    
    public function setPhoto($photo, $params) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else
        if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $name = basename($file);
        }
        else {
            throw new Ynfeedback_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
        }

        
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        if (empty($params)) {
            $params = array(
                'parent_type' => 'user',
                'parent_id' => Engine_Api::_()->user()->getViewer() -> getIdentity()
            );
        }
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
        $image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

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
        // Update row
        return $iMain -> getIdentity();
    }

    public function deletePhoto($photo_id) {
        $photo = Engine_Api::_()->getItem('ynresume_photo', $photo_id);
        if (!$photo) return false;
        if ($photo->parent_type == 'ynresume_resume') {
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
            if ($file) $file->remove();
            $photo->delete();
            return true;
        }
        return false; 
    }
    
	public function getFullUrl($url)
	{
		$host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
		$proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? 'https' : 'http';
		$port = (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);
		$uri = $proto . '://' . $host;
		if ((('http' == $proto) && (80 != $port)) || (('https' == $proto) && (443 != $port)))
		{
			$uri .= ':' . $port;
		}
		$url = $uri . '/' . ltrim($url, '/');

		return $url;
	}
    
    //HOANGND get default theme color
    public function getDefaultThemeColor($theme) {
        $array = array(
            'theme_1' => '#619dbe',
            'theme_2' => '#619dbe',
            'theme_3' => '#e95959',
            'theme_4' => '#0cbba4'
        );
        return (!empty($array[$theme])) ? $array[$theme] : '#000000'; 
    }

    function addScheme($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
}
