<?php
class Ynbusinesspages_IndexController extends Core_Controller_Action_Standard {
	public function init() {
	    // only show to member_level if authorized
	    if( !$this->_helper->requireAuth()->setAuthParams('ynbusinesspages_business', null, 'view')->isValid() ) return;
	}
	public function indexAction() {
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingAction() {
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	
	public function validFormSearch()
	{
		$this->view->form = $form = new Ynbusinesspages_Form_Search(array(
            'type' => 'ynbusinesspages_business'
        ));
        
        $categories = Engine_Api::_() -> getItemTable('ynbusinesspages_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynbusinesspages') {
            if ($controller == 'index' && (in_array($action, array('claim', 'manage', 'listing', 'manage-claim', 'manage-favourite', 'manage-follow')))) {
                $forwardListing = false;
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynbusinesspages_general', true));
        }
        
        if(in_array($action, array('manage', 'manage-claim', 'manage-favourite', 'manage-follow')))
        {
            $form -> removeElement('lat');
            $form -> removeElement('long');
            $form -> removeElement('location');
            $form -> removeElement('within');
            $form -> removeElement('category');
            switch ($action) {
                case 'manage-claim':
                    $arr_status = array(
                        'all'       => 'All',
                        'unclaimed' => 'Unclaimed', 
                        'claimed' => 'Claimed', 
                    );
                    $form -> status -> addMultiOptions($arr_status);
                    break;
                case 'manage':
                    $arr_status = array(
                        'all'       => 'All',
                        'draft' => 'Draft', 
                        'pending' => 'Pending', 
                        'published' => 'Published', 
                        'closed' => 'Closed', 
                        'denied' => 'Denied', 
                    );
                    $form -> status -> addMultiOptions($arr_status);
                    break;  
                default:
                    $form -> removeElement('status');
                    break;
            }
        }
        else
        {
            $form -> removeElement('status');
        }
        //Setup params
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $originalOptions = $params;
        
        if ($form->isValid($params)) {
            $params = $form->getValues();
        } else {
            //$params = array();
        }
		return $params;
	}
	
	public function manageAction() {
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> content -> setEnabled();
		
	    //Setup params
		$params = $this -> validFormSearch();		
        $originalOptions = $params;
		$posts = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
        if (!isset($posts['page']) || $posts['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$posts['page'];
        }
		$originalOptions['page'] = $page;
		
		$params['user_id'] = $viewer -> getIdentity();
		
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		if (!isset($params['status']))
		{
			$params['status'] = 'all';
		}
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page );
        
        $this->view->totalBusinesses = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
	}

	public function manageClaimAction() {
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> content -> setEnabled();
		
	    //Setup params
        $params = $this -> validFormSearch();
        $originalOptions = $params;
		$posts = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
        if (!isset($posts['page']) || $posts['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$posts['page'];
        }
		
		$originalOptions['page'] = $page;
		
		$params['claimer_id'] = $viewer -> getIdentity();
		$params['claim'] = 1;
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page );
        
        $this->view->totalBusinesses = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
	}

	public function manageFollowAction() {
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> content -> setEnabled();
		
	    //Setup params
        $params = $this -> validFormSearch();
        $originalOptions = $params;
		$posts = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
        if (!isset($posts['page']) || $posts['page'] == '0') 
        {
            $page = 1;
        }
        else 
        {
            $page = (int)$posts['page'];
        }
		
		$originalOptions['page'] = $page;
		
		$params['follower_id'] = $viewer -> getIdentity();
		$params['follow'] = 1;
		
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        $paginator->setCurrentPageNumber($page );
        $this->view->totalBusinesses = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
	}

	public function manageFavouriteAction() {
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> content -> setEnabled();
		
	    //Setup params
        $params = $this -> validFormSearch();
        $originalOptions = $params;
		$posts = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
        if (!isset($posts['page']) || $posts['page'] == '0') 
        {
            $page = 1;
        }
        else 
        {
            $page = (int)$posts['page'];
        }
		
		$originalOptions['page'] = $page;
		
		$params['favouriter_id'] = $viewer -> getIdentity();
		$params['favourite'] = 1;
		
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        $paginator->setCurrentPageNumber($page );
        $this->view->totalBusinesses = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
	}
    
	public function directionAction() 
    {
	    $id = $this -> _getParam('id', 0);
	    if (!$id) {
	        return $this->_helper->requireAuth()->forward();
	    }
	    $this->view->item = $item = Engine_Api::_()->getItem('ynbusinesspages_location', $id);
	    if (empty($item)) {
	        return $this->_helper->requireAuth()->forward();
	    }   
   }
	
	public function getMyLocationAction()
	{
		$latitude = $this -> _getParam('latitude');
		$longitude = $this -> _getParam('longitude');
		$values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;
		die ;
	}
	
	 public function founderSuggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'type' => 'user', 
                'url' => $friend->getHref(),
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
	
	public function addInfoAction()
	{
		// Disable layout and viewrenderer
		$this -> _helper -> layout -> disableLayout();
		$label_header = 'header_'.$this->_getParam('index');
		$label_content = 'content_'.$this->_getParam('index');
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_AddInfo(array('labelHeader' => $label_header, 'labelContent' => $label_content));
	}

	public function createAction() {
		$this -> _helper -> content -> setEnabled();

		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		// Check authorization to post business.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'create') -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$isCreator = Engine_Api::_() -> getDbTable('creators', 'ynbusinesspages') -> checkIsCreator($viewer);
		if (!$isCreator) {
			$this -> _helper -> redirector -> gotoRoute(array('module' => 'ynbusinesspages', 'controller' => 'index', 'action' => 'create-step-one'), 'ynbusinesspages_general', TRUE);
		}

	}

	public function createStepOneAction() {
		$this -> _helper -> content -> setEnabled();

		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		// Check authorization to post business.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'create') -> isValid())
			return;
	    
        //check max businesses user can create
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynbusinesspages_business', $viewer->level_id, 'max');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynbusinesspages_business')
                ->where('name = ?', 'max'));
            if ($row) {
                $max = $row->value;
            }
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->select()
            -> where('user_id = ?', $viewer->getIdentity())
			-> where('is_claimed <> ?', 1)
            -> where('deleted <> ?', 1);
            
        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) 
        {
        	$this -> view -> notCreateMore = true;
        }

		$table = Engine_Api::_() -> getItemTable('ynbusinesspages_package');
		$select = $table -> select() -> where('`show` = 1') -> where('`deleted` = 0') -> where('`current` = 1') -> order('order ASC');
		
		$packages = $table -> fetchAll($select);
		$this -> view -> packages = $packages;
		
		if (count($packages) == 1) {
			foreach ($packages as $package) {
				if ($package->price == 0) {
					$this -> _helper -> redirector -> gotoRoute(array('module' => 'ynbusinesspages', 'controller' => 'index', 'action' => 'create-step-two', 'package_id' => $package -> getIdentity()), 'ynbusinesspages_general', TRUE);
				}
			}
		}
	}

