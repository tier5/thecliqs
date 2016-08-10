<?php
class Ynbusinesspages_DashboardController extends Core_Controller_Action_Standard
{
	protected $_periods = array(
		Zend_Date::DAY, //dd
	    Zend_Date::WEEK, //ww
	    Zend_Date::MONTH, //MM
	    Zend_Date::YEAR, //y
    );
    protected $_allPeriods = array(Zend_Date::SECOND, Zend_Date::MINUTE, Zend_Date::HOUR, Zend_Date::DAY, Zend_Date::WEEK, Zend_Date::MONTH, Zend_Date::YEAR, );
    protected $_periodMap = array(Zend_Date::DAY => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, ), Zend_Date::WEEK => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::WEEKDAY_8601 => 1, ), Zend_Date::MONTH => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, ), Zend_Date::YEAR => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, Zend_Date::MONTH => 1, ), );
	
	
	public function init() 
	{
	    if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id))) {
            Engine_Api::_() -> core() -> setSubject($business);
        }
        $this -> _helper -> requireSubject('ynbusinesspages_business');
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_() -> core() -> getSubject();
		$actionName = $this->getRequest()->getActionName();
		
		if (!$business->isAllowed('view_dashboard') && $actionName != 'feature' && !$viewer->isAdmin())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
	}
	
	public function chartDataAction() 
    {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        // Get params
        $start = $this -> _getParam('start');
        $offset = $this -> _getParam('offset', 0);
        $type = $this -> _getParam('type', 'all');
        $mode = $this -> _getParam('mode');
        $chunk = $this -> _getParam('chunk');
        $period = $this -> _getParam('period');
        $periodCount = $this -> _getParam('periodCount', 1);
        
		// Get business
		$business_id  = $this ->_getParam('id');

        // Validate chunk/period
        if (!$chunk || !in_array($chunk, $this -> _periods)) {
            $chunk = Zend_Date::DAY;
        }
        if (!$period || !in_array($period, $this -> _periods)) {
            $period = Zend_Date::MONTH;
        }
        if (array_search($chunk, $this -> _periods) >= array_search($period, $this -> _periods)) {
            die('whoops');
            return;
        }

        // Validate start
        if ($start && !is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$start) {
            $start = time();
        }

        // Fixes issues with month view
        Zend_Date::setOptions(array('extend_month' => true, ));

        // Get timezone
        $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
            $timezone = $viewer -> timezone;
        }

        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject -> setTimezone($timezone);

        $partMaps = $this -> _periodMap[$period];
        foreach ($partMaps as $partType => $partValue) {
            $startObject -> set($partValue, $partType);
        }

        // Do offset
        if ($offset != 0) {
            $startObject -> add($offset, $period);
        }

        // Get end time
        $endObject = new Zend_Date($startObject -> getTimestamp());
        $endObject -> setTimezone($timezone);
        $endObject -> add($periodCount, $period);
        $endObject -> sub(1, Zend_Date::SECOND);
		
		//Get table according to type
		switch ($type) {
			case 'reviews':
				$staTable = Engine_Api::_() -> getDbtable('reviews', 'ynbusinesspages');
				break;
			case 'members':
				$staTable = Engine_Api::_() -> getDbtable('membership', 'ynbusinesspages');
				break;	
			case 'followers':
				$staTable = Engine_Api::_() -> getDbtable('follows', 'ynbusinesspages');
				break;	
			case 'comments':
				$staTable = Engine_Api::_() -> getItemTable('activity_action');
				break;
			case 'shares':
				$staTable = Engine_Api::_() -> getItemTable('activity_action');
				break;
			case 'events':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'photos':
				$staTable = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages');
				break;
			case 'videos':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'files':
				$staTable = Engine_Api::_() -> getDbtable('files', 'ynfilesharing');
				break;
			case 'mp3musics':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'musics':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'blogs':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'polls':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;	
			case 'discussions':
				$staTable = Engine_Api::_() -> getDbtable('topics', 'ynbusinesspages');
				break;
			case 'wikis':
				$staTable = Engine_Api::_() -> getDbtable('pages', 'ynwiki');
				break;
			case 'classified':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;	
			case 'groupbuy':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'contests':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'listings':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'jobs':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'ynmusic_songs':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			case 'ynmusic_albums':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;		
			case 'ynultimatevideo_videos':
				$staTable = Engine_Api::_() -> getDbtable('mappings', 'ynbusinesspages');
				break;
			default:
				
				break;	
		}
		$staName = $staTable -> info('name');
		
        // Get data
        $select = $staTable -> select();
		if(!in_array($type, array('comments', 'shares', 'files', 'members')))
		{
        	$select -> where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('creation_date ASC');
		}
		switch ($type) {
			case 'reviews':
				$select -> where('business_id = ?', $business_id);
				break;
			case 'members':
				$select -> where('actived_date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('actived_date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('actived_date ASC');
				$select -> where('resource_id = ?', $business_id);
				break;	
			case 'followers':
				$select -> where('business_id = ?', $business_id);
				break;	
			case 'comments':
				$arrTypes = array('status', 'post', 'post_self');
				$select 
					    -> where('type IN (?)', $arrTypes)
						-> where('object_type = ?', 'ynbusinesspages_business')
					 	-> where('object_id = ?', $business_id)
					    -> where('date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('date ASC');
				break;
			case 'shares':
				
				$tableAttachments =  Engine_Api::_() -> getDbTable('attachments', 'activity');
				$selectAttachments = $tableAttachments -> select() 
											-> from($tableAttachments->info('name'), 'action_id')
											-> where('type = ?', 'ynbusinesspages_business')
											-> where('id = ?', $business_id);
				$actionIds  =  $tableAttachments -> fetchAll($selectAttachments);
				$arr_actionIds =  array();
				foreach($actionIds as $id)
				{
					$arr_actionIds[] = $id -> action_id;
				}
				if(count($arr_actionIds) > 0)
				{
					$select 
					   -> where('type = ?', 'share')
					   -> where('action_id IN (?)', $arr_actionIds)
					   -> where('date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('date ASC');
				}
				else 
				{
					$select -> where("0 = 1");
				}
				break;	
			case 'events':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'event');
				break;
			case 'photos':
				$select -> where('business_id = ?', $business_id);
				break;
			case 'videos':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'video');
				break;	
			case 'files':
				$tableFolders =  Engine_Api::_() -> getDbTable('folders', 'ynfilesharing');
				$selectFolders = $tableFolders -> select() 
											-> from($tableFolders->info('name'), 'folder_id')
											-> where('parent_type = ?', 'ynbusinesspages_business')
											-> where('parent_id = ?', $business_id);
				$folderIds  =  $tableFolders -> fetchAll($selectFolders);
				$arr_folderIds =  array();
				foreach($folderIds as $id)
				{
					$arr_folderIds[] = $id -> folder_id;
				}
				if(count($arr_folderIds) > 0)
				{
					$select 
					   -> where('folder_id IN (?)', $arr_folderIds)
					   -> where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('creation_date ASC');
				}
				else 
				{
					$select -> where("0 = 1");
				}
				break;	
			case 'mp3musics':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'mp3music_album');
				break;	
			case 'musics':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'music_playlist');
				break;
			case 'blogs':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'blog');
				break;	
			case 'polls':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'poll');
				break;				
			case 'discussions':
				$select -> where('business_id = ?', $business_id);
				break;
			case 'wikis':			
				$select -> where('parent_type = ?', 'ynbusinesspages_business')
						-> where('parent_id = ?', $business_id);
				break;
			case 'classified':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'classified');
				break;	
			case 'groupbuy':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'groupbuy_deal');
				break;	
			case 'contests':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'yncontest_contest');
				break;
			case 'listings':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'ynlistings_listing');
				break;	
			case 'jobs':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'ynjobposting_job');
				break;
			case 'ynmusic_songs':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'ynmusic_song');
				break;
			case 'ynmusic_albums':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'ynmusic_album');
				break;
			case 'ynultimatevideo_videos':
				$select -> where('business_id = ?', $business_id)
						-> where('type = ?', 'ynultimatevideo_video');
				break;
			default:
				
				break;
		}

		$rawData = $staTable -> fetchAll($select);
        // Now create data structure
        $currentObject = clone $startObject;
        $nextObject = clone $startObject;
        $data = array();
        $dataLabels = array();
        $cumulative = 0;
        $previous = 0;

        do {
            $nextObject -> add(1, $chunk);
            $currentObjectTimestamp = $currentObject -> getTimestamp();
            $nextObjectTimestamp = $nextObject -> getTimestamp();
            $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;

            // Get everything that matches
            $currentPeriodCount = 0;
            foreach ($rawData as $rawDatum) {
            	if(!in_array($type, array('members', 'shares', 'comments')))
				{
                	$rawDatumDate = strtotime($rawDatum -> creation_date);
				}	
				else
				{
					switch ($type) {
						case 'comments':
							$rawDatumDate = strtotime($rawDatum -> date);
							break;
						case 'shares':
							$rawDatumDate = strtotime($rawDatum -> date);
							break;
						case 'members':
							$rawDatumDate = strtotime($rawDatum -> actived_date);
							break;	
						default:
							
							break;
					}
				}	
                if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
                    $currentPeriodCount += 1;
                }
            }
            // Now do stuff with it
            switch( $mode ) {
                default :
                case 'normal' :
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount;
                    break;
                case 'cumulative' :
                    $cumulative += $currentPeriodCount;
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
                    break;
                case 'delta' :
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount - $previous;
                    $previous = $currentPeriodCount;
                    break;
            }
            $currentObject -> add(1, $chunk);
        } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );

        // Remove some grid lines if there are too many
        $xsteps = 1;
        if (count($data) > 100) {
            $xsteps = ceil(count($data) / 100);
        }
        $title = "<strong>".date('M d Y', $startObject -> getTimestamp())."</strong> to <strong>".date('M d Y', $endObject -> getTimestamp())."</strong>";
        echo Zend_Json::encode(array('json' => $data, 'title' => $title));
        return true;
    }
	
	public function themeAction()
    {
    	$this -> _helper -> content -> setEnabled();
  		$viewer = Engine_Api::_() -> user() -> getViewer();
		
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam('business_id'));
        if (!$business) {
            return $this->_helper->requireSubject()->forward();
        }
		
		//check auth
		if(!$business->isAllowed('change_theme'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this -> view -> business = $business;
		$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
	  	if(!($package -> getIdentity()))
		{
			return $this->_helper->requireSubject()->forward();
		}
	  	// Get form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Theme(array('package' => $package));
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		$theme = $this->_getParam('theme');
		$business -> theme = $theme;
		$business -> save();
		$message = $this->view->translate('Your business theme has been updated.');
		$form -> addNotice($message);
							
    }
	
	public function featureAction()
    {
    	$isSmoothbox = $this -> _getParam('load_smoothbox', '0');
    	if ($isSmoothbox != '1')
    	{
	    	$this -> _helper -> content -> setEnabled();
    	}
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fee_feature = $settings->getSetting('ynbusinesspages_feature_fee', 10);
		
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $this->_getParam('business_id'));
        if (!$business) {
            return $this->_helper->requireSubject()->forward();
        }
		if(!$business -> approved)
		{
			return $this->_helper->requireSubject()->forward();
		}
		//check auth
		if(!$business->isAllowed('feature_business'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
	  	if(!($package -> getIdentity()))
		{
			return $this->_helper->requireSubject()->forward();
		}
	  	// Get form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Feature(array(
			'fee' => $fee_feature,
		));
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
							'action' => 'place-order',
							'id' => $business -> getIdentity(),
							'feature_day_number' => $this->_getParam('day'),
							), 'ynbusinesspages_general', true);
							
		$this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => $redirect_url,
            'format' => 'smoothbox',
            'messages' => array($this->view->translate("Please wait..."))
        ));
    }
	
	public function packageAction()
	{
		$this -> _helper -> content -> setEnabled();
		$businessId = $this -> _getParam('business_id', 0);
		$this -> view -> business = $business =  Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
		
		if(!$business->isAllowed('update_package'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		// Check authorization to post business.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'create') -> isValid())
			return;
	
		$table = Engine_Api::_() -> getItemTable('ynbusinesspages_package');
		$select = $table -> select() -> where('`show` = 1') -> where('`deleted` = 0') -> where('`current` = 1') -> order('order ASC');
		if($business -> package_id != 0)
		{
			$this -> view -> currentPackage = $currentPackage = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
			$select -> where('package_id <> ?', $currentPackage -> getIdentity());
		}
		
		$renewalsTable = Engine_Api::_() -> getDbTable('renewals', 'ynbusinesspages');
		$row = $renewalsTable -> getRowByBusinessId($businessId);
		if(empty($row))
		{
			$row = $renewalsTable -> createRow();
			$row -> business_id = $businessId;
			$row -> time = 'month';
			$row -> save();
		}
		
		$packages = $table -> fetchAll($select);
		$this -> view -> packages = $packages;
	}
	
	public function packageChangeAction()
	{
		$businessId = $this ->_getParam('business_id');
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
		
		$currentPackage = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
		if(empty($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		//check auth
		if(!$business->isAllowed('update_package'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$packageId = $this ->_getParam('packageId');
		$changePackage = Engine_Api::_() -> getItem('ynbusinesspages_package', $packageId);
		
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_ChangePackage();
		if(empty($currentPackage))
		{
			$description = $this->view->translate('YNBUSINESSPAGES_DASHBOARD_PACKAGE_MAKENEW');
		    $description = vsprintf($description, array(
		      $changePackage -> getTitle(),
	    	));
			$form -> setTitle('Buy Package');
			$form -> submit -> setLabel('Buy');
		}
		else 
		{
			$description = $this->view->translate('YNBUSINESSPAGES_DASHBOARD_PACKAGE_WARNING');
		    $description = vsprintf($description, array(
		      $currentPackage -> getTitle(),
		      $changePackage -> getTitle(),
	    	));
		}
		
		$form->setDescription($description);
		// Not post/invalid
	    if( !$this->getRequest()->isPost() ) 
	    {
	    	return;
	    }
		
		$urlRedirect = $this -> view -> url(array(
	                'action' => 'place-order', 
	                'id' => $businessId,
					'packageId' => $packageId
				), 'ynbusinesspages_general', true);
				
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRedirect' => $urlRedirect,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
		
	}
	
	public function renewalNotificationAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$businessId = $this ->_getParam('id');
		$time = $this ->_getParam('time');
		
		if(empty($businessId) || empty($time))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		$renewalsTable = Engine_Api::_() -> getDbTable('renewals', 'ynbusinesspages');
		$row = $renewalsTable -> getRowByBusinessId($businessId);
		if(!empty($row))
		{
			$row -> business_id = $businessId;
			$row -> time = $time;
			$row -> save();
		}
		else 
		{
			$row = $renewalsTable -> createRow();
			$row -> business_id = $businessId;
			$row -> time = $time;
			$row -> save();
		}
		echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => Zend_Registry::get("Zend_Translate") -> _("Success!")));
		exit ;
	}	
	
	public function coverAction()
	{
		$this -> _helper -> content -> setEnabled();
		$this -> view -> businessId = $businessId = $this -> _getParam('business_id', 0);
		if (!$businessId)
		{
			return $this->_helper->requireSubject()->forward();
		}
		/**
		 * @todo get business and check this object is NULL or NOT
		 * code below
		 */
		
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		//check auth
		if(!$business->isAllowed('manage_cover'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$package = $business -> getPackage();
		if (!($package -> getIdentity()))
		{
			return $this->_helper->requireSubject()->forward();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> quota = $quota = (int)$package -> max_cover;
		$coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
		$this -> view -> covers = $covers = $coverTbl -> getCoverByBusiness($businessId);
		$this -> view -> canUpload = (count($covers) < $quota);
		$this -> view -> mine = $viewer->isSelf($business->getOwner());
    	$this -> view -> canEdit = $business->isAllowed('cover_photo', $viewer);
    	
    	$this -> view -> form = $form = new Ynbusinesspages_Form_Cover_Upload();
    	$session = new Zend_Session_Namespace('mobile');
		if (!$session -> mobile)
		{
			$form -> business_id -> setValue($business -> getIdentity());
		}
    	
    	
	    // Not post/invalid
	    if( !$this->getRequest()->isPost() ) 
	    {
	    	return;
	    }
	    if( !$form->isValid($this->getRequest()->getPost()) ) 
	    {
	    	return;
	    }
	    
		// Process
		$table = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array(
				'business_id' => $business -> getIdentity(),
			);
			
			// mobile upload photos
			$arr_photo_id = array();
			if ($session -> mobile && !empty($_FILES['photos']))
			{
				$files = $_FILES['photos'];
				if(!$files['name'][0])
				{
					$form -> addError($this -> view -> translate("Please choose a photo to upload!"));
					return;
				}
				foreach ($files['name'] as $key => $value)
				{
					$type = explode('/', $files['type'][$key]);
					if ($type[0] != 'image' || !is_uploaded_file($files['tmp_name'][$key]))
					{
						continue;
					}
					try
					{
						$temp_file = array(
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'name' => $files['name'][$key]
						);
						
						$photo = $coverTbl -> createRow();
						$photo -> setFromArray(array(
			      			'business_id' => $businessId,
			      			'order' => (int)($coverTbl -> getMaxOrderByBusiness($businessId)) + 1
			      		));
						$photo -> save();
						$photo -> setPhoto($temp_file);
						$arr_photo_id[] = $photo -> getIdentity();
					}
					catch ( Exception $e )
					{
						throw $e;
						return;
					}
				}
			}
			else
			{
				$values = $form -> getValues();
				$arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
			}
			$values = $form -> getValues();

			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			
			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id)
			{
				$photo = Engine_Api::_() -> getItem("ynbusinesspages_cover", $photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$count++;
			}

			$db -> commit();
			return $this->_helper->redirector->gotoRoute();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}
		
		$coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
		$businessId = $_POST['business_id'];
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $_POST['business_id']);
		
		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $error
					)))));
		}
		
		$covers = $coverTbl -> getCoverByBusiness($businessId);
		$package = $business -> getPackage();
		$quota = (int)$package -> max_cover;
		
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}

		if (count($covers) == intval($quota))
		{
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => false,
						'error' => Zend_Registry::get('Zend_Translate') -> _('Can not upload! Maximum of photos is ') . $quota,
						'name' => $name
					)))));
		}
		
		$db = Engine_Api::_() -> getDbtable('photos', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			
			$photo = $coverTbl -> createRow();
			$photo -> setFromArray(array(
	      			'business_id' => $businessId,
	      			'order' => (int)($coverTbl -> getMaxOrderByBusiness($businessId)) + 1
			));
			$photo -> save();

			$temp_file = array(
				'type' => $_FILES['files']['type'][0],
				'tmp_name' => $_FILES['files']['tmp_name'][0],
				'name' => $_FILES['files']['name'][0]
			);

			$photo -> setPhoto($temp_file);
			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo -> getIdentity();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $name,
						'photo_id' => $photo_id
					)))));

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			//$error = $e -> getMessage();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
	}
	
	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynbusinesspages_cover', $this -> getRequest() -> getParam('photo_id'));
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $this -> view -> translate('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		
		// Process
		$db = Engine_Api::_() -> getDbtable('covers', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$photo -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function orderCoverAction()
	{
		$businessId = $this -> _getParam('business_id', 0);

		$order = $this->_getParam('order');
		if( !$order ) {
			$this->view->status = false;
			return;
		}
		
		// Get a list of all photos in this album, by order
		$coverTable = Engine_Api::_()->getItemTable('ynbusinesspages_cover');
		$currentOrder = $coverTable->select()
		->from($coverTable, 'photo_id')
		->where('business_id = ?', $businessId)
		->order('order ASC')
		->query()
		->fetchAll(Zend_Db::FETCH_COLUMN)
		;
		
		// Find the starting point?
		$start = null;
		$end = null;
		for( $i = 0, $l = count($currentOrder); $i < $l; $i++ ) {
			if( in_array($currentOrder[$i], $order) ) {
				$start = $i;
				$end = $i + (count($order) -1);
				break;
			}
		}

		if( null === $start || null === $end ) {
			return;
		}
		
		for( $i = 0, $l = count($currentOrder); $i < $l; $i++ ) 
		{
			if( $i >= $start && $i <= $end ) 
			{
				$photo_id = $order[$i - $start];
			} 
			else 
			{
				$photo_id = $currentOrder[$i];
			}
			
			$coverTable->update(array(
        		'order' => $i,
			), array(
        		'photo_id = ?' => $photo_id,
			));
		}

		$this->view->status = true;
	}
	
	public function deleteCoverAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
	    $cover = Engine_Api::_()->getItem('ynbusinesspages_cover', $this->getRequest()->getParam('cover_id'));
	
	    // In smoothbox
	    $this->_helper->layout->setLayout('default-simple');
	    
	    // Make form
	    $this->view->form = $form = new Ynbusinesspages_Form_Cover_Delete();
	
	    if( !$cover )
	    {
	      $this->view->status = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Cover doesn't exists or not authorized to delete");
	      return;
	    }
	
	    if( !$this->getRequest()->isPost() )
	    {
	      $this->view->status = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
	      return;
	    }
		
	    $db = $cover->getTable()->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
	      $cover->delete();
	      $db->commit();
	      return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Delete successfully!')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
	    }
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	}
	
	public function addRoleAction()
	{
		$businessId = $this -> _getParam('business_id', 0);
		
		if ($businessId == 0)
		{
			return;
		}
		$this -> view -> form = $form =  new Ynbusinesspages_Form_Role_Create(array('businessId' => $businessId));
		// If not post or form not valid, return
	    if( !$this->getRequest()->isPost() ) 
	    {
			return;
	    }
	    
	    if( !$form->isValid($this->getRequest()->getPost()) ) 
	    {
			return;
	    }
	    $values = $form->getValues();
	    
	    $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		$list = $listTbl -> createRow();
		$list->setFromArray($values);
		$list->owner_id = $businessId;
		$list->child_count = 0;
		$listId = $list->save();
		
	    if ((int)$values['clone_list_id'] != 0)
	    {
	    	/**
	    	 * @todo 
	    	 * - get listitem belong original role
	    	 * - insert listitem above to new role
	    	 */	
	    	$clist = Engine_Api::_()->getItem('ynbusinesspages_list', (int)$values['clone_list_id']);
	    	$list->privacy = $clist->privacy;
	    	$list->follow = (int)$values['clone_list_id'];
	    	$list->save();
	    }
	    return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added role successfully!')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function manageRoleAction()
	{
		$this -> _helper -> content -> setEnabled();
		$this -> view -> businessId = $businessId = $this -> _getParam('business_id', 0);
		if (!$businessId)
		{
			return $this->_helper->requireSubject()->forward();
		}
		/**
		 * @todo get business and check this object is NULL or NOT
		 * code below
		 */
		
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		//check auth
		if(!$business->isAllowed('manage_role'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		$this -> view -> roles = $roles = $listTbl -> getListByBusiness($businessId);
	}
	
	public function deleteRoleAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
	
	    // In smoothbox
	    $this->_helper->layout->setLayout('default-simple');
		
		$this -> view -> roleId = $roleId = $this -> _getParam('role_id', 0);
		if (!$roleId)
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$role = Engine_Api::_()->getItem('ynbusinesspages_list', $roleId);
		/**
		 * @todo checking permission before delete role
		 */
	    
		// Make form
	    $this->view->form = $form = new Ynbusinesspages_Form_Role_Delete();
		if( !$this->getRequest()->isPost() )
	    {
	      $this->view->status = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
	      return;
	    }
	    
		$db = $role->getTable()->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
	      $role->delete();
	      $db->commit();
	      return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted role successfully!')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
	    }
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	}
	
	public function editRoleAction()
	{
		$businessId = $this -> _getParam('business_id', 0);
		if ($businessId == 0)
		{
			return;
		}
		$this -> view -> roleId = $roleId = $this -> _getParam('role_id', 0);
		if (!$roleId)
		{
			return $this->_helper->requireSubject()->forward();
		}
		$list = Engine_Api::_()->getItem('ynbusinesspages_list', $roleId);
		$this -> view -> form = $form =  new Ynbusinesspages_Form_Role_Edit(array('businessId' => $businessId));
		$form -> populate(array(
			'name' => $list->name,
			'clone_list_id' => $list->follow,
		)) ;
		
		// If not post or form not valid, return
	    if( !$this->getRequest()->isPost() ) 
	    {
			return;
	    }
	    
	    if( !$form->isValid($this->getRequest()->getPost()) ) 
	    {
			return;
	    }
	    $values = $form->getValues();
		$list->setFromArray($values);
		$list->owner_id = $businessId;
		$listId = $list->save();
		
	    if ((int)$values['clone_list_id'] != 0)
	    {
	    	/**
	    	 * @todo 
	    	 * - get listitem belong original role
	    	 * - insert listitem above to new role
	    	 */	
	    	$clist = Engine_Api::_()->getItem('ynbusinesspages_list', (int)$values['clone_list_id']);
	    	$list->privacy = $clist->privacy;
	    	$list->save();
	    }
	    return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edited role successfully!')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function roleSettingAction()
	{
		$this -> _helper -> content -> setEnabled();
		$this->view->businessId = $businessId = $this -> _getParam('business_id', 0);
		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		//check auth
		if(!$business->isAllowed('manage_rolesetting'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$roleId = $this -> _getParam('role_id', 0);
		if ($roleId == 0)
	    {
	    	$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
			$options = $listTbl -> getListAssocByBusiness($businessId);
			unset($options[0]);
			$roleId = array_shift(array_keys($options));
	    }
	    
		$this -> view -> form = $form = new Ynbusinesspages_Form_Role_Setting(array(
			'businessId' => $businessId,
			'roleId' => $roleId
		));
		
		// If not post or form not valid, return
	    if( !$this->getRequest()->isPost() ) 
	    {
			return;
	    }
	    
	    if( !$form->isValid($this->getRequest()->getPost()) ) 
	    {
			return;
	    }
	    
	    $values = $form->getValues();
	  	$role = Engine_Api::_()->getItem('ynbusinesspages_list', $roleId);
	  	$role -> privacy = $values;
	  	$role -> save();
	  	return $this->_helper->redirector->gotoRoute();
	}
    
    public function moduleAction() {
        $this -> _helper -> content -> setEnabled();
        $this->view->business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$business->isAllowed('manage_module')) {
            return $this -> _helper -> requireAuth() -> forward();
        }
        $package = $business->getPackage();
        if ($package -> getIdentity()) {
            $this->view->modules = $modules = $package->getAvailableModules(); 
        }
    }
    
    public function statisticsAction()
    {
    	$this -> _helper -> content -> setEnabled();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> formChartStatistic = $formChartStatistic = new Ynbusinesspages_Form_Business_ChartStatistics(array('business' => $business));
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
    }
	
    public function anouncementAction()
    {
    	//check auth
    	$business = Engine_Api::_() -> core() -> getSubject();
		if(!$business->isAllowed('manage_announcement'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
    	$this -> _helper -> content -> setEnabled();
    }
    
}