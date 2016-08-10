<?php

class Ynjobposting_CompanyController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this -> _helper -> content -> setEnabled();
	}
  
	public function followAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $this->_getParam('id'));
		$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
		$followRow = $tableFollow -> getFollowBy($company -> getIdentity(), $viewer -> getIdentity());
		if(isset($followRow))
		{
			if($followRow -> active == 1)
			{
				$followRow -> active = 0;
			}
			else
			{
				$followRow -> active = 1;
			}
			$followRow -> save();	
		}
		else 
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$owner = $company -> getOwner();
			$notifyApi -> addNotification($owner, $viewer, $company , 'ynjobposting_company_follow');
			$followRow = $tableFollow -> createRow();
			$followRow -> user_id = $viewer -> getIdentity();
			$followRow -> company_id = $company -> getIdentity();
			$followRow -> active = 1;
			$followRow -> save();
		}
		
		$this -> _forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
			'parentRefresh' => true,
	        'format' => 'smoothbox',
	        'messages' => array($this->view->translate("Please wait..."))
	    ));
   }
  
  public function updateStatusAction()
  {
  		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
  		$view = Zend_Registry::get('Zend_View');
  		$viewer = Engine_Api::_() -> user() -> getViewer();
		$status = $this->_getParam('status');
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $this->_getParam('id'));
		$job_status = $label = "";
		
		switch ($status) {
			case 'published':
                if (!$company->isClosable()) {
                    return $this -> _helper -> requireAuth -> forward();
                }
				$label = $view -> translate('Publish');
				$job_status = 'published';
				$notificationType = 'ynjobposting_job_published';
				$notificationTypeCompany = "ynjobposting_company_published";
				break;
			case 'deleted':
                if (!$company->isDeletable()) {
                    return $this -> _helper -> requireAuth -> forward();
                }
				$label = $view -> translate('Delete');
				$job_status = 'deleted';
				$notificationType = 'ynjobposting_job_deleted';
				$notificationTypeCompany = "ynjobposting_company_deleted";
				break;
			case 'closed':
                if (!$company->isClosable()) {
                    return $this -> _helper -> requireAuth -> forward();
                }
				$label = $view -> translate('Close');
				$job_status = 'ended';
				$notificationType = 'ynjobposting_job_ended';
				$notificationTypeCompany = "ynjobposting_company_closed";
				break;
		}
			
	  	// Get form
		$this -> view -> form = $form = new Ynjobposting_Form_Company_Status(array(
			'label' => $label,
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
		//save company status
		$company -> status = $status;
		$company -> save();
		
		$company_owner = $company -> getOwner();
		$notifyApi -> addNotification($company_owner, $company, $company, $notificationTypeCompany);
	
		
		switch ($status) {
			case 'published':
			
				break;
			case 'deleted':
                $jobs = $company -> getJobs();
                foreach($jobs as $job)
                {
                    $job -> delete();
                    $owner = $job -> getOwner();
                    //send notice to job
                    $notifyApi -> addNotification($owner, $job, $job, $notificationType);
                }
                $company -> delete();
                break;
			case 'closed':
				$jobs = $company -> getJobs();
				foreach($jobs as $job)
				{
					$job -> status = $job_status;
					$job -> save();
					$owner = $job -> getOwner();
					//send notice to job
					$notifyApi -> addNotification($owner, $job, $job, $notificationType);
				}
				break;
		}
		
		if($status != 'deleted')
		{	
			$this -> _forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
				'parentRefresh' => true,
	            'format' => 'smoothbox',
	            'messages' => array($this->view->translate("Please wait..."))
	        ));
		}
		else
		{
			$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
			), 'ynjobposting_general', true);
			
			$this -> _forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
				'parentRedirect' => $redirect_url,
	            'format' => 'smoothbox',
	            'messages' => array($this->view->translate("Please wait..."))
	        ));
		}
  }
  
  public function sponsorAction()
  {
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fee_sponsorcompany = $settings->getSetting('ynjobposting_fee_sponsorcompany', 10);
		
        $company = Engine_Api::_()->getItem('ynjobposting_company', $this->_getParam('id'));
        if (!$company) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$company->isSponsorable()) {
            return $this -> _helper -> requireAuth -> forward();
        }
	  	// Get form
		$this -> view -> form = $form = new Ynjobposting_Form_Company_Sponsor(array(
			'fee' => $fee_sponsorcompany,
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
					        'controller' => 'company',
							'action' => 'place-order',
							'id' => $this->_getParam('id'),
							'number' => $this->_getParam('day'),
							), 'ynjobposting_extended', true);
							
		$this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRedirect' => $redirect_url,
            'format' => 'smoothbox',
            'messages' => array($this->view->translate("Please wait..."))
        ));
  }
  
  	public function manageAction()
  	{
  		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$page = $this->_getParam('page', 1);
  		
		$tableCompany = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$select = $tableCompany -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> where('status <> ?', 'deleted') -> order('company_id DESC');
  		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
  		$paginator -> setItemCountPerPage(5);
		$paginator -> setCurrentPageNumber($page);
		
		$sponsorTbl = Engine_Api::_()->getDbTable('sponsors', 'ynjobposting');
		$select = $sponsorTbl -> select() -> from ($sponsorTbl->info('name'), 'company_id') -> where('active = 1');
		$sponsorIds = $select -> query() -> fetchAll();
		foreach ($sponsorIds as $k => $v)
		{
			$sponsorIds[$k] = $v['company_id'];
		}
		$this->view->sponsorIds = $sponsorIds;
		
		$this -> _helper -> content	-> setEnabled();
	}
  	
	public function manageFollowAction()
  	{
  		
  		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$page = $this->_getParam('page', 1);
  		
		$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
		$select = $tableFollow -> getFollowByUserIdSelect($viewer -> getIdentity());
  		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
  		$paginator -> setItemCountPerPage(5);
		$paginator -> setCurrentPageNumber($page);
		
		$sponsorTbl = Engine_Api::_()->getDbTable('sponsors', 'ynjobposting');
		$select = $sponsorTbl -> select() -> from ($sponsorTbl->info('name'), 'company_id') -> where('active = 1');
		$sponsorIds = $select -> query() -> fetchAll();
		foreach ($sponsorIds as $k => $v)
		{
			$sponsorIds[$k] = $v['company_id'];
		}
		$this->view->sponsorIds = $sponsorIds;
		
		$this -> _helper -> content	-> setEnabled();
	}
	
	public function manageJobsAction()
  	{
  		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer->getIdentity())
		{
        	return $this -> _helper -> requireAuth -> forward();
		}
	  	$company = Engine_Api::_() -> getItem('ynjobposting_company', $this->_getParam('company_id'));
	    if (!$company->isEditable()) {
	        return $this -> _helper -> requireAuth -> forward();
	    }
	  	$this -> view -> company = $company;
	  	$this -> view -> form = $form = new Ynjobposting_Form_Jobs_Posted_Search();
	  	$form -> populate($this -> _getAllParams());
		$values = $form->getValues();
		$values['company_id'] = $company -> getIdentity();
        $values['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        //print_r($values); exit;
	    $page = $this->_getParam('page', 1);
	    $this -> view -> formValues = $values;
	    $this -> view -> paginator = $paginator = Engine_Api::_()->getItemTable('ynjobposting_job')->getJobsPaginator($values);
	    $this -> view -> paginator -> setItemCountPerPage(20);
	    $this -> view -> paginator -> setCurrentPageNumber($page);
	  	$this -> _helper -> content	-> setEnabled();
  	}
  
  public function detailAction()
  {
 	$company = Engine_Api::_() -> getItem('ynjobposting_company', $this->_getParam('id'));
    
    //check auth for view job
    if (!$company->isViewable()) {
        return $this -> _helper -> requireAuth -> forward();
    }
    
  	if (!Engine_Api::_()->core()->hasSubject('ynjobposting_company'))
	{
    	Engine_Api::_()->core()->setSubject($company);
	}
  	$this -> _helper -> content	-> setEnabled();
	
  }
  
  public function addIndustryAction()
  {
  	// Disable layout and viewrenderer
    $this -> _helper -> layout -> disableLayout();
	
	//get first industry
	$industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustries();
	$firstIndustry = $industries[1];
	
	//get current industry
	$industry = Engine_Api::_() -> getItem('ynjobposting_industry', $this->_getParam('industry_id', $firstIndustry -> getIdentity()));
	
	//get profile question
	$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynjobposting_company');
	if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
	{
		$profileTypeField = $topStructure[0] -> getChild();
		$formArgs = array(
			'topLevelId' => $profileTypeField -> field_id,
			'topLevelValue' => $industry -> option_id,
		);
	}
	//get company
	$company = null;
	$company_id = $this->_getParam('company_id');
	if(!empty($company_id))
	{
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $company_id);
	}
	$labelIndustry = 'industry_'.$this->_getParam('index');
	$labelField = 'field_'.$this->_getParam('index');
	$this -> view -> form = $form = new Ynjobposting_Form_Company_AddIndustry(array('company' => $company, 'main' => $this->_getParam('main'),'industry' => $industry, 'labelField' => $labelField, 'formArgs' => $formArgs, 'labelIndustry' => $labelIndustry));
  	
  }
  
  public function addInfoAction()
  {
  	// Disable layout and viewrenderer
    $this -> _helper -> layout -> disableLayout();
    $label_header = 'header_'.$this->_getParam('index');
    $label_content = 'content_'.$this->_getParam('index');
	$this -> view -> form = $form = new Ynjobposting_Form_Company_AddInfo(array('labelHeader' => $label_header, 'labelContent' => $label_content));
  }
  
  public function createAction()
  {
  	// Return if guest try to access to create link.
	if (!$this -> _helper -> requireUser -> isValid())
		return;
	
    // Check authorization to create company.
    if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_company', null, 'create') -> isValid())
        return;
        
	$this -> _helper -> content	-> setEnabled();
	$viewer = Engine_Api::_() -> user() -> getViewer();
	
    //get max company user can create
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_companies = $permissionsTable->getAllowed('ynjobposting', $viewer->level_id, 'max_company');
    if ($max_companies == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynjobposting')
            ->where('name = ?', 'max_company'));
        if ($row) {
            $max_companies = $row->value;
        }
    }
    $companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
    $select = $companyTbl->select()
        -> where('user_id = ?', $viewer->getIdentity())
        -> where('deleted = ?', 0);
        
    $raw_data = $companyTbl->fetchAll($select);
    if (($max_companies != 0) && (sizeof($raw_data) >= $max_companies)) {
        $this->view->error = true;
        $this->view->message = Zend_Registry::get('Zend_Translate') -> _('Your companies are reach limit. Plese delete some companies for creating new.');
        return;
    }
	
    $tableIndustry = Engine_Api::_()->getItemTable('ynjobposting_industry');
    
    $industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustries();
	$firstIndustry = $industries[1];
	$industry_id = $this -> _getParam('industry_id', $firstIndustry->industry_id);

	// Create Form
	//get current industry
	$industry = Engine_Api::_() -> getItem('ynjobposting_industry', $industry_id);
	
	//get profile question
	$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynjobposting_company');
	if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
	{
		$profileTypeField = $topStructure[0] -> getChild();
		$formArgs = array(
			'topLevelId' => $profileTypeField -> field_id,
			'topLevelValue' => $industry -> option_id,
		);
	}
    
	$posts = $this -> getRequest() -> getPost();
	$number_add_more = $posts['number_add_more'];
	$this -> view -> number_add_more_index = $number_add_more_index = $posts['number_add_more_index'];
	$companyInfo = array();
	for($i = 1; $i <= $number_add_more_index; $i++)
	{
		$header  = $posts['header_'.$i];
		$content  = $posts['content_'.$i];
		if($header != "" || $content != "")
		{ 
			$companyInfo[] = (object)array(
				'header' => $header,
				'content' => $content,
			);
		}
	}
		
	if(!empty($companyInfo))
	{
		$this -> view -> form = $form = new Ynjobposting_Form_Company_Create( array(
			'formArgs' => $formArgs,
			'companyInfo' => $companyInfo,
		));
	    $number_add_more = count($companyInfo);
		$form -> number_add_more -> setValue($number_add_more);
		$form -> number_add_more_index -> setValue($number_add_more_index);
	}
	else
	{
		$this -> view -> form = $form = new Ynjobposting_Form_Company_Create( array(
			'formArgs' => $formArgs,
		));
	}
	// Populate industry list.
	$industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustries();
	unset($industries[0]);
	foreach ($industries as $item)
	{
		$form -> industry_id -> addMultiOption($item['industry_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
	}
	
	//populate industry
	if($industry_id)
	{
		$form -> industry_id -> setValue($industry_id);
	}
	else
	{
		$form->addError('Create company require at least one industry. Please contact admin for more details.');
	}
    
    //check auth for sponsor company
    if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_company', null, 'sponsor') -> checkRequire()) {
        $form->removeElement('sponsor');        
    }
    
	//populate data
	$posts = $this -> getRequest() -> getPost();
	$form -> populate($posts);
	$this -> view -> posts = $posts;
		
	$submit_button = $this -> _getParam('submit_button');	
	if (!isset($submit_button))
	{
		return;
	}
	
	// Check method and data validity.
	$posts = $this -> getRequest() -> getPost();
	if (!$this -> getRequest() -> isPost())
	{
		return;
	}
	if (!$form -> isValid($posts))
	{
		return;
	}
	
  	//get values
	$params = $this->_getAllParams();
	$values = $form -> getValues();
	
	//check sponsor
	if($values['sponsor'] == 1)
	{
		if(empty($values['sponsor_period']))
		{
			$form -> addError('Please input valid sponsor period value.');
			return;
		}	
	}
	
	//check from & to employee value
	if($values['to'] < $values['from'])
	{
		$form -> addError('Please input valid from employee & to employee value.');
		return;
	}
	
	$regexp = "/^[_A-z0-9-]+(\.[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,3})$/";                                                                                                            
    if(!preg_match($regexp, $values['contact_email']))
    {
        $form->addError('Please enter valid email!'); 
        return ;
    }
	$db = Engine_Db_Table::getDefaultAdapter();
	$db->beginTransaction();
	try 
	{
		//save company
		$companyTable = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$company = $companyTable -> createRow();
		$company -> user_id = $viewer -> getIdentity();
		$company -> name = $values['name'];
		$company -> description = $values['description'];
		$company -> location = $values['location_address'];
		$company -> longitude = $values['long'];
		$company -> latitude = $values['lat'];
		$company -> website = $values['website'];
		$company -> from_employee = $values['from'];
		$company -> to_employee = $values['to'];
		$company -> contact_name = $values['contact_name'];
		$company -> contact_email = $values['contact_email'];
		$company -> contact_phone = $values['contact_phone'];
		$company -> contact_fax = $values['contact_fax'];
		$company -> save();
		
		// Set photo
	    if( !empty($values['photo']) ) {
	        $company->setPhoto($form -> photo);
	    }
	
		// Add Cover photo
		if (!empty($values['cover_thumb'])) {
			$company -> setCoverPhoto($form -> cover_thumb);
		}
		
		// Insert Addtional Information
		$tableCompanyInfo = Engine_Api::_() -> getDbTable('companyinfos', 'ynjobposting');
		$number_add_more_index = $params['number_add_more_index'];
		for($i = 1; $i <= $number_add_more_index; $i++)
		{
			$header  = $params['header_'.$i];
			$content  = $params['content_'.$i];
			if(!empty($header) && !empty($content))
			{
				$allowed_html = '<strong><b><em><i><u><strike><sub><sup><p><div><pre><address><h1><h2><h3><h4><h5><h6><span><ol><li><ul><a><img><embed><br><hr><object><param><iframe>';
				$infoRow = $tableCompanyInfo -> createRow();
				$infoRow -> header = strip_tags($header, $allowed_html);
				$infoRow -> content = strip_tags($content, $allowed_html);
				$infoRow -> company_id = $company -> getIdentity();
				$infoRow -> save();
			}
		}
		
		//insert industry to mapping table
		if(!empty($values['industry_id']))
		{
			$tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
			$checkIndustry = $tableIndustryMap -> checkExistIndustryByCompany($values['industry_id'], $company -> getIdentity());
			if(empty($checkIndustry))
			{
				$rowIndustryMap = $tableIndustryMap -> createRow();
				$rowIndustryMap -> company_id = $company -> getIdentity();
				$rowIndustryMap -> industry_id = $values['industry_id'];
				$rowIndustryMap -> main = true;
				$rowIndustryMap -> save();
			}
			$sub_industries = $this->_getParam('sub_industry');
			if(!empty($sub_industries))
			{
				foreach($sub_industries as $sub_industry_id)
				{
					$checkIndustry = $tableIndustryMap -> checkExistIndustryByCompany($sub_industry_id, $company -> getIdentity());
					if(empty($checkIndustry))
					{
						$rowIndustryMap = $tableIndustryMap -> createRow();
						$rowIndustryMap -> company_id = $company -> getIdentity();
						$rowIndustryMap -> industry_id = $sub_industry_id;
						$rowIndustryMap -> main = false;
						$rowIndustryMap -> save();
					}
				}
			}
		}
		
		//save custom field values of industries
		$customfieldform = $form -> getSubForm('fields');
		$customfieldform -> setItem($company);
		$customfieldform -> saveValues();
		
        //set auth for view, comment
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
        $auth_arr = array('view', 'comment');
        foreach ($auth_arr as $elem) {
            $auth_role = $values[$elem];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($company, $role, $elem, ($i <= $roleMax));
            }    
        }
        
        //send notice to admin
        $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
        $list_admin = Engine_Api::_()->user()->getSuperAdmins();
        foreach($list_admin as $admin)
        {
            $notifyApi -> addNotification($admin, $company, $company, 'ynjobposting_company_create');
        }
        
        //add activity
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($company -> getOwner(), $company, 'ynjobposting_company_create');
        if($action) {
            $activityApi->attachActivity($action, $company);
        }
        $company -> addDefaultSubmissionForm();
		// Commit
        $db -> commit();
		
		if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
        {
            Engine_Api::_()->yncredit()-> hookCustomEarnCredits($company -> getOwner(), $company -> name, 'ynjobposting_company', $company);
		}
		
    } catch (Exception $e) {
        $db -> rollBack();
        throw $e;
    }
	
	if($values['sponsor'] == 1)
	{
		return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'company',
                    'action' => 'place-order',
                    'number' => $values['sponsor_period'],
                    'id' => $company -> getIdentity()
                ), 'ynjobposting_extended', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
        ));
	}
	else 
	{
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'controller' => 'company',
				'action' => 'detail',
				'id' => $company -> getIdentity()
			), 'ynjobposting_extended', true),
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}	
	
  }
  
   public function placeOrderAction() 
    {
    	$settings = Engine_Api::_()->getApi('settings', 'core');
		$number_sponsor_day = 0;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> fee_sponsorcompany = $fee_sponsorcompany = $settings->getSetting('ynjobposting_fee_sponsorcompany', 10);
        $this -> view -> company = $company = Engine_Api::_() -> getItem('ynjobposting_company', $this ->_getParam('id'));
		$this -> view -> number_sponsor_day = $number_sponsor_day = $this ->_getParam('number', 0);
        
        if(!$company -> isSponsorable())
        {
            $message = $this -> view -> translate('You do not have permission to do this.');
        	return $this -> _redirector($message);
        }
        if (!$number_sponsor_day) {
            $message = $this -> view -> translate('Invalid sponsor day.');
        	return $this -> _redirector($message);
        }
		//Credit
        //check permission
        // Get level id
        $id = $viewer->level_id;
        if ($this -> _helper -> requireAuth() -> setAuthParams('ynjobposting', null, 'use_credit') -> checkRequire()) {
            //TODO add implement code here
            $allowPayCredit = 0;
            $credit_enable = Engine_Api::_() -> ynjobposting() -> checkYouNetPlugin('yncredit');
            if ($credit_enable)
			{
                $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
                $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", 'sponsor_company')->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
				if($type_spend)
				{
					$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
					$select = $creditTbl->select()
		                ->where("level_id = ? ", $id)
		                ->where("type_id = ?", $type_spend -> type_id)
		                ->limit(1);
		            $spend_credit = $creditTbl->fetchRow($select);
					if($spend_credit)
					{
		               $allowPayCredit = 1;
		            }
				}
			}
            $this -> view -> allowPayCredit = $allowPayCredit;
        };
        $this -> view -> total_pay = $total_pay = $fee_sponsorcompany * $number_sponsor_day;
        
	   //if package free & feature fee free????
	   if($fee_sponsorcompany == 0)
	   {
			//core - buy job
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try 
			{
				Engine_Api::_() -> ynjobposting() -> buyCompany($company->getIdentity(), $number_sponsor_day);
				$db -> commit();
			} 
			catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
			return $this ->_forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'module' => 'ynjobposting',
					'controller' => 'company',
					'action' => 'detail',
					'id' => $company->getIdentity(),
				), 'ynjobposting_extended', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Success...'))
			 ));
		}   
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if (!$gatewayTable -> getEnabledGatewayCount() && !$allowPayCredit) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
        if ($row = $ordersTable -> getLastPendingOrder()) {
            $row -> delete();
        }
		$featured = 0;
		if($number_sponsor_day)
		{
			$featured = 1;
		}
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
            $ordersTable -> insert(array(
            	'user_id' => $viewer -> getIdentity(), 
	            'creation_date' => new Zend_Db_Expr('NOW()'), 
	            'package_id' => 0, 
	            'item_id' => $company -> getIdentity(),
	            'type' => 'company',
	            'price' => $total_pay, 
	            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				'featured' => $featured,
				'number_day' => $number_sponsor_day,
			));
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
					$ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
					$order = $ordersTable -> getLastPendingOrder();
                    return $this -> _forward('success', 'utility', 'core', 
                        array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(
                        array(
	                        'controller'=>'company',
	                        'action' => 'pay-credit', 
	                        'item_id' => $id,
							'order_id' => $order -> getIdentity()
						), 'ynjobposting_extended', true), 
                        'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
                    break;
                    
                default:
                    
                    break;
            }
        }

        $company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
            
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

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynjobposting');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynjobposting', 'cancel_route' => 'ynjobposting_transaction', 'return_route' => 'ynjobposting_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'transaction', 'action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynjobposting_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function payCreditAction()
    {
    	$credit_enable = Engine_Api::_() -> ynjobposting() -> checkYouNetPlugin('yncredit');
        if (!$credit_enable)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
		$this -> view -> order = $order = Engine_Api::_()->getItem('ynjobposting_order', $this->_getParam('order_id'));
		if(!$order)
        {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
		$featured = $order -> featured;
		
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
        $select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = ?", 'sponsor_company')->limit(1);
        $type_spend = $typeTbl -> fetchRow($select);
        if(!$type_spend)
        {
            $message = $this -> view -> translate('Can not pay with credit.');
            return $this -> _redirector($message);
        }
		
        // Get user
        $this->_user = $viewer = Engine_Api::_()->user()->getViewer();
        $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
		$this-> view -> item = $company = Engine_Api::_() -> getItem('ynjobposting_company', $item_id);
        $numbers = $this->_getParam('number_item', 1);
        // Process
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
        $credits = 0;
        $cancel_url = "";
		
        $cancel_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(
	          array(
	            'controller' => 'company',
	            'action' => 'place-order',
	            'number' => $order -> number_day,
	            'id' => $item_id,
	          ), 'ynjobposting_extended', true);
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
		 $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'ynjobposting');
	     $db = $transactionsTable->getAdapter();
	     $db->beginTransaction();
	     try {
	     	Engine_Api::_() -> ynjobposting() -> buyCompany($company->getIdentity(), $order -> number_day);
			//add feature
			
			$description = $this ->view ->translate(array('Sponsor company in %s day', 'Sponsor company in %s days', $order -> number_day), $order -> number_day);
			//save transaction
	     	$transactionsTable->insert(array(
		     	'creation_date' => date("Y-m-d"),
		     	'status' => 'completed',
		     	'gateway_id' => '-3',
		     	'amount' => $order->price,
		     	'currency' => $order->currency,
		     	'user_id' => $order->user_id,
		     	'type' => $order->type,
		     	'item_id' => $order->item_id,
		     	'description' => $description,
			 ));
			 
			  //send notification to admin
			 if($order->type == 'company')
			 {
			 	$notificationType = 'ynjobposting_company_transaction';
				$item = Engine_Api::_() -> getItem('ynjobposting_company', $order->item_id);
			 }
		     elseif($order->type == 'job')
			 {
			 	$notificationType = 'ynjobposting_job_transaction';
				$item = Engine_Api::_() -> getItem('ynjobposting_job', $order->item_id);
			 }
			 $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			 $list_admin = Engine_Api::_()->user()->getSuperAdmins();
			 foreach($list_admin as $admin)
			 {
				 $notifyApi -> addNotification($admin, $item, $item, $notificationType);
			 }
	      $db->commit();
	    } catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
		
        Engine_Api::_()->yncredit()-> spendCredits($viewer, (-1) * $credits, $viewer->getTitle(), 'sponsor_company', $viewer);
        $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => 'company', 'action' => 'detail', 'id' => $order->item_id), 'ynjobposting_extended', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Pay with Credit!'))));
    }
  
  public function editAction()
  {
  
  	// Return if guest try to access to edit link.
	if (!$this -> _helper -> requireUser -> isValid())
		return;
	
	$this -> view -> company = $company = Engine_Api::_() -> getItem('ynjobposting_company' , $this->_getParam('id'));
	if(empty($company))
	{
		return $this->_helper->requireSubject()->forward();
	}
	
    //check auth for editing company
    if (!$company->isEditable()) {
        return $this -> _helper -> requireAuth -> forward();
    }
      
	$this -> _helper -> content	-> setEnabled();
	$viewer = Engine_Api::_() -> user() -> getViewer();
	
    $tableIndustry = Engine_Api::_()->getItemTable('ynjobposting_industry');
    $tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
	$main_industry = $tableIndustryMap -> getMainIndustryByCompanyId($company -> getIdentity());
		
	$industry_id = $this -> _getParam('industry_id', $main_industry->industry_id);

	// Create Form
	//get current industry
	$industry = Engine_Api::_() -> getItem('ynjobposting_industry', $industry_id);
	
	//get profile question
	$formArgs = array();
	$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynjobposting_company');
	
	if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
	{
		$profileTypeField = $topStructure[0] -> getChild();
		//get company industries
		$formArgs = array(
			'topLevelId' => $profileTypeField -> field_id,
			'topLevelValue' => $industry -> option_id,
		);
	}
	
	//get Company Additional Info
	$tableCompanyInfo = Engine_Api::_() -> getDbTable('companyinfos', 'ynjobposting');
	$companyInfo = $tableCompanyInfo -> getRowInfoByCompanyId($company -> getIdentity());
	$this -> view -> number_add_more  = $number_add_more = count($companyInfo);
	$this -> view -> number_add_more_index  = $number_add_more_index = $number_add_more;
	if ($this -> getRequest() -> isPost())
	{
		//check if edit(populate data)
		$posts = $this -> getRequest() -> getPost();
		$number_add_more = $posts['number_add_more'];
		$this -> view -> number_add_more_index = $number_add_more_index = $posts['number_add_more_index'];
		$companyInfo = array();
		for($i = 1; $i <= $number_add_more_index; $i++)
		{
			$header  = $posts['header_'.$i];
			$content  = $posts['content_'.$i];
			if($header != "" || $content != "")
			{
				$companyInfo[] = (object)array(
					'header' => $header,
					'content' => $content,
				);
			}
		}
	}
	$this -> view -> form = $form = new Ynjobposting_Form_Company_Edit( array(
		'item' => $company,
		'location' => $company -> location,
		'formArgs' => $formArgs,
		'companyInfo' => $companyInfo,
	));
	
	//populate auth
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
    $auth_arr = array('view');
    foreach ($auth_arr as $elem) {
        foreach ($roles as $role) {
            if(1 === $auth->isAllowed($company, $role, $elem)) {
                $form->$elem->setValue($role);
            }
        }    
    } 
	
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
	
	// Populate industry list.
	$industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustries();
	unset($industries[0]);
	foreach ($industries as $item)
	{
		$form -> industry_id -> addMultiOption($item['industry_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
	}
	
	//populate industry
	if($industry_id)
	{
		$form -> industry_id -> setValue($industry_id);
	}
	
	//populate sub industry
	$sub_industries = $tableIndustryMap -> getSubIndustryByCompanyId($company -> getIdentity());
	$this -> view -> sub_industries = $sub_industries;
	$submit_button = $this -> _getParam('submit_button');
	if (!isset($submit_button))
	{
		$values = $form -> getValues();
		if(empty($values))
		{
			$form -> populate($form -> getValues());
		}
		else 
		{
			//populate location
			$form -> populate(array('location_address' => $company->location));
			$form -> populate(array('lat' => $company->latitude));
			$form -> populate(array('long' => $company->longitude));
			$form -> populate(array('from' => $company->from_employee));
			$form -> populate(array('to' => $company->to_employee));
			$form -> populate($company -> toArray());
		}
		return;
	}
	
    //check auth for sponsor company
    if (!$this -> _helper -> requireAuth() -> setAuthParams('ynjobposting_company', null, 'sponsor') -> checkRequire()) {
        $form->removeElement('sponsor');        
    }
    
    //populate auth
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
    $auth_arr = array('view');
    foreach ($auth_arr as $elem) {
        foreach ($roles as $role) {
            if(1 === $auth->isAllowed($job, $role, $elem)) {
                $form->$elem->setValue($role);
            }
        }    
    }
	
	//populate data
	$posts = $this -> getRequest() -> getPost();
	$form -> populate($posts);
	$this -> view -> posts = $posts;
		
	$submit_button = $this -> _getParam('submit_button');	
	if (!isset($submit_button))
	{
		return;
	}
	   
	// Check method and data validity.
	$posts = $this -> getRequest() -> getPost();
	if (!$this -> getRequest() -> isPost())
	{
		return;
	}
	if (!$form -> isValid($posts))
	{
		return;
	}
  	//get values
	$params = $this->_getAllParams();
	$values = $form -> getValues();
	
	//check from & to employee value
	if($values['to'] < $values['from'])
	{
		$form -> addError('Please input valid from employee & to employee value.');
		return;
	}
	
	$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";                                                                                                            
    if(!preg_match($regexp, $values['contact_email']))
    {
        $form->addError('Please enter valid email!'); 
        return ;
    }
	$db = Engine_Db_Table::getDefaultAdapter();
	$db->beginTransaction();
	try 
	{
		//Check if it edit main category
		if($main_industry -> industry_id != $values['industry_id'])
		{
			$old_industry = Engine_Api::_()->getItem('ynjobposting_industry', $main_industry -> industry_id);
			$isEditIndustry = true;
		}
		
		//save company
		$company -> name = $values['name'];
		$company -> description = $values['description'];
		$company -> location = $values['location_address'];
		$company -> longitude = $values['long'];
		$company -> latitude = $values['lat'];
		$company -> website = $values['website'];
		$company -> from_employee = $values['from'];
		$company -> to_employee = $values['to'];
		$company -> contact_name = $values['contact_name'];
		$company -> contact_email = $values['contact_email'];
		$company -> contact_phone = $values['contact_phone'];
		$company -> contact_fax = $values['contact_fax'];
		$company -> save();
		
		// Set photo
	    if( !empty($values['photo']) ) {
	        $company->setPhoto($form -> photo);
	    }
	
		// Add Cover photo
		if (!empty($values['cover_thumb'])) {
			$company -> setCoverPhoto($form -> cover_thumb);
		}
		
		// Insert Addtional Information
		$tableCompanyInfo = Engine_Api::_() -> getDbTable('companyinfos', 'ynjobposting');
		//delete old data before insert all
		$tableCompanyInfo -> deleteAllInfoByCompanyId($company -> getIdentity());
		//insert new data
		$number_add_more = $params['number_add_more'];
		for($i = 1; $i <= $number_add_more_index; $i++)
		{
			$header  = $params['header_'.$i];
			$content  = $params['content_'.$i];
			if(!empty($header) && !empty($content))
			{
				$allowed_html = '<strong><b><em><i><u><strike><sub><sup><p><div><pre><address><h1><h2><h3><h4><h5><h6><span><ol><li><ul><a><img><embed><br><hr><object><param><iframe>';
				$infoRow = $tableCompanyInfo -> createRow();
				$infoRow -> header = strip_tags($header, $allowed_html);
				$infoRow -> content = strip_tags($content, $allowed_html);
				$infoRow -> company_id = $company -> getIdentity();
				$infoRow -> save();
			}
		}
		
		//delete old industry
		$tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
		$tableIndustryMap -> deleteIndustriesByCompanyId($company -> getIdentity());
		
		//insert industry to mapping table
		if(!empty($values['industry_id']))
		{
			$tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
			$checkIndustry = $tableIndustryMap -> checkExistIndustryByCompany($values['industry_id'], $company -> getIdentity());
			if(empty($checkIndustry))
			{
				$rowIndustryMap = $tableIndustryMap -> createRow();
				$rowIndustryMap -> company_id = $company -> getIdentity();
				$rowIndustryMap -> industry_id = $values['industry_id'];
				$rowIndustryMap -> main = true;
				$rowIndustryMap -> save();
			}
			$sub_industries = $this->_getParam('sub_industry');
			if(!empty($sub_industries))
			{
				foreach($sub_industries as $sub_industry_id)
				{
					$checkIndustry = $tableIndustryMap -> checkExistIndustryByCompany($sub_industry_id, $company -> getIdentity());
					if(empty($checkIndustry))
					{
						$rowIndustryMap = $tableIndustryMap -> createRow();
						$rowIndustryMap -> company_id = $company -> getIdentity();
						$rowIndustryMap -> industry_id = $sub_industry_id;
						$rowIndustryMap -> main = false;
						$rowIndustryMap -> save();
					}
				}
			}
		}
		
		// Add fields
		$customfieldform = $form -> getSubForm('fields');
		$customfieldform -> setItem($company);
		$customfieldform -> saveValues();
		
		// Remove old data custom fields if edit industry
		if($isEditIndustry)
		{
			$tableMaps = Engine_Api::_() -> getDbTable('maps','ynjobposting');
			$tableValues = Engine_Api::_() -> getDbTable('values','ynjobposting');
			$tableSearch = Engine_Api::_() -> getDbTable('search','ynjobposting');
			if($old_industry)
			{
				$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_industry->option_id));
				$arr_ids = array();
				if(count($fieldIds) > 0)
				{
					//clear values in search table
					$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $company->getIdentity()) -> limit(1));
					foreach($fieldIds as $id)
					{
						try{
							$column_name = 'field_'.$id -> child_id;
							$searchItem -> $column_name = NULL;
							$arr_ids[] = $id -> child_id;
						}
						catch(exception $e)
						{
							continue;
						}
					}
					$searchItem -> save();
					//delele in values table
					if(count($arr_ids) > 0)
					{
						$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $company->getIdentity()) -> where('field_id IN (?)', $arr_ids));
						foreach($valueItems as $item)
						{
							$item -> delete();
						}
					}
				}
			}
		}
		
        //set auth for view, comment
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
        $auth_arr = array('view');
        foreach ($auth_arr as $elem) {
            $auth_role = $values[$elem];
            if (!$auth_role) {
                $auth_role = 'everyone';
            }
            $roleMax = array_search($auth_role, $roles);
            foreach ($roles as $i=>$role) {
               $auth->setAllowed($company, $role, $elem, ($i <= $roleMax));
            }    
        }
            
		// Commit
        $db -> commit();
    } catch (Exception $e) {
        $db -> rollBack();
        throw $e;
    }
	
	$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
	$owner = $company -> getOwner();
	$notifyApi -> addNotification($owner, $company, $company, 'ynjobposting_company_edited');
	
	return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'controller' => 'company',
				'action' => 'detail',
				'id' => $company -> getIdentity()
			), 'ynjobposting_extended', true),
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
	));
	
  }
	
    protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynjobposting_general', true), 'messages' => array($message)));
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
  
}
