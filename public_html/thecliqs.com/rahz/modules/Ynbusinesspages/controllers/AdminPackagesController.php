<?php
class Ynbusinesspages_AdminPackagesController extends Core_Controller_Action_Admin {

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_packages');
	}

	public function indexAction() {
		$this->view->form = $form = new Ynbusinesspages_Form_Admin_Package_Search();
		$form->isValid($this->_getAllParams());
	    $params = $form->getValues();
	    $this->view->formValues = $params;
	    $this -> view -> page = $page = $this->_getParam('page',1);
	    $tablePackage = Engine_Api::_() -> getItemTable('ynbusinesspages_package');
		$this -> view -> currency =  $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
		$this -> view -> paginator = $paginator = $tablePackage -> getPackagesPaginator($params);
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	}

	public function createAction() {
		
		$this->view->form = $form = new Ynbusinesspages_Form_Admin_Package_Create();
		$packageModuleTable = Engine_Api::_() -> getItemTable('ynbusinesspages_packagemodule');
		//populate modules
		$moduleTable = Engine_Api::_() -> getItemTable('ynbusinesspages_module');
		$modules = $moduleTable -> getAllModules();
		foreach ($modules as $item)
		{
			if($item['item_type'] != 'user')
			{
				if(Engine_Api::_() -> hasItemType($item['item_type']))
				{
					$form -> modules -> addMultiOption($item['module_id'], $item['title']);
				}
			}	
		}
		
		$tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}
		if (!count($categories)) {
			$form->addError($this->view->translate('Can not find any categories. Please add some first.'));
		}
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Api::_()->getItemTable('ynbusinesspages_package')->getAdapter();
    	$db->beginTransaction();
	    $viewer = Engine_Api::_() -> user() -> getViewer();
		try
		{
			  $package = Engine_Api::_()->getItemTable('ynbusinesspages_package')->createRow();
			  $values = $form->getValues();
			  $package->title = $values['title'];
			  $package->price = $values['price'];
			  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
			  $package->valid_amount = $values['valid_amount'];
			  $package->valid_period = 'day';
			  $package->description = $values['description'];
			  $package->show = $values['show'];
			  $package->max_cover = $values['max_cover'];
			  $package->themes = $values["themes"];
			  $package->allow_owner_manage_page = $values['allow_owner_manage_page'];
			  $package->allow_user_join_business = $values['allow_user_join_business'];
			  $package->allow_user_share_business = $values['allow_user_share_business'];
			  $package->allow_user_invite_friend = $values['allow_user_invite_friend'];
			  $package->allow_owner_add_contactform = $values['allow_owner_add_contactform'];
			  $package->allow_owner_add_customfield = $values['allow_owner_add_customfield'];
			  $package->allow_bussiness_multiple_admin = $values['allow_bussiness_multiple_admin'];
	          $package->user_id = $viewer->getIdentity();
              $package->category_id = $values['category_id'];
			  $package->save();
			  
			  $db->commit();
			  
			  if(!empty($values['modules']))
			  {
			  	 foreach($values['modules'] as $module_id)
				 {
				 	$packageModule = $packageModuleTable -> createRow();
				 	$packageModule -> package_id = $package -> getIdentity();
				 	$packageModule -> module_id = $module_id;
					$packageModule -> save();
				 }
			  }
						  
			 $levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			 $auth = Engine_Api::_() -> authorization() -> context;
			 $auth -> setAllowed($package, 'everyone', 'view', false);
			 foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			 }
	
			 // Add permissions view package
			 if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			 } else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			 }
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		$this->_helper->redirector->gotoRoute(array('module'=>'ynbusinesspages','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}

	public function editAction()
	{
		$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $this->_getParam('id'));
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view->form = $form = new Ynbusinesspages_Form_Admin_Package_Edit(array('package' => $package));
		$tableCategory = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item['title']);
		}
		if (!count($categories)) {
			$form->addError($this->view->translate('Can not find any categories. Please add some first.'));
		}
		$form -> populate($package->toArray());
		$packageModuleTable = Engine_Api::_() -> getItemTable('ynbusinesspages_packagemodule');
		//populate modules
		$moduleTable = Engine_Api::_() -> getItemTable('ynbusinesspages_module');
		$modules = $moduleTable -> getAllModules();
		foreach ($modules as $item)
		{
			if($item['item_type'] != 'user')
			{
				if(Engine_Api::_() -> hasItemType($item['item_type']))
				{
					$form -> modules -> addMultiOption($item['module_id'], $item['title']);
				}
			}	
		}
		//set value for modules
		$moduleMaps = $packageModuleTable -> getModuleByPackageId($package -> getIdentity());
		$arr_moduleMaps = array();
		foreach($moduleMaps as $moduleMap)
		{
			array_push($arr_moduleMaps, $moduleMap -> module_id);
		}
		$form->setDefaults(array('modules'=>$arr_moduleMaps));
		
		$auth = Engine_Api::_() -> authorization() -> context;
		$allowed = array();
		// populate permission view package 
		if ($auth -> isAllowed($package, 'everyone', 'view')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($package, $level, 'view')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		
		if (!empty($allowed)) {
			$form -> populate(array('levels' => $allowed, ));
		}
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();
		
		//CHECK checkHasBusiness
		$hasBusiness = $package -> checkHasBusiness();
		$addNewModule = false;
		$values = $form->getValues();
		try
		{
		  if(!$hasBusiness)
		  {
			  $package->title = $values['title'];
			  $package->price = $values['price'];
			  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
			  $package->valid_amount = $values['valid_amount'];
			  $package->valid_period = 'day';
			  $package->description = $values['description'];
			  $package->show = $values['show'];
			  $package->max_cover = $values['max_cover'];
			  $package->themes = $values["themes"];
			  $package->allow_owner_manage_page = $values['allow_owner_manage_page'];
			  $package->allow_user_join_business = $values['allow_user_join_business'];
			  $package->allow_user_share_business = $values['allow_user_share_business'];
			  $package->allow_user_invite_friend = $values['allow_user_invite_friend'];
			  $package->allow_owner_add_contactform = $values['allow_owner_add_contactform'];
			  $package->allow_owner_add_customfield = $values['allow_owner_add_customfield'];
			  $package->allow_bussiness_multiple_admin = $values['allow_bussiness_multiple_admin'];
	          $package->user_id = $viewer->getIdentity();
			  $package->category_id = $values['category_id'];
			  $package->save();
		  }
		  else
		  {
		  	  $resultTheme = array_diff($package -> themes, $values["themes"]);
		  	  if(
		  	  	  ($package -> allow_owner_manage_page != $values['allow_owner_manage_page'])
		  	  	||($package -> allow_user_join_business !=	$values['allow_user_join_business'])
		  	  	||($package -> allow_user_share_business !=	$values['allow_user_share_business'])
		  	  	||($package -> allow_user_invite_friend !=	$values['allow_user_invite_friend'])
		  	  	||($package -> allow_owner_add_contactform !=	$values['allow_owner_add_contactform'])
		  	  	||($package -> allow_owner_add_customfield !=	$values['allow_owner_add_customfield'])
		  	  	||($package -> allow_bussiness_multiple_admin !=	$values['allow_bussiness_multiple_admin'])
		  	  	||($package -> max_cover !=	$values['max_cover'])
		  	  	||(!empty($resultTheme))
		  	  	||($package -> description !=	$values["description"])
		  	  	||($package -> valid_amount !=	$values["valid_amount"])
			  )
			  {
			  	  $newPackage = Engine_Api::_()->getItemTable('ynbusinesspages_package')->createRow();
				  $newPackage->title = $values['title'];
				  $newPackage->price = $values['price'];
				  $newPackage->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
				  $newPackage->valid_amount = $values['valid_amount'];
				  $newPackage->valid_period = 'day';
				  $newPackage->description = $values['description'];
				  $newPackage->show = $values['show'];
				  $newPackage->max_cover = $values['max_cover'];
				  $newPackage->themes = $values["themes"];
				  $newPackage->allow_owner_manage_page = $values['allow_owner_manage_page'];
				  $newPackage->allow_user_join_business = $values['allow_user_join_business'];
				  $newPackage->allow_user_share_business = $values['allow_user_share_business'];
				  $newPackage->allow_user_invite_friend = $values['allow_user_invite_friend'];
				  $newPackage->allow_owner_add_contactform = $values['allow_owner_add_contactform'];
				  $newPackage->allow_owner_add_customfield = $values['allow_owner_add_customfield'];
				  $newPackage->allow_bussiness_multiple_admin = $values['allow_bussiness_multiple_admin'];
		          $newPackage->user_id = $viewer->getIdentity();
				  $newPackage->category_id = $values['category_id'];
				  $newPackage->save();
				  
				  //set old package current to 0
				  $package->current = 0;
				  $package->save();	
				  
				  $addNewModule = true;
				  
				  $businesses = $package -> getAllBusinesses();
				  foreach($businesses as $business)
				  {
				  	  $user = $business -> getOwner();
					  //send mail
					  $params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
					  $params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
					  $href =  				 
						'http://'. @$_SERVER['HTTP_HOST'].
						Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
					  $params['business_link'] = $href;	
					  $params['business_name'] = $business -> getTitle();
					  if(!empty($user))
					  {
					  	try{
							Engine_Api::_()->getApi('mail','ynbusinesspages')->send($user -> email, 'ynbusinesspages_package_changed',$params);
						}
						catch(exception $e){
							
						}
					  }
				  }
			  }
			  else 
			  {
				  $package->title = $values['title'];
				  $package->price = $values['price'];
				  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
				  $package->valid_amount = $values['valid_amount'];
				  $package->valid_period = 'day';
				  $package->description = $values['description'];
				  $package->show = $values['show'];
				  $package->max_cover = $values['max_cover'];
				  $package->themes = $values["themes"];
				  $package->allow_owner_manage_page = $values['allow_owner_manage_page'];
				  $package->allow_user_join_business = $values['allow_user_join_business'];
				  $package->allow_user_share_business = $values['allow_user_share_business'];
				  $package->allow_user_invite_friend = $values['allow_user_invite_friend'];
				  $package->allow_owner_add_contactform = $values['allow_owner_add_contactform'];
				  $package->allow_owner_add_customfield = $values['allow_owner_add_customfield'];
				  $package->allow_bussiness_multiple_admin = $values['allow_bussiness_multiple_admin'];
		          $package->user_id = $viewer->getIdentity();
				  $package->category_id = $values['category_id'];
				  $package->save();
			  }
			  
		  }
		  $db->commit();
		  if(!empty($values['modules']))
		  {
		  	 
			 	if(!$addNewModule)
				{
					$packageModuleTable -> deleteRowsByPackageId($package -> getIdentity());
					foreach($values['modules'] as $module_id)
					{
						$packageModule = $packageModuleTable -> createRow();
					 	$packageModule -> package_id = $package -> getIdentity();
					 	$packageModule -> module_id = $module_id;
						$packageModule -> save();
					}
			    }
				else
				{
					foreach($values['modules'] as $module_id)
					{
						$packageModule = $packageModuleTable -> createRow();
					 	$packageModule -> package_id = $newPackage -> getIdentity();
					 	$packageModule -> module_id = $module_id;
						$packageModule -> save();
					}
				}
			 
		  }
		  
		    // Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
	
			// Clear permissions view package by level
			$auth -> setAllowed($package, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			}
	
			// Add permissions view package
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			}
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		$this->_helper->redirector->gotoRoute(array('module'=>'ynbusinesspages','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}
	
	public function deleteAction()
   {
    // In smoothbox
    $this->view->form = $form = new Ynbusinesspages_Form_Admin_Package_Delete();
    $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $package = Engine_Api::_()->getItem('ynbusinesspages_package', $id);
		if($package -> getIdentity())
		{
			$package->deleted =  1;
			$package->save();
		}	
		$db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Package deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
    }
  }
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $id);
						$package -> deleted = true;
						$package -> save();
					}
					break;
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function sortAction()
  	{
		$packages = Engine_Api::_()->getItemTable('ynbusinesspages_package')->getPackagesPaginator($params);
	    $order = explode(',', $this->getRequest()->getParam('order'));
	    foreach( $order as $i => $item ) {
	      $package_id = substr($item, strrpos($item, '_')+1);
	      foreach( $packages as $package ) {
	        if( $package->package_id == $package_id ) {
	          $package->order = $i;
	          $package->save();
	        }
	    	}
    	}
	}
}