	public function createStepTwoAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		// Check authorization to post business.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'create') -> isValid())
			return;
		
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
        //check max businesses user can create
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynbusinesspages_business', $viewer->level_id, 'max');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynbusinesspages_business')
                ->where('name = ?', 'max'));
            if ($row) {
                $max = $row->value;
            }
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->select()
            -> where('user_id = ?', $viewer->getIdentity())
			-> where('is_claimed <> ?', 1)
            -> where('deleted <> ?', 1);
            
        $raw_data = $table->fetchAll($select);
        if (($max != 0) && (sizeof($raw_data) >= $max)) {
            echo ('Your businesses are reach limit. Plese delete some businesses for creating new.');
            $this -> _helper -> content -> setNorender();
            return;
        }
        
		//get package
		$package_id = $this ->_getParam('package_id');
		$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $package_id);
		
		if(!$package -> getIdentity())
		{
			$message = $this -> view -> translate('Please select package.');
            return $this -> _redirector($message);
		}
		$this -> view -> package = $package;
		
		//get first industry
		$tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
		$categories = $tableCategory -> getCategories();
		$firstCategory = $categories[1];
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynbusinesspages_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynbusinesspages_business');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}
		$businessInfo = array();
		if($package -> allow_owner_add_customfield)
		{
			$posts = $this -> getRequest() -> getPost();
			$number_add_more = $posts['number_add_more'];
			$this -> view -> number_add_more_index = $number_add_more_index = $posts['number_add_more_index'];
			for($i = 1; $i <= $number_add_more_index; $i++)
			{
				$header  = $posts['header_'.$i];
				$content  = $posts['content_'.$i];
				if($header != "" || $content != "")
				{ 
					$businessInfo[] = (object)array(
						'header' => $header,
						'content' => $content,
					);
				}
			}
		}	
		if(!empty($businessInfo))
		{
			$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Create( array(
				'formArgs' => $formArgs,
				'package' => $package,
				'businessInfo' => $businessInfo,
			));
		    $number_add_more = count($businessInfo);
			$form -> number_add_more -> setValue($number_add_more);
			$form -> number_add_more_index -> setValue($number_add_more_index);
		}
		else
		{
			$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Create( array('formArgs' => $formArgs, 'package' => $package));
		}
		
		// Populate industry list.
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			if (in_array($item['category_id'], $package->category_id))
				$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
		}

		//populate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create business require at least one category. Please contact admin for more details.');
		}

		
		//populate data
		
		$posts = $this -> getRequest() -> getPost();
		$form -> populate($posts);
		$this -> view -> posts = $posts;
		$number_location = $posts['number_location'];
		$this -> view -> number_location_index = $number_location_index = $posts['number_location_index'];
		$subLocation = array();
		for($i = 1; $i <= $number_location_index; $i++)
		{
			$title_name = 'location_title_'.$i;
			$location_name = 'location_address_'.$i;
			$latitude_name = 'lat_'.$i;
			$longitude_name = 'long_'.$i;
			$title = $posts[$title_name];
			$location = $posts[$location_name];
			$latitude = $posts[$latitude_name];
			$longitude = $posts[$longitude_name];
			if(!empty($title) || !empty($location))
			{ 
				$subLocation[] = (object)array(
					'title' => $title,
					'location' => $location,
					'latitude' => $latitude,
					'longitude' => $longitude,
				);
			}
		}
		if(!empty($subLocation))
		{
			$number_location = count($subLocation);
			$form -> number_location -> setValue($number_location);
			$form -> number_location_index -> setValue($number_location_index);
		}
		$this -> view -> subLocation = $subLocation;
		
		//return if not click submit or save draft
		$submit_button = $this -> _getParam('submit_button');
		$save_draft = $this -> _getParam('save_draft');
		if (!isset($submit_button))
		{
			if (!isset($save_draft))
			{
				return;
			}	
		}
		
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($posts)) {
			return;
		}
		
		//get values
		$params = $this ->_getAllParams();
		$values = $form -> getValues();
		
		//check email
		$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
		if (!preg_match($regexp, $values['email'])) {
			$form -> addError('Please enter valid email!');
			return;
		}
		
		//check exsiting name & email
		$businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$checkExist = $businessTable -> getBusinessByNameEmail($values['name'], $values['email']);
		
		if(!empty($checkExist))
		{
			$form -> addError('Your business name and email are already existing!');
			return;
		}
		
		//merge 'phone', 'fax', 'web_address', 'theme'
		$sub_fone = $this -> _getParam('sub_phone');
		$sub_fax = $this -> _getParam('sub_fax');
		$sub_web_address = $this -> _getParam('sub_web_address');
		$theme = $this -> _getParam('theme');
		if(!empty($sub_fone))
		{
			 array_push($sub_fone, $values['phone']);
			 $values['phone'] = $sub_fone;
		}
		if(!empty($sub_fax))
		{
			 array_push($sub_fax, $values['fax']);
			 $values['fax'] = $sub_fax;
		}
		if(!empty($sub_web_address))
		{
			 array_push($sub_web_address, $values['web_address']);
			 $values['web_address'] = $sub_web_address;
		}
		if(!empty($theme))
		{
			$values['theme'] = $theme;
		}
		if(!is_array($values['phone']))
			$values['phone'] = array($values['phone']);
		if(!is_array($values['fax']))
			$values['fax'] = array($values['fax']);
		if(!is_array($values['web_address']))	
			$values['web_address'] = array($values['web_address']);
		
		//user_id & status & approved
		$values['user_id'] = $viewer -> getIdentity();
		$values['status'] = 'draft';
		$values['approved'] = false;
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			//save business
			$business = $businessTable -> createRow();
      		$business->setFromArray($values);
			$business -> save();
			
			// Add tags
			$tags = preg_split('/[,]+[\s]+[,]+|[,]+/', trim($values['tags']));
			$business -> tags() -> addTagMaps($viewer, $tags);
			
			//Set Operation hours
			$arr_days = array('monday', 'tuesday',  'wednesday',
  							  'thursday', 'friday', 'saturday',
  								'sunday');
			$tableOperationHour = Engine_Api::_() -> getDbTable('operatinghours', 'ynbusinesspages');
			foreach($arr_days as $each_day)
			{
				$from_day_name = $each_day."_from";
				$to_day_name = $each_day."_to";
				$from_day_value = $this -> _getParam($from_day_name);
		        $to_day_value = $this -> _getParam($to_day_name);
				
				if(!empty($from_day_value) && !empty($to_day_value))
				{
					$operateRow = $tableOperationHour -> createRow();
					$operateRow -> business_id = $business -> getIdentity();
					$operateRow -> day = $each_day;
					$operateRow -> from = $from_day_value;
					$operateRow -> to = $to_day_value;
					$operateRow -> save();
				}
			}					
			
			// Set Location
			$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
			$number_location_index = $this ->_getParam('number_location_index');
			if(!empty($number_location_index) && $number_location_index > 0)
			{
				for($locationIndex = 1; $locationIndex <= $number_location_index; $locationIndex++)
				{
					$title_name = 'location_title_'.$locationIndex;
					$location_name = 'location_address_'.$locationIndex;
					$latitude_name = 'lat_'.$locationIndex;
					$longitude_name = 'long_'.$locationIndex;
					$title = strip_tags($this ->_getParam($title_name));
					$location = $this ->_getParam($location_name);
					$latitude = $this ->_getParam($latitude_name);
					$longitude = $this ->_getParam($longitude_name);
					if(!empty($location) && !empty($latitude) && !empty($longitude))
					{
						$locationRow = $tableLocation -> createRow();
						$locationRow -> business_id = $business -> getIdentity();
						$locationRow -> title = $title;
						$locationRow -> location = $location;
						$locationRow -> latitude = $latitude;
						$locationRow -> longitude = $longitude;
						$locationRow -> main = false;
						$locationRow -> save();
					}
				}
			}
			//for location in form  
			if(!empty($values['location_address']) && !empty($values['lat']) && !empty($values['long']))		
			{
				$locationRow = $tableLocation -> createRow();
				$locationRow -> business_id = $business -> getIdentity();
				$locationRow -> title = strip_tags($this ->_getParam('location_title'));
				$locationRow -> location = $values['location_address'];
				$locationRow -> latitude = $values['lat'];
				$locationRow -> longitude = $values['long'];
				$locationRow -> main = true;
				$locationRow -> save();
			}
			
			//Set Founders
			$tableFounder = Engine_Api::_() -> getDbTable('founders', 'ynbusinesspages');
			$toValues = $this ->_getParam('toValues');
			if(!empty($toValues))
			{
				$founders = explode(",", $toValues);
				foreach($founders as $founder)
				{
					$founderRow = $tableFounder -> createRow();
					$founderRow -> business_id = $business -> getIdentity();
					$user = Engine_Api::_() -> getItem('user', $founder);
					if($user -> getIdentity() > 0)
						$founderRow -> user_id = $founder;
					else 
						$founderRow -> name = $founder;						
					$founderRow -> save();
				}
			}
			
			// Set photo
			if (!empty($values['photo'])) {
				$business -> setPhoto($form -> photo);
			}
			
			if(($package -> getIdentity() > 0) && ($package -> allow_owner_add_customfield))
			{
				// Insert Addtional Information
				$tableBusinessInfo = Engine_Api::_() -> getDbTable('businessinfos', 'ynbusinesspages');
				$number_add_more_index = $params['number_add_more_index'];
				for ($i = 1; $i <= $number_add_more_index; $i++) {
					$header = $params['header_' . $i];
					$content = $params['content_' . $i];
					if (!empty($header) && !empty($content)) {
						$allowed_html = '<strong><b><em><i><u><strike><sub><sup><p><div><pre><address><h1><h2><h3><h4><h5><h6><span><ol><li><ul><a><img><embed><br><hr><object><param><iframe>';
						$infoRow = $tableBusinessInfo -> createRow();
						$infoRow -> header = strip_tags($header, $allowed_html);
						$infoRow -> content = strip_tags($content, $allowed_html);
						$infoRow -> business_id = $business -> getIdentity();
						$infoRow -> save();
					}
				}
			}
			
			//insert category to mapping table
			if (!empty($values['category_id'])) {
				$tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
				$checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($values['category_id'], $business -> getIdentity());
				if (empty($checkCategory)) {
					$rowCategoryMap = $tableCategoryMap -> createRow();
					$rowCategoryMap -> business_id = $business -> getIdentity();
					$rowCategoryMap -> category_id = $values['category_id'];
					$rowCategoryMap -> main = true;
					$rowCategoryMap -> save();
				}
				$sub_categories = $this -> _getParam('sub_category');
				if (!empty($sub_categories)) {
					foreach ($sub_categories as $sub_category_id) {
						$checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($sub_category_id, $business -> getIdentity());
						if (empty($checkCategory)) {
							$rowCategoryMap = $tableCategoryMap -> createRow();
							$rowCategoryMap -> business_id = $business -> getIdentity();
							$rowCategoryMap -> category_id = $sub_category_id;
							$rowCategoryMap -> main = false;
							$rowCategoryMap -> save();
						}
					}
				}
			}
			
			//save custom field values of category
			$customdefaultfieldform = $form -> getSubForm('fieldsParent');
			$customdefaultfieldform -> setItem($business);
			$customdefaultfieldform -> saveValues();
			
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($business);
			$customfieldform -> saveValues();
			
            //set auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('video', 'view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = 'everyone';
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($business, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
			//send email
			$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
			$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
			$href =  				 
				'http://'. @$_SERVER['HTTP_HOST'].
				Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
			$params['business_link'] = $href;	
			$params['business_name'] = $business -> getTitle();
			try{
				Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_business_created',$params);
			}
			catch(exception $e){
				
			}
			/**
			 * Insert 2 default roles: ADMIN and MEMBER
			 */
			$business -> insertSampleList();	
			// Commit
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
	        {
	        	$user = $business -> getOwner();
				if($user -> getIdentity())
	            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynbusinesspages_new', $user);
			}
			
			$db -> commit();

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		if (isset($save_draft))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'id' => $business -> getIdentity(),
					'slug' => $business -> getSlug(),
				), 'ynbusinesspages_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		if (isset($submit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'controller' => 'index',
					'action' => 'place-order',
					'id' => $business -> getIdentity(),
					'packageId' => $package_id
				), 'ynbusinesspages_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}

	}
	
	public function createForClaimingAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		// Check authorization to post business.
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'create') -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$isCreator = Engine_Api::_() -> getDbTable('creators', 'ynbusinesspages') -> checkIsCreator($viewer);
		if(!$isCreator)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> _helper -> content -> setEnabled();
		
		//get first industry
		$tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
		$categories = $tableCategory -> getCategories();
		$firstCategory = $categories[1];
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynbusinesspages_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynbusinesspages_business');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_CreateClaim( array('formArgs' => $formArgs));
		
		// Populate industry list.
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}

		//populate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create business require at least one category. Please contact admin for more details.');
		}
		
		//populate data
		$posts = $this -> getRequest() -> getPost();
		$form -> populate($posts);
		$this -> view -> posts = $posts;
		$number_location = $posts['number_location'];
		$this -> view -> number_location_index = $number_location_index = $posts['number_location_index'];
		
		$subLocation = array();
		for($i = 1; $i <= $number_location_index; $i++)
		{
			$title_name = 'location_title_'.$i;
			$location_name = 'location_address_'.$i;
			$latitude_name = 'lat_'.$i;
			$longitude_name = 'long_'.$i;
			$title = $posts[$title_name];
			$location = $posts[$location_name];
			$latitude = $posts[$latitude_name];
			$longitude = $posts[$longitude_name];
			if(!empty($title) || !empty($location))
			{ 
				$subLocation[] = (object)array(
					'title' => $title,
					'location' => $location,
					'latitude' => $latitude,
					'longitude' => $longitude,
				);
			}
		}
		if(!empty($subLocation))
		{
			$number_location = count($subLocation);
			$form -> number_location -> setValue($number_location);
			$form -> number_location_index -> setValue($number_location_index);
		}
		$this -> view -> subLocation = $subLocation;
		$submit_button = $this -> _getParam('submit_button');
		if (!isset($submit_button))
		{
		    $form -> populate($form -> getValues());
			return;
		}
		
		// Check method and data validity.
		$posts = $this -> getRequest() -> getPost();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($posts)) {
			return;
		}
		
		//get values
		$params = $this ->_getAllParams();
		$values = $form -> getValues();
		
		
		$theme = $this -> _getParam('theme');
		if(!empty($theme))
		{
			$values['theme'] = $theme;
		}
		
		//status
		$values['status'] = 'unclaimed';
		$superAdmins = Engine_Api::_() -> user() -> getSuperAdmins();
		foreach($superAdmins as $superAdmin)
		{
			$values['user_id'] = $superAdmin -> getIdentity();
			break;
		}
		$values['is_claimed'] = true;
			
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			//save business
			$businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
			$business = $businessTable -> createRow();
      		$business->setFromArray($values);
			$business -> save();
			
			// Set Location
			$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
			$number_location_index = $this ->_getParam('number_location_index');
			if(!empty($number_location_index) && $number_location_index > 0)
			{
				for($locationIndex = 1; $locationIndex <= $number_location_index; $locationIndex++)
				{
					$title_name = 'location_title_'.$locationIndex;
					$location_name = 'location_address_'.$locationIndex;
					$latitude_name = 'lat_'.$locationIndex;
					$longitude_name = 'long_'.$locationIndex;
					$title = strip_tags($this ->_getParam($title_name));
					$location = $this ->_getParam($location_name);
					$latitude = $this ->_getParam($latitude_name);
					$longitude = $this ->_getParam($longitude_name);
					if(!empty($location) && !empty($latitude) && !empty($longitude))
					{
						$locationRow = $tableLocation -> createRow();
						$locationRow -> business_id = $business -> getIdentity();
						$locationRow -> title = $title;
						$locationRow -> location = $location;
						$locationRow -> latitude = $latitude;
						$locationRow -> longitude = $longitude;
						$locationRow -> main = false;
						$locationRow -> save();
					}
				}
			}
			//for location in form  
			if(!empty($values['location_address']) && !empty($values['lat']) && !empty($values['long']))		
			{
				$locationRow = $tableLocation -> createRow();
				$locationRow -> business_id = $business -> getIdentity();
				$locationRow -> title = strip_tags($this ->_getParam('location_title'));
				$locationRow -> location = $values['location_address'];
				$locationRow -> latitude = $values['lat'];
				$locationRow -> longitude = $values['long'];
				$locationRow -> main = true;
				$locationRow -> save();
			}
			
			// Set photo
			if (!empty($values['photo'])) {
				$business -> setPhoto($form -> photo);
			}

			//insert category to mapping table
			if (!empty($values['category_id'])) {
				$tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
				$checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($values['category_id'], $business -> getIdentity());
				if (empty($checkCategory)) {
					$rowCategoryMap = $tableCategoryMap -> createRow();
					$rowCategoryMap -> business_id = $business -> getIdentity();
					$rowCategoryMap -> category_id = $values['category_id'];
					$rowCategoryMap -> main = true;
					$rowCategoryMap -> save();
				}
				$sub_categories = $this -> _getParam('sub_category');
				if (!empty($sub_categories)) {
					foreach ($sub_categories as $sub_category_id) {
						$checkCategory = $tableCategoryMap -> checkExistCategoryByBusiness($sub_category_id, $business -> getIdentity());
						if (empty($checkCategory)) {
							$rowCategoryMap = $tableCategoryMap -> createRow();
							$rowCategoryMap -> business_id = $business -> getIdentity();
							$rowCategoryMap -> category_id = $sub_category_id;
							$rowCategoryMap -> main = false;
							$rowCategoryMap -> save();
						}
					}
				}
			}
			
			//save custom field values of industries
			$customdefaultfieldform = $form -> getSubForm('defaultFields');
			$customdefaultfieldform -> setItem($business);
			$customdefaultfieldform -> saveValues();
			
			//save custom field values of industries
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($business);
			$customfieldform -> saveValues();
			
			 //set auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('video', 'view', 'comment');
            foreach ($auth_arr as $elem) {
                $auth_role = 'everyone';
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($business, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
			
			//send email
			$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
			$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
			$href =  				 
				'http://'. @$_SERVER['HTTP_HOST'].
				Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
			$params['business_link'] = $href;	
			$params['business_name'] = $business -> getTitle();
			try{
				Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_business_created',$params);
			}
			catch(exception $e)
			{
				
			}
			$business -> insertSampleList();	
			// Commit
			$db -> commit();

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		if (isset($submit_button))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'id' => $business -> getIdentity(), 
				'slug' => $business -> getSlug()
				), 'ynbusinesspages_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}

	}
	
	public function deleteClaimAction() {
		// Check authorization to claim business.
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'claim') -> isValid())
            return;
		
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this ->_getParam('business_id'));
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_DeleteClaim();
		if (!$business)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}
		
		$claimTable = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');
		
		$db = $claimTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$claimRequest = $claimTable -> getClaimRequest($viewer -> getIdentity(), $business -> getIdentity());
			if(!empty($claimRequest))
			{
				$claimRequest -> delete();
			}
			
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Delete successfully.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage-claim'), 'ynbusinesspages_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function claimBusinessAction() {
		// Check authorization to claim business.
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'claim') -> isValid())
            return;
		
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('id'));
		
		if($business -> status != 'unclaimed')
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Claim();
		if (!$business)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}
		
		$claimTable = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');
		
		$db = $claimTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$claimRequest = $claimTable -> getClaimRequest($viewer -> getIdentity(), $business -> getIdentity());
			if(empty($claimRequest))
			{
				$claimRequest = $claimTable -> createRow();
				$claimRequest -> business_id = $business -> getIdentity();
				$claimRequest -> user_id = $viewer -> getIdentity();
				$claimRequest -> status = 'pending';
				$claimRequest -> save();
			}
			else
			{
				$claimRequest -> business_id = $business -> getIdentity();
				$claimRequest -> user_id = $viewer -> getIdentity();
				$claimRequest -> status = 'pending';
				$claimRequest -> save();
			}
			
			//send notice
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($viewer, $business, $business, 'ynbusinesspages_claim_success');
			
			//send email
			$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
			$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
			$href =  				 
				'http://'. @$_SERVER['HTTP_HOST'].
				Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
			$params['business_link'] = $href;	
			$params['business_name'] = $business -> getTitle();
			if(!empty($viewer))
			{
				try{
					Engine_Api::_()->getApi('mail','ynbusinesspages')->send($viewer -> email, 'ynbusinesspages_claim_success',$params);
				}
				catch(exception $e)
				{
					
				}
			}
			
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Claim was successfully sent. Awaiting approval.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynbusinesspages_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function placeOrderAction() 
    {
    	$settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> business = $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this ->_getParam('id'));
		$this -> view -> package = $package = Engine_Api::_() -> getItem('ynbusinesspages_package', $this ->_getParam('packageId'));
       	if(empty($package))
		{
			$this -> view -> package = $package =  new Ynbusinesspages_Model_Package(array());
		}
		else //if buy package
		{
			//check auth
			if(!$business->isAllowed('update_package'))
			{
				return $this -> _helper -> requireAuth() -> forward();
			}		
		}
       	$feature_day_number = $this ->_getParam('feature_day_number');
	    
		if($feature_day_number)
		{
			$canFeature = $business -> isAllowed('feature_business');
			if(!$canFeature)
			{
				$message = $this -> view -> translate('You do not have permission to do this.');
            	return $this -> _redirector($message);
			} 
		}
		else {
			if($business->user_id != $viewer->getIdentity())
	        {
	            $message = $this -> view -> translate('You do not have permission to do this.');
	            return $this -> _redirector($message);
	        }
		}
        
        if (!($package -> getIdentity()) && !$feature_day_number) {
            $message = $this -> view -> translate('Please select package or set feature day.');
            return $this -> _redirector($message);
        }
		//check if feature business
		if($feature_day_number)
		{
			//check auth
			if(!$business -> approved)
			{
				return $this -> _helper -> requireAuth() -> forward();
			}
			if($feature_day_number <= 0)
			{
				$message = $this -> view -> translate('Invalid feature day.');
            	return $this -> _redirector($message);
			}
		}
		
		if($package -> getIdentity())
		{
			$package_id = $package -> getIdentity();
		}
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
    	$action_type = "";
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynbusinesspages_business', null, 'use_credit') -> checkRequire()) {
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
            if ($credit_enable)
            {
            	if($package -> getIdentity()){
					$action_type = 'publish_businesses';
				}
				else {
					$action_type = 'feature_businesses';
				}
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1 );
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
		$package_price = 0;
		if($package -> getIdentity())
		{
			$package_price = $package -> price;
			$this -> view -> total_pay = $total_pay = $package_price;
		}
		else
		{
			$this -> view -> feature_fee = $feature_fee = $settings->getSetting('ynbusinesspages_feature_fee', 10);
			$this -> view -> total_pay = $total_pay = $feature_day_number * $feature_fee;
		}
	   //if package free
	   if($total_pay == 0)
	   {
			//core - buyBusiness
			$db = Engine_Api::_()->getDbtable('business', 'ynbusinesspages')->getAdapter();
			$db->beginTransaction();
			try 
			{
				if($package -> getIdentity())
				{
					Engine_Api::_() -> ynbusinesspages() -> buyBusiness($business->getIdentity(), $package_id);
					$db -> commit();
				}
				else 
				{
					Engine_Api::_() -> ynbusinesspages() -> featureBusiness($business->getIdentity(), $feature_day_number);
					$db -> commit();
				}
				
			} 
			catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
		    
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'id' => $business -> getIdentity(),
					'slug' => $business -> getSlug(),
				), 'ynbusinesspages_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Success...'))
			 ));
		}  
	   
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit)) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
		
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynbusinesspages');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
        	if($package -> getIdentity())
			{
	            $ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
		            'package_id' => $package_id, 
		            'item_id' => $business -> getIdentity(),
		            'price' => $total_pay, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				));
			}
			else 
			{
				$ordersTable -> insert(array(
	            	'user_id' => $viewer -> getIdentity(), 
		            'creation_date' => new Zend_Db_Expr('NOW()'), 
		            'featured' => true,
					'feature_day_number' => $feature_day_number,
		            'item_id' => $business -> getIdentity(),
		            'price' => $total_pay, 
		            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				));
			}
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
    }

    public function updateOrderAction() 
    {
        $type = $this ->_getParam('type');
        $id = $this ->_getParam('id');
        if(isset($type))
        {
            switch ($type) {
                
                case 'paycredit':
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynbusinesspages');
					$order = $ordersTable -> getLastPendingOrder();
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
	                        'action' => 'pay-credit', 
	                        'item_id' => $id,
							'order_id' => $order -> getIdentity()
						), 'ynbusinesspages_general', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
            
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynbusinesspages');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynbusinesspages', 'cancel_route' => 'ynbusinesspages_transaction', 'return_route' => 'ynbusinesspages_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynbusinesspages_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function payCreditAction()
    {
    	$credit_enable = Engine_Api::_() -> hasModuleBootstrap('yncredit');
        if (!$credit_enable)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
		$order = Engine_Api::_()->getItem('ynbusinesspages_order', $this->_getParam('order_id'));
		if(!$order)
        {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
		$action_type = "";
		$featured = $order -> featured;
		$package_id = $order -> package_id;
		if($package_id)
		{
			$action_type = 'publish_businesses';
		}
		else
		{
			$action_type = 'feature_businesses';
		}
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", $action_type)->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
		$this-> view -> item = $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $item_id);
	    $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
	          array(
	            'action' => 'place-order',
	            'id' => $item_id,
	            'packageId' => $order -> package_id
	          ), 'ynbusinesspages_general', true);
	    //publish fee
        $this -> view -> total_pay = $total_pay =  $order -> price ;    
        $credits = ceil(($total_pay * $defaultPrice * $numbers));
        $this -> view -> cancel_url = $cancel_url;
        $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
        if (!$balance) 
        {
          $currentBalance = 0;
        } else 
        {
          $currentBalance = $balance->current_credit;
        }
        $this->view->currentBalance = $currentBalance;
        $this->view->credits = $credits;
        $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    
        // Check method
        if (!$this->getRequest()->isPost()) 
        {
          return;
        }
    
        // Insert member transaction
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynbusinesspages');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
			//add feature
			$description = "";
			if($package_id)
			{
				Engine_Api::_() -> ynbusinesspages() -> buyBusiness($business->getIdentity(), $order -> package_id);
				$description = $this ->view ->translate('Buy business');
				/**
		         * Call Event from Affiliate
		         */
				if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
				{
					$params['module'] = 'ynbusinesspages';
					$params['user_id'] = $order->user_id;
					$params['rule_name'] = 'publish_business';
					$params['total_amount'] = $order->price;
					$params['currency'] = $order->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			else 
			{
				Engine_Api::_() -> ynbusinesspages() -> featureBusiness($business->getIdentity(), $order -> feature_day_number);
				$description = $this ->view ->translate('Feature business');
				/**
		         * Call Event from Affiliate
		         */
				if(Engine_Api::_() -> hasModuleBootstrap('ynaffiliate'))	
				{
					$params['module'] = 'ynbusinesspages';
					$params['user_id'] = $order->user_id;
					$params['rule_name'] = 'feature_business';
					$params['total_amount'] = $order->price;
					$params['currency'] = $order->currency;
		        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
				}
		        /**
		         * End Call Event from Affiliate
		         */
			}
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => '-3',
		     	'amount' => $order->price,
		     	'currency' => $order->currency,
		     	'user_id' => $order->user_id,
		     	'item_id' => $order->item_id,
		     	'description' => $description,
			 ));
			 
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), $action_type, $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $business->getIdentity(), 'slug' => $business -> getSlug()), 'ynbusinesspages_profile', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
	
    public function printAction() {
        $this -> _helper -> layout -> setLayout('default-simple');
        $businessId = $this->_getParam('id', null);
        if (is_null($businessId)) {
            return;
        }
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
        if (is_null($business)) {
            return;
        }
        if (!Engine_Api::_()->core()->hasSubject('ynbusinesspages_business'))
        {
            Engine_Api::_()->core()->setSubject($business);
        }
        $this->view->business = $business;
    }
    	
	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynbusinesspages_general', true), 'messages' => array($message)));
	}
	
	protected function _checkEnoughCredits($credits)
	{
		$balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
		if (!$balance) {
			return false;
		}
		$currentBalance = $balance->current_credit;
		if ($currentBalance < $credits) {
			return false;
		}
		return true;
	}
	
	public function loginAsBusinessAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if (!$this -> getRequest() -> isPost()) 
		{
			$params['user_id'] = $viewer -> getIdentity();
			$table = Engine_Api::_() -> getDbTable('business', 'ynbusinesspages');
			$select = $table -> getBusinessesSelect($params);
			$this -> view -> business = $table -> fetchAll($select);
			return;
		}
		$businessId = @$_POST['business_item'];
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
		// Action login as business
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$business_session -> businessId = $businessId;
		
		if(!empty($_POST['smoothbox']))
		{
			// Just redirect to business detail
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $business -> getHref(),
				'format' => 'smoothbox',
				'messages' => array($this->view->translate("Login as Business Successfully!"))
				));
		}
		else 
		{
			return $this->_helper->redirector->gotoUrl($business -> getHref());
		}
	}
	public function logoutBusinessAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$business_session -> unsetAll();
		$uri = $this->_getParam('return_url');
		if($uri)
		{
			if( substr($uri, 0, 3) == '64-' ) 
			{
	        	$uri = base64_decode(substr($uri, 3));
	        }
        	return $this->_redirect($uri, array('prependBase' => false));
		}
		return $this->_helper->redirector->gotoRoute(array(), 'ynbusinesspages_general', true);
	}
	
	public function warningAction()
	{
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view ->  return_url = $this->_getParam('return_url');
	}
	
	public function getCategoryAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$category_id = $this ->_getParam('id');
		
		$table = Engine_Api::_() -> getDbTable('categories', 'ynbusinesspages');
		$category = $table -> getNode($category_id);
		$i = 0;
		$return_str = "";
		if($category)
		{
			foreach($category->getBreadCrumNode() as $node)
			{
				if($node -> category_id != 1)
				{
				 	if($i != 0)
						$return_str .= "&raquo";	
        		    $i++; 
					$return_str .= "<a href= '".$node->getHref()."'>".$node->shortTitle()."</a>";
        		}
	     	 }
	     	 if($category -> parent_id != 0 && $category -> parent_id  != 1)
					$return_str .= "&raquo";	
			 $return_str .= "<a href= '".$category->getHref()."'>".$category->getTitle()."</a>";
     	}
     	 
		echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => $return_str));
		exit ;
	}
    
    public function displayMapViewAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $businessIds = $this->_getParam('ids', '');
        if ($businessIds != '')
        {
            $businessIds = explode("_", $businessIds);
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table -> select();
        
        if (is_array($businessIds) && count($businessIds))
        {
            $select -> where ("business_id IN (?)", $businessIds);
        }
        else 
        {
            $select -> where ("business_id IN (0)");
        }
        $businesses = $table->fetchAll($select);
            
        $datas = array();
        $contents = array();
        $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ;
        $view = Zend_Registry::get("Zend_View");
        $locationArr = array();
        foreach($businesses as $business) {
            $mainLocation = $business->getMainLocation(true);           
            if(!is_null($mainLocation) && $mainLocation -> latitude) {               
                $icon = $view->layout()->staticBaseUrl.'application/modules/Ynbusinesspages/externals/images/maker.png';
                $key = "{$mainLocation -> latitude},{$mainLocation -> longitude}";
                $locationArr[$key][] = $mainLocation;
            }
        }
        
        foreach ($locationArr as $locationList) {
            if (count($locationList) == 1) {
                $location = $locationList[0];
                $datas[] = array(   
                        'business_id' => $location -> business_id,              
                        'latitude' => $location -> latitude,
                        'longitude' => $location -> longitude,
                        'icon' => $icon
                );
                $business = Engine_Api::_()->getItem('ynbusinesspages_business', $location -> business_id);
                if (!$business) continue;
                $contents[] = '
                    <div class="ynbusinesspages-maps-main" style="overflow: hidden;">  
                        <div class="ynbusinesspages-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">                          
                            <div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
                                '. $view->itemPhoto($business, "thumb.icon") .'
                            </div>                              
                            <a href="'.$business->getHref().'" class="ynbusinesspages-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
                                '.$business->getTitle().'
                            </a>
                        </div>
                    </div>
                ';
            }
            else if (count($locationList) > 1)
            {
                $location = $locationList[0];
                $datas[] = array(   
                        'business_id' => $location -> business_id,              
                        'latitude' => $location -> latitude,
                        'longitude' => $location -> longitude,
                        'icon' => $icon
                );
                $str = '<div>' . count($locationList) . $view->translate(" businesses") . '</div>';
                foreach ($locationList as $location){
                    $business = Engine_Api::_()->getItem('ynbusinesspages_business', $location -> business_id);
                    if (!$business) continue;
                    $str .= '
                        <div class="ynbusinesspages-maps-main" style="overflow: hidden;">  
                            <div class="ynbusinesspages-maps-content" style="overflow: hidden; line-height: 20px; width: auto; white-space: nowrap;">                          
                                <div style="margin-right: 5px; font-size: 11px;margin-right: 5px;font-size: 11px;height: 48px;width: 48px; overflow: hidden">
                                    '. $view->itemPhoto($business, "thumb.icon") .'
                                </div>                              
                                <a href="'.$business->getHref().'" class="ynbusinesspages-maps-title" style="color: #679ac0; font-weight: bold; font-size: 12px; text-decoration: none;" target="_parent">
                                    '.$business->getTitle().'
                                </a>
                            </div>
                        </div>
                    ';
                }
                $contents[] = $str;
            }
        }
        
        echo $this ->view -> partial('_map_view.tpl', 'ynbusinesspages',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
        exit();
    }	
    
	public function businessBadgeAction()
	{
		$this -> _helper -> layout -> setLayout('default-simple');
		$business_id = $this -> _getParam('business_id');
		$this -> view -> status = $status = $this -> _getParam('status');
		$aStatus = str_split($status);
		$name = 0;
		$description = 0;
		$led = 0;
		if (count($aStatus) == 3)
		{
			if ($aStatus[0] == '1')
				$name = 1;
			if ($aStatus[1] == '1')
				$description = 1;
			if ($aStatus[2] == '1')
				$led = 1;
		}
		$this -> view -> name = $name;
		$this -> view -> description = $description;
		$this -> view -> led = $led;

		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
		if (!$business)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> business = $business;
	}
    
	public function composeMessageAction()
	{
		// Make form
		$this->view->form = $form = new Messages_Form_Compose();
		$form -> setDescription('Create your new message with the form below. Your message will be sent to business owner.');
		// Get params
		$multi = $this->_getParam('multi');
		$to = $this->_getParam('to');
		$viewer = Engine_Api::_()->user()->getViewer();
		$toObject = null;

		// Build
		$isPopulated = false;
		if( !empty($to) && (empty($multi) || $multi == 'user') ) {
			$multi = null;
			// Prepopulate user
			$toUser = Engine_Api::_()->getItem('user', $to);
			/*
			$isMsgable = ( 'friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
			$viewer->membership()->isMember($toUser) );
			*/
			$isMsgable = true;
			if( $toUser instanceof User_Model_User &&
			(!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
			isset($toUser->user_id) &&
			$isMsgable ) {
				$this->view->toObject = $toObject = $toUser;
				$form->toValues->setValue($toUser->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		} else if( !empty($to) && !empty($multi) ) {
			// Prepopulate group/event/etc
			$item = Engine_Api::_()->getItem($multi, $to);
			// Potential point of failure if primary key column is something other
			// than $multi . '_id'
			if( $item instanceof Core_Model_Item_Abstract &&
			$item->getIdentity() && (
			$item->isOwner($viewer) ||
			$item->authorization()->isAllowed($viewer, 'edit')
			)) {
				$this->view->toObject = $toObject = $item;
				$form->toValues->setValue($item->getGuid());
				$isPopulated = true;
			} else {
				$multi = null;
				$to = null;
			}
		}
		$this->view->isPopulated = $isPopulated;

		// Build normal
		if( !$isPopulated ) {
			// Apparently this is using AJAX now?
			//      $friends = $viewer->membership()->getMembers();
			//      $data = array();
			//      foreach( $friends as $friend ) {
			//        $data[] = array(
			//          'label' => $friend->getTitle(),
			//          'id' => $friend->getIdentity(),
			//          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
			//        );
			//      }
			//      $this->view->friends = Zend_Json::encode($data);
		}

		// Assign the composing stuff
		$composePartials = array();
		foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
			if( empty($data['composer']) ) {
				continue;
			}
			foreach( $data['composer'] as $type => $config ) {
				// is the current user has "create" privileges for the current plugin
				$isAllowed = Engine_Api::_()
				->authorization()
				->isAllowed($config['auth'][0], null, $config['auth'][1]);

				if( !empty($config['auth']) && !$isAllowed ) {
					continue;
				}
				$composePartials[] = $config['script'];
			}
		}
		$this->view->composePartials = $composePartials;
		// $this->view->composePartials = $composePartials;

		// Get config
		$this->view->maxRecipients = $maxRecipients = 10;


		// Check method/data
		if( !$this->getRequest()->isPost() ) {
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
		}

		// Process
		$db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
		$db->beginTransaction();

		try {
			// Try attachment getting stuff
			$attachment = null;
			$attachmentData = $this->getRequest()->getParam('attachment');
			if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
				$type = $attachmentData['type'];
				$config = null;
				foreach( Zend_Registry::get('Engine_Manifest') as $data )
				{
					if( !empty($data['composer'][$type]) )
					{
						$config = $data['composer'][$type];
					}
				}
				if( $config ) {
					$plugin = Engine_Api::_()->loadClass($config['plugin']);
					$method = 'onAttach'.ucfirst($type);
					$attachment = $plugin->$method($attachmentData);
					$parent = $attachment->getParent();
					if($parent->getType() === 'user'){
						$attachment->search = 0;
						$attachment->save();
					}
					else {
						$parent->search = 0;
						$parent->save();
					}
				}
			}

			$viewer = Engine_Api::_()->user()->getViewer();
			$values = $form->getValues();

			// Prepopulated
			if( $toObject instanceof User_Model_User ) {
				$recipientsUsers = array($toObject);
				$recipients = $toObject;
				// Validate friends
				/*
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					if( !$viewer->membership()->isMember($recipients) ) {
						return $form->addError('One of the members specified is not in your friends list.');
					}
				}
				*/
			} else if( $toObject instanceof Core_Model_Item_Abstract &&
			method_exists($toObject, 'membership') ) {
				$recipientsUsers = $toObject->membership()->getMembers();
				//        $recipients = array();
				//        foreach( $recipientsUsers as $recipientsUser ) {
				//          $recipients[] = $recipientsUser->getIdentity();
				//        }
					$recipients = $toObject;
			}
			// Normal
			else {
				$recipients = preg_split('/[,. ]+/', $values['toValues']);
				// clean the recipients for repeating ids
				// this can happen if recipient is selected and then a friend list is selected
				$recipients = array_unique($recipients);
				// Slice down to 10
				$recipients = array_slice($recipients, 0, $maxRecipients);
				// Get user objects
				$recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
				// Validate friends
				if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
					foreach( $recipientsUsers as &$recipientUser ) {
						if( !$viewer->membership()->isMember($recipientUser) ) {
							return $form->addError('One of the members specified is not in your friends list.');
						}
					}
				}
			}

			// Create conversation
			$conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
			$viewer,
			$recipients,
			$values['title'],
			$values['body'],
			$attachment
			);

			// Send notifications
			foreach( $recipientsUsers as $user ) {
				if( $user->getIdentity() == $viewer->getIdentity() ) {
					continue;
				}
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
				$user,
				$viewer,
				$conversation,
          'message_new'
          );
			}

			// Increment messages counter
			Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

			// Commit
			$db->commit();
		} catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}

		if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'smoothboxClose' => true,
			));
		} else {
			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
		        'redirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
			));
		}
	}
	
}
