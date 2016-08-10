<?php
class Ynbusinesspages_BusinessController extends Core_Controller_Action_Standard
{
	public function init() {
		
		if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
		{
			Engine_Api::_() -> core() -> setSubject($business);
		}
		$this -> _helper -> requireSubject('ynbusinesspages_business');
	}
	
	public function openCloseAction()
	{
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_() -> core() -> getSubject();
		if (!$business)
		{
			return $this->_helper->requireSubject()->forward();
		}
		if(in_array($business -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		if(!($viewer -> isSelf($business -> getOwner())))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		if($business -> status == 'published')
		{
			$status = 'closed';
			$action = Zend_Registry::get('Zend_Translate') -> _('close');
			$message = Zend_Registry::get('Zend_Translate') -> _('The selected business has been closed.');
		}
		elseif($business -> status == 'closed')
		{
			$status = 'published';
			$action = Zend_Registry::get('Zend_Translate') -> _('published'); 
			$message = Zend_Registry::get('Zend_Translate') -> _('The selected business has been published.');
		}
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_OpenClose();
		
		$title = ucfirst($action)." ".Zend_Registry::get('Zend_Translate') -> _('Business');
		$desc = Zend_Registry::get('Zend_Translate') -> _('Are you sure you want to')." ".$action." ".Zend_Registry::get('Zend_Translate') -> _('this business?');
		$form->setTitle($title)
     		 ->setDescription($desc);
		$form->submit->setLabel(ucfirst($action));
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$db = $business -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$business -> status = $status;
			$business -> save();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> _forward('success', 'utility', 'core', array(
				'closeSmoothbox' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _($message))
		));
	}
	
	public function editAction()
	{
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		//Get business
		$business = Engine_Api::_() -> core() -> getSubject();
        
        // Check authorization to edit business.
        if (!$business->isEditable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//getPackage
		$this -> view -> package = $package = $business -> getPackage();
	
		//get main 
		$tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
	    $tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
		$main_category = $tableCategoryMap -> getMainCategoryByBusinessId($business -> getIdentity());
		
		$category_id = $this -> _getParam('category_id', $main_category -> category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynbusinesspages_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynbusinesspages_business');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}
		
		if(($package -> getIdentity() > 0) && ($package -> allow_owner_add_customfield))
		{
			//get Company Additional Info
			$tableBusinessInfo = Engine_Api::_() -> getDbTable('businessinfos', 'ynbusinesspages');
			$businessInfo = $tableBusinessInfo -> getRowsInfoByBusinessId($business -> getIdentity());
			$this -> view -> number_add_more  = $number_add_more = count($businessInfo);
			$this -> view -> number_add_more_index  = $number_add_more_index = $number_add_more;
			
			//get location
			$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
			$locations = $tableLocation -> getSubLocationsByBusinessId($business -> getIdentity());
			$this -> view -> number_location  = $number_location = count($locations);
			$this -> view -> number_location_index  = $number_location_index = $number_location; 
			
			if ($this -> getRequest() -> isPost())
			{
				$posts = $this -> getRequest() -> getPost();
				$number_add_more = $posts['number_add_more'];
				$this -> view -> number_add_more_index = $number_add_more_index = $posts['number_add_more_index'];
				$businessInfo = array();
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
			
			$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Edit( array(
				'formArgs' => $formArgs,
				'item' => $business,
				'businessInfo' => $businessInfo,
			));
			
			//addtional info
			if($number_add_more == 0)
			{
				$form -> number_add_more -> setValue(1);
				$form -> number_add_more_index -> setValue(1);
			}
			else
			{
				$form -> number_add_more -> setValue($number_add_more);
				$form -> number_add_more_index -> setValue($number_add_more_index);
			}
			
		}
		else
		{
			$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Edit( array(
				'formArgs' => $formArgs,
				'item' => $business,
			));
		}
		
		//get location
		$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
		$locations = $tableLocation -> getSubLocationsByBusinessId($business -> getIdentity());
		$this -> view -> number_location  = $number_location = count($locations);
		$this -> view -> number_location_index  = $number_location_index = $number_location; 
		
		if ($this -> getRequest() -> isPost())
		{
			$posts = $this -> getRequest() -> getPost();
			$this -> view -> number_location_index = $number_location_index = $posts['number_location_index'];
		}
		
		//add locations
		if($number_location == 0)
		{
			$form -> number_location -> setValue(0);
			$form -> number_location_index -> setValue(0);
		}
		else
		{
			$form -> number_location -> setValue($number_location);
			$form -> number_location_index -> setValue($number_location_index);
		}
		
		if ($business->status == 'draft') {
			$form->removeElement('category_id');
		}
		else {
			
			// Populate category list.
			$categories = $tableCategory -> getCategories();
			unset($categories[0]);
			if($package -> getIdentity() > 0)
			{
				foreach ($categories as $item) {
					if (in_array($item['category_id'], $package->category_id))
						$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
				}
			}
			else if($business->status == 'unclaimed' && $business->is_claimed == 1)
			{
				foreach ($categories as $item) {
					$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
				}
			}
	
			//populate category
			if ($category_id) {
				$form -> category_id -> setValue($category_id);
			} else {
				$form -> addError('Create business require at least one category. Please contact admin for more details.');
			}
			
			//populate sub industry
			$sub_categories = $tableCategoryMap -> getSubCategoryByBusinessId($business -> getIdentity());
			$this -> view -> sub_categories = $sub_categories;
		}
		
		//populate when postback
	    $posts = $this -> getRequest() -> getPost();
		if($posts)
		{
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
		}
		else 
		{
			$form -> populate($business -> toArray());
			
			//phone, fax , web address
			$form -> phone -> setValue($business -> phone['0']);
			$form -> fax -> setValue($business -> fax['0']);
			$form -> web_address -> setValue($business -> web_address['0']);
			$arr_phone = $business -> phone;
			$arr_fax = $business -> fax;
			$arr_web_address = $business -> web_address;
			//unset
			unset($arr_phone['0']);
			unset($arr_fax['0']);
			unset($arr_web_address['0']);			
			$this -> view -> sub_phones = $arr_phone;
			$this -> view -> sub_faxs = $arr_fax;
			$this -> view -> sub_web_addresses = $arr_web_address;
			
			//operating hours
			$tableHours = Engine_Api::_() -> getDbTable('operatinghours', 'ynbusinesspages');
			$operatingHours = $tableHours -> getHoursByBusinessId($business->getIdentity());
			foreach($operatingHours as $hour)
			{
				$name = $hour -> day;
				$name_to = $name.'_to';
				$name_from = $name.'_from';
				$form -> $name_to -> setValue($hour -> to);
				$form -> $name_from -> setValue($hour -> from);
			}
			
			//founder
			$tableFounder = Engine_Api::_() -> getDbTable('founders', 'ynbusinesspages');
			$founders = $tableFounder -> getFoundersByBusinessId($business->getIdentity());
			$this -> view -> founders = $founders;
			
			//location
			$tableLocation = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
			$mainLocation = $tableLocation ->  getMainLocationByBusinessId($business->getIdentity());
			$this -> view -> mainLocation = $mainLocation;
			$locations = $tableLocation -> getSubLocationsByBusinessId($business -> getIdentity());
			$this -> view -> locations = $locations;
			$form -> number_location -> setValue(count($locations));
			
		}
		
		//tags
		$tagStr = '';
		foreach ($business->tags()->getTagMaps() as $tagMap)
		{
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));

		//return if not click submit or save draft
		$submit_button = $this -> _getParam('submit_button');
		if (!isset($submit_button))
		{
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
		
		//check email
		$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
		if (!preg_match($regexp, $values['email'])) {
			$form -> addError('Please enter valid email!');
			return;
		}
		
		//check exsiting name & email
		$businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$checkExist = $businessTable -> getBusinessByNameEmail($values['name'], $values['email']);
		//check when changing email or name
		if(($values['name'] != $business -> name) || ($values['email'] != $business -> email))
		{
			if(!empty($checkExist))
			{
				$form -> addError('Your business name and email are already existing!');
				return;
			}
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
		
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			//save business
      		$business->setFromArray($values);
			$business -> save();
			
			//Handle tags
			$tags = preg_split('/[,]+[\s]+[,]+|[,]+/', trim($values['tags']));
			$business -> tags() -> setTagMaps($viewer, $tags);

			$search_table = Engine_Api::_() -> getDbTable('search', 'core');
			$select = $search_table -> select() -> where('type = ?', 'ynbusinesspages_pages') -> where('id = ?', $business -> getIdentity());
			$row = $search_table -> fetchRow($select);
			if ($row)
			{
				$row -> keywords = $values['tags'];
				$row -> save();
			}
			
			//Set Operation hours
			$arr_days = array('monday', 'tuesday',  'wednesday',
  							  'thursday', 'friday', 'saturday',
  								'sunday');
			$tableOperationHour = Engine_Api::_() -> getDbTable('operatinghours', 'ynbusinesspages');
			//delete all before insert
			$tableOperationHour -> deleteAllHoursByBusinessId($business -> getIdentity());
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
			//delete all before insert
			$tableLocation -> deleteAllLocationsByBusinessId($business -> getIdentity());
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
				$locationRow -> title = strip_tags($this->_getParam('location_title'));
				$locationRow -> location = $values['location_address'];
				$locationRow -> latitude = $values['lat'];
				$locationRow -> longitude = $values['long'];
				$locationRow -> main = true;
				$locationRow -> save();
			}
			
			//Set Founders
			$tableFounder = Engine_Api::_() -> getDbTable('founders', 'ynbusinesspages');
			//delete all before insert
			$tableFounder -> deleteAllFoundersByBusinessId($business -> getIdentity());
			$toValues = $this ->_getParam('toValues');
			if(!empty($toValues))
			{
				$founders = explode(",", $toValues);
				foreach($founders as $founder)
				{
					$founderRow = $tableFounder -> createRow();
					$founderRow -> business_id = $business -> getIdentity();
					if(is_numeric($founder))
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
				//delete all before insert
				$tableBusinessInfo -> deleteAllInfoByBusinessId($business -> getIdentity());
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
				//delete all before insert
				$tableCategoryMap -> deleteCategoriesByBusinessId($business -> getIdentity());
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
			
			// Commit
			$db -> commit();
			
			//send notice to followers
			$tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
			$followers = $tableFollow -> getUsersFollow($business -> getIdentity());
			if($followers)
			{
				foreach($followers as $follower)
				{
					Engine_Api::_() -> getDbTable('notifications', 'activity') -> addNotification($follower, $business, $business, 'ynbusinesspages_edited');
				}
			}

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		$fromAdmin = $this ->_getParam('admin');
		if (isset($submit_button))
		{
			if(!empty($fromAdmin))
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'module' => 'ynbusinesspages',
						'controller' => 'businesses',
						'action' => 'index',
					), 'admin_default', true),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
			else 
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'id' => $business -> getIdentity(),
						'slug' => $business -> getSlug(),
					), 'ynbusinesspages_profile', true),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
		}
		
	}
	
	public function deleteAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('business_id'));
		
		// Check authorization to delete business.
		if (!$business->isDeletable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Delete();
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

		$db = $business -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$business -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The selected business has been deleted.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynbusinesspages_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function transferAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> business_id = $business_id = $this -> _getParam('business_id');
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
		
		$fromAdmin = $this ->_getParam('admin');
		if(!$fromAdmin)
		{
			$this -> _helper -> content -> setEnabled();
		}
		if (!$business)
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		if (!$viewer -> isAdmin() && !$business -> isOwner($viewer))
		{
			return $this -> _helper -> requireAuth -> forward();
		}

		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Transfer(array('fromAdmin' => $fromAdmin));
		
		
		if (!$this -> getRequest() -> getPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		//Process
		$values = $form -> getValues();
		
		 //check max businesses user can create
        $user = Engine_Api::_()->getItem('user', $values['toValues']);
		if(!$user -> getIdentity())
		{
			$error_message = Zend_Registry::get('Zend_Translate') -> _('Cannot find this user.');
        	$form -> addError($error_message);
			$form -> toValues -> setValue('');
			return;
		}
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynbusinesspages_business', $user->level_id, 'max');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $user->level_id)
                ->where('type = ?', 'ynbusinesspages_business')
                ->where('name = ?', 'max'));
            if ($row) {
                $max = $row->value;
            }
        }
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $table->select()
            -> where('user_id = ?', $user->getIdentity())
			-> where('is_claimed <> ?', 1)
            -> where('deleted <> ?', 1);
            
        $raw_data = $table->fetchAll($select);
        if (sizeof($raw_data) >= $max && $max != 0) 
        {
        	$error_message = Zend_Registry::get('Zend_Translate') -> _('This user has reached the limit of businesses. Please choose another user.');
        	$form -> addError($error_message);
			return;
        }
		$db = Engine_Api::_() -> getDbtable('business', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();
		$member = Engine_Api::_() -> user() -> getUser($values['toValues']);
		
		try
		{
			//update owner
			$user_id = $values['toValues'];
			$user = Engine_Api::_() -> getItem('user', $user_id);
			$business -> transferOwner($user);
			
			//delete all claims if exsist
			$tableClaim = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');
			$tableClaim -> denyAllClaims($business -> getIdentity());
			
			//TODO remove 
			/*$list = $group -> getOfficerList();
			$list -> remove($member);*/
			
			// Add action
			/*
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($member, $group, 'advgroup_transfer');
			*/
			
			//Add notification
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($member, $business, $business, 'ynbusinesspages_transfer_owner');
			
			$db -> commit();
		}
		catch(Exception $e)
		{
			$db -> rollback();
			throw $e;
		}
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$business_session -> unsetAll();
		
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$callbackUrl = $this -> view -> url(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()), 'ynbusinesspages_profile', true);
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $callbackUrl,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new business owner had been set.'))
			));
		}
		else
		{
			if($fromAdmin)
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'closeSmoothbox' => true,
					'parentRefresh' => true,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new business owner had been set.')),
				));
			}
			else 
			{
				$callbackUrl = $this -> view -> url(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()), 'ynbusinesspages_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new business owner had been set.'))
				));
			}
		}
	}

    public function tooltipAction() {
        $typeShow = $this->_getParam('type_show');
        $id = $this->_getParam('business_id');
        $type = $this->_getParam('type');
        if ($typeShow == 'ajax') {
            $json = array(
                'error' => 0,
                'html' => '',
                'message' => ''
            );
            $this -> _helper -> layout -> disableLayout();
            $this -> _helper -> viewRenderer -> setNoRender(true);
            if (is_null($id) || is_null($type)) {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
                echo Zend_Json::encode($json);
                return;
            }
            if (in_array($type, array('phone', 'website', 'location'))) {
                $method = 'renderBusiness' . ucfirst($type);
                $json['html'] = Engine_Api::_()->ynbusinesspages()->$method($id);
            }
            else {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            }
            echo Zend_Json::encode($json);
            return;
        }
    }
    
    public function addToCompareAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $id = $this->_getParam('business_id');
        $json = array(
            'error' => 0,
            'message' => ''
        );
        if (is_null($id)) {
            $json['error'] = 1;
            $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            echo Zend_Json::encode($json);
            return;
        }
        
        $value = $this->_getParam('value');
        if ($value) {
            $count = Engine_Api::_()->ynbusinesspages()->addBusinessToCompare($id);
            $max = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynbusinesspages_max_comparison', 5);
            if ( $count === false) {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            }
            else if ($count > $max) {
                Engine_Api::_()->ynbusinesspages()->removeComparebusiness($id, null);
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Reach limit! You can not add this business to compare.');
            } 

        }
        else {
            if (Engine_Api::_()->ynbusinesspages()->removeComparebusiness($id, null) === false) {
                $json['error'] = 1;
                $json['message'] = Zend_Registry::get('Zend_Translate') -> _('Request not found!');
            }
        }
        echo Zend_Json::encode($json);
        return;
    } 
	
	public function profileFollowAction() 
    {
    	$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $business = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        $row = $followTable->getFollowBusiness($business->getIdentity(), $viewer->getIdentity());
		$option_id = $this->getRequest()->getParam('option_id', 1);
        if ($option_id) 
        {
           if(!$row)
		   {
		   		$row = $followTable->createRow();
			    $row->business_id = $business->getIdentity();
			    $row->user_id = $viewer->getIdentity();
				$row->creation_date = date('Y-m-d H:i:s');
				$row -> save();
				
				$business -> follow_count = $business -> follow_count + 1;
				$business -> save();
		   }
        } 
		else if($row)
		{
			$row -> delete();
			$business -> follow_count = $business -> follow_count - 1;
			$business -> save();
		}
    }

	public function profileFavouriteAction() 
    {
    	$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $business = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $favouriteTable = Engine_Api::_()->getDbTable('favourites', 'ynbusinesspages');
        $row = $favouriteTable->getFavouriteBusiness($business->getIdentity(), $viewer->getIdentity());
        $option_id = $this->getRequest()->getParam('option_id', 1);
        if ($option_id) 
        {
           if(!$row)
		   {
		   		$row = $favouriteTable->createRow();
			    $row->business_id = $business->getIdentity();
			    $row->user_id = $viewer->getIdentity();
				$row -> save();
		   }
        } 
		else if($row)
		{
			$row -> delete();
		}
    }
	
	public function unFollowAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_()->core()->getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($business, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('business', 'ynbusinesspages') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$viewer = Engine_Api::_()->user()->getViewer();
        	$followTable = Engine_Api::_()->getDbTable('follows', 'ynbusinesspages');
        	$row = $followTable->getFollowBusiness($business->getIdentity(), $viewer->getIdentity());
			if ($row)
			{
				$row -> delete();
				$business -> follow_count = $business -> follow_count - 1;
				$business -> save();
				$db -> commit();
			}
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Unfollow successfully.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}
	public function unFavouriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_()->core()->getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($business, $viewer, 'view') -> isValid())
		{
			return;
		}
		$favouriteTable = Engine_Api::_()->getDbTable('favourites', 'ynbusinesspages');
		$db = $favouriteTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$viewer = Engine_Api::_()->user()->getViewer();
        	
        	$row = $favouriteTable->getFavouriteBusiness($business->getIdentity(), $viewer->getIdentity());
			if ($row)
			{
				$row -> delete();
				$db -> commit();
			}
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Unfavourite successfully.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}
    
	public function changeRoleAction()
  	{
  		$businessId = $this->_getParam('business_id', 0);
  		$userId = $this->_getParam('user_id', 0);
  		if (!$businessId || !$userId)
  		{
  			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid Paramenters.");
			return;
  		}
  		$tabId = $this->_getParam('tab');
  		$user = Engine_Api::_()->user()->getUser($userId);
  		$business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
  		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		
		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Role_Change(array(
			'business' => $business,
			'user' => $user
		));
		
		if (!$business)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists to set role");
			return;
		} 
  		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid( $this -> getRequest() -> getPost())) {
			return;
		}
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try 
		{
			$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
			$values = $form -> getValues();
			
			$oldList = $listTbl -> getListByUser($user, $business);
			if (!is_null($oldList))
			{
				$oldList->remove($user);	
			}
			
			$list = Engine_Api::_()->getItem('ynbusinesspages_list', $values['role_id']);
			if (!is_null($list))
			{
				$list->add($user);
			}
	
			$membershipTbl = Engine_Api::_()->getDbTable('membership', 'ynbusinesspages');
			$membership = $membershipTbl -> getMemberInfo($business, $user);
			$membership -> list_id = $list -> getIdentity();
			$membership -> save();
			
			// Commit
			$db -> commit();
	  		
	  		return $this->_forward('success', 'utility', 'core', array(
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changed Role Successfully.')),
		      'layout' => 'default-simple',
		      'parentRedirect' => $this->view->url(array('id' => $businessId), 'ynbusinesspages_profile') . ( ($tabId && is_numeric($tabId)) ? "?tab={$tabId}" : "" ),
		      'closeSmoothbox' => true,
		    ));
		} 
		catch (Exception $e) 
		{
			$db -> rollBack();
			throw $e;
		}
  	}
  	 
  	public function promoteAction() {
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('business_id'));
  		if (!$business) {
  			return $this -> _helper -> requireAuth -> forward();
  		}
  		// In smoothbox
  		$this -> _helper -> layout -> setLayout('default-simple');
  		// Make form
  		$this -> view -> business = $business;
  	}
  	
	public function checkinAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('business_id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Business_Checkin();
		if (!$business)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists to check in");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $business -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$business -> checkin($viewer);
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You have checked in this business.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $business->getIdentity()), 'ynbusinesspages_profile', true),
			'messages' => Array($this -> view -> message)
		));
	}

    public function removeItemAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        
        $itemType = $this->_getParam('item_type');
        $itemId = $this->_getParam('item_id');
        $label = $this->_getParam('item_label');
        if (is_null($label)) {
            $label = $this->view->translate('Item');
        }
        else {
            $label = $this->view->translate($label);
        }
        $deleteAction = $this->_getParam('remove_action');
        $this -> view -> business = $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this->_getParam('business_id'));  
        if (is_null($itemType) || is_null($itemId) || is_null($deleteAction) || is_null($business)) {
            $this->view->status = false;
            $this->view->error = $this->view->translate('Your request is invalid.');
            return;
        }
        $this-> view->form = $form = new Ynbusinesspages_Form_Business_RemoveItem(array('label' => $label));
        if ($itemType == 'yncontest_contest') $itemType = 'contest';
        $item = Engine_Api::_()->getItem($itemType, $itemId);
        if (!$business -> isAllowed($deleteAction, null, $item)) {
            $this->view->status = false;
            $this->view->error = $this->view->translate('You don\'t have permission to delete this %s.', $label);
            return;
        }

        if( !$this->getRequest()->isPost()) {
            return;
        }
        
        $params = array (
            'type' => $itemType,
            'item_id' => $itemId
        );
        
        $result = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> deleteItem($params);
		if ($itemType == 'ynmusic_album') {
			$songs = $item->getSongs();
			foreach ($songs as $song) {
				$p = array (
		            'type' => $song->getType(),
		            'item_id' => $song->getIdentity()
		        );
				Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> deleteItem($p);
			}
		}
        if($result != "true") {
            return $this -> _forward('success', 'utility', 'core', array(
                'messages' => array($this->view->translate('Can not delele this %s', $label)),
                'layout' => 'default-simple',
                'parentRefresh' => false,
                'closeSmoothbox' => true,
            ));
        }
        
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array($this->view->translate('This %s has beed deleted', $label)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
            'closeSmoothbox' => true,
        ));
    }

    public function getPeopleCheckinAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this->_getParam('business_id'));
        $table = Engine_Api::_()->getDbTable('checkin', 'ynbusinesspages');
        $select = $table->select()->where('business_id = ?', $business->getIdentity());
        $rows = $table->fetchAll($select);
        $ids = array();
        $data = array();
        foreach ($rows as $row) {
            array_push($ids, $row->user_id);    
        }
        if (empty($ids)) {
            echo Zend_Json::encode(array('success' => true, 'json' => $data));
            return true;
        }
        
        $users = Engine_Api::_()->user()->getUserMulti($ids);
        
        foreach ($users as $user) {
            $data[] = array(
                'id' => $user->getIdentity(),
                'title' => $user->getTitle(),
                'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
                'url' => $user->getHref(),
            );
        }
        
        echo Zend_Json::encode(array('success' => true, 'json' => $data));
        return true;
    }
    
    public function likeCountAction()
    {
    	$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $business = Engine_Api::_() -> core() -> getSubject();
        $view = $this -> view;
        echo Zend_Json::encode(array(
        	'like_count' => $business -> like_count,
        	'html' => $view -> translate(array('<span>%1$s</span> like', '<span>%1$s</span> likes', $business -> like_count), $business -> like_count)
        ));
        exit;
    }
}
