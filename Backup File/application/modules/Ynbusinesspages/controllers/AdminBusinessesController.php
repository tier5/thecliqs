<?php
class Ynbusinesspages_AdminBusinessesController extends Core_Controller_Action_Admin {

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_businesses');
	}

	public function indexAction() {
		$tableBusiness = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$this -> view -> form = $form = new Ynbusinesspages_Form_Admin_Business_Search();

		$list_categories = Engine_Api::_() -> getItemTable('ynbusinesspages_category') -> getAllCategories();
		foreach ($list_categories as $category)
		{
			$form -> category_id -> addMultiOption($category['category_id'], $category['title']);
		}
		
		$form->populate($this->_getAllParams());
        $values = $form -> getValues;
		
		$params = $this->_getAllParams();
		$sysTimezone = date_default_timezone_get();
        if ($values['from_date']) {
            $from_date = new Zend_Date(strtotime($values['from_date']));
			$from_date->setTimezone($sysTimezone);
			$params['from_date'] = $from_date;
        }
		
	    if ($values['to_date']) {
	    	$to_date = new Zend_Date(strtotime($values['to_date']));
			$to_date->setTimezone($sysTimezone);
			$params['to_date'] = $to_date;
	    }
		//for admin
		$params['admin'] = 1;
		$page = $this -> _getParam('page', 1);
		$params['page'] = $page;
		if (!isset($params['status']))
		{
			$params['status'] = 'all';
		}
		
		unset($params['module']);
		unset($params['controller']);
		unset($params['action']);
		unset($params['rewrite']);
		
		$this->view->formValues = $params;
		$this -> view -> paginator = $paginator = $tableBusiness -> getBusinessesPaginator($params);
	    $limit = $this->_getParam('itemCountPerPage', 10);
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page );
	}

	public function featureAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$featureTbl = Engine_Api::_() -> getItemTable("ynbusinesspages_feature");
		$businessId = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
        if (!$business) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the business.")));
            exit ;
        }
		if ($businessId) {
			$featureRow = $featureTbl -> getFeatureRowByBusinessId($businessId);
			if (is_null($featureRow)) {
				$featureRow = $featureTbl -> createRow();
				$featureRow -> setFromArray(array('business_id' => $businessId, 'creation_date' => new Zend_Db_Expr("NOW()"), ));
			}
			$featureRow -> active = $value;
			$featureRow -> modified_date = new Zend_Db_Expr("NOW()");
			$featureRow -> expiration_date = NULL;
			$featureRow -> save();
			
			$business -> featured = $value;
			$business -> save();
			
			$owner = $business -> getOwner();
			if($value == 1)
			{
				$notifyApi -> addNotification($owner, $business, $business, 'ynbusinesspages_business_featured');
			}
			else
			{
				$notifyApi -> addNotification($owner, $business, $business, 'ynbusinesspages_business_unfeatured');
			}
			
			echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set featured successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset featured successfully!")));
			exit ;
		} else {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not set featured this business")));
			exit ;
		}
	}
	
	public function deleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('business_id'));
		
		//only owner & admin could delete
		try{
			if(!($viewer -> isSelf($business -> getOwner())) && (!$viewer -> isAdmin()))
			{
				return $this -> _helper -> requireAuth() -> forward();
			}
		}
		catch(exception $e){
			
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
			'messages' => array($this -> view -> message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
		
	}
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$tableBusiness = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
						if (isset($business)) {
							//delete business
							$business -> delete();
						}
					}
					break;
				case 'Approve' :
					foreach ($ids_array as $id) {
						$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
						if (isset($business)) {
							if($business -> status == 'pending')
							{
								//for claim business
								$tableClaim = Engine_Api::_()->getDbTable('claimrequests', 'ynbusinesspages');
								$rowClaim = $tableClaim -> getClaimRequest($business -> user_id, $business -> getIdentity());
								if(!empty($rowClaim) && $rowClaim -> status == 'approved')
								{
									$user = Engine_Api::_() -> getItem('user', $business -> user_id);
									if($user -> getIdentity())
									{
										$adminList = $business -> getAdminList();
										$adminList -> add($user);
										$business -> membership() -> addMember($user) -> setUserApproved($user) -> setResourceApproved($user);
										$business -> membership() -> getMemberInfo($user) -> setFromArray(array('list_id' => $adminList->getIdentity())) -> save();
									}
								}
								
								//get package
								$package = Engine_Api::_() -> getItem('ynbusinesspages_package', $business -> package_id);
								if($package -> getIdentity())
								{
									$now =  date("Y-m-d H:i:s");
									$business -> approved_date = $now;
									if ($package->valid_amount == 0) {
										$business -> expiration_date = NULL;
										$business -> never_expire = 1;
									}
									else {
										if($package->valid_amount == 1)
										{
											$type = 'day';
										}
										else 
										{
											$type = 'days';
										}
										
										$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($package->valid_amount." ".$type));
										$business -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
									}
								}	
								
								if(!$business -> approved)
								{
									//add activity
									$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
									$action = $activityApi->addActivity($business -> getOwner(), $business, 'ynbusinesspages_business_create');
									if($action) {
										$activityApi->attachActivity($action, $business);
									}
								}
								
								$business -> approved = true;
								$business -> status = 'published';
								$business -> save();
								
								//send notice
								$owner = $business -> getOwner();
								$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
								$notifyApi -> addNotification($owner, $business, $business, 'ynbusinesspages_business_approved');
								//send email
								$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
								$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
								$href =  				 
									'http://'. @$_SERVER['HTTP_HOST'].
									Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
								$params['business_link'] = $href;	
								$params['business_name'] = $business -> getTitle();
								if(!empty($owner))
								{
									try{
										Engine_Api::_()->getApi('mail','ynbusinesspages')->send($owner -> email, 'ynbusinesspages_business_approved',$params);
									}
									catch(exception $e)
									{
										
									}
								}
							}	
						}
					}
					break;
				case 'Deny' :
					foreach ($ids_array as $id) {
						$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
						if (isset($business)) {
							if($business -> status == 'pending')
								$business -> changeStatus('denied');
						}
					}
					break;	
				
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function neverExpireAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$businessId = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $businessId);
        if (!$businessId || !$business) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the business.")));
            exit ;
        }
		
		if (!in_array($business->status, array('published', 'expired'))) {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("This request is invalid.")));
            exit ;
		}
			
		$business->never_expire = $value;
		
		if ($value == 1) {
			$business->status = 'published';
			
			//send notifications
		    $owner = $business -> getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		    $notifyApi -> addNotification($owner, $owner, $business, 'ynbusinesspages_business_never_expire');
		}	
		
		$business->save();		
		echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set never expire successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset never expire successfully!")));
		exit ;
	}

	public function expireNowAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('business_id');
        $this->view->business_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
            	$old_status = $business->status;
                $business = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
				$old_status = $business->status;
				$business->status = 'expired';
				$business->never_expire = 0;
				$business->save();
				if ($old_status == 'published') {
					$owner = $business -> getOwner();
					$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				    $notifyApi -> addNotification($owner, $owner, $business, 'ynbusinesspages_business_expired');
				}
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This business has been set to expired.')
            ));
        }
    }
}
