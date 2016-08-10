<?php
class Ynjobposting_AdminCompaniesController extends Core_Controller_Action_Admin {

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_manage_companies');
	}

	public function indexAction() {
		$tableCompany = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$this -> view -> form = $form = new Ynjobposting_Form_Admin_Company_Search();

		$list_industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getAllIndustries();
		foreach ($list_industries as $industry)
		{
			$form -> industry_id -> addMultiOption($industry['industry_id'], $industry['title']);
		}
		
		$form->populate($this->_getAllParams());
		$values = $form->getValues();
        $this->view->formValues = $values;
		
		$params = $this->_getAllParams();
		$page = $this -> _getParam('page', 1);
		$params['page'] = $page;
		$this -> view -> paginator = $paginator = $tableCompany -> getCompaniesPaginator($params);
		$this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}

	public function sponsorAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$sponsorTbl = Engine_Api::_() -> getItemTable("ynjobposting_sponsor");
		$companyId = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $company = Engine_Api::_()->getItem('ynjobposting_company', $companyId);
        if (!$company) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the company.")));
            exit ;
        }
        if (!$company->isSponsorable()) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Don't have permission to do this.")));
            exit ;
        }
		if ($companyId) {
			$sponsorRow = $sponsorTbl -> getSponsorRowByCompanyId($companyId);
			if (is_null($sponsorRow)) {
				$sponsorRow = $sponsorTbl -> createRow();
				$sponsorRow -> setFromArray(array('company_id' => $companyId, 'creation_date' => new Zend_Db_Expr("NOW()"), ));
			}
			$sponsorRow -> active = $value;
			$sponsorRow -> modified_date = new Zend_Db_Expr("NOW()");
			$sponsorRow -> expiration_date = NULL;
			$sponsorRow -> save();
			
			$company -> sponsored = $value;
			$company -> save();
			
			$owner = $company -> getOwner();
			if($value == 1)
			{
				$notifyApi -> addNotification($owner, $company, $company, 'ynjobposting_company_sponsored');
			}
			else
			{
				$notifyApi -> addNotification($owner, $company, $company, 'ynjobposting_company_unsponsored');
			}
			
			echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set sponsored successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset sponsored successfully!")));
			exit ;
		} else {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not set featured this company")));
			exit ;
		}
	}

	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
						if ($company && $company -> isDeletable()) {
							//delete job 
							$list_jobs = $tableJob -> getJobsByCompanyId($id);
							foreach($list_jobs as $job)
							{
								$job -> delete();
							}
							//delete company
							$company -> deleted = true;
							$company -> status = 'deleted';
							$company -> save();
						}
					}
					break;
				case 'Publish' :
					foreach ($ids_array as $id) {
						$company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
						$company -> status = 'published';
						$company -> save();
					}
					break;
				case 'Close' :
					foreach ($ids_array as $id) {
						
						$company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
						if ($company && $company -> isClosable()) {
                            $list_jobs = $tableJob -> getJobsByCompanyId($id);
                            foreach($list_jobs as $job)
                            {
                                $job -> changeStatus('ended');
                            }
							$company -> status = 'closed';
							$company -> save();
						}
					}
					break;	
				
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}

}
