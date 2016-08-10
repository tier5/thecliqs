<?php
class Ynbusinesspages_JobController extends Core_Controller_Action_Standard {
	public function init() {
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
			{
				Engine_Api::_() -> core() -> setSubject($business);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
		
        $business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynjobposting_job')) {
			return $this -> _helper -> requireAuth -> forward();
		}
        
        if( !Engine_Api::_()->hasModuleBootstrap('ynjobposting') ) {
            return $this -> _helper -> requireSubject -> forward();
        }
	}
	
    public function importAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');  
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        $reload = $this->_getParam('reload', true);
        if (!$business -> isAllowed('job_import')) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('You don\'t have permission to import jobs.');
            return;
        }
        
        //get available companies
        $companies = Engine_Api::_()->ynbusinesspages()->getMyCompanies();
        if (empty($companies)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('You can not get any jobs because you have no companies in module Job Posting.');
            return;
        }
        
        $this->view->form = $form = new Ynbusinesspages_Form_Job_Get();
        $form->company_id->addMultiOptions($companies);
        
        $company_id = $this->_getParam('company_id', null);
        if (is_null($company_id)) {
            $keys = array_keys($companies);
            $company_id = $keys[0];
        }
        $form->company_id->setValue($company_id);
        $jobs = Engine_Api::_()->ynbusinesspages()->getJobsByCompany($company_id, $business->getIdentity());
        
        if (empty($jobs)) {
            $form->addError(Zend_Registry::get('Zend_Translate')->_('No jobs available in this company'));
            return;    
        }
                
        $form->job_ids->addMultiOptions($jobs); 
        
        // Check method and data validity.
        $posts = $this -> getRequest() -> getPost();
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$form -> isValid($posts)) {
            return;
        }
        
        $values = $form -> getValues();
        $viewer = Engine_Api::_()->user()->getViewer();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try {
            foreach ($values['job_ids'] as $job_id) {
                $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                $job = Engine_Api::_()->getItem('ynjobposting_job', $job_id);
                $row = $table -> createRow();
                $row -> setFromArray(array(
                   'business_id' => $business->getIdentity(),
                   'item_id' => $job_id,
                   'type' => 'ynjobposting_job',
                   'owner_id' => $viewer -> getIdentity(),                     
                   'owner_type' => 'user',  
                   'creation_date' => date('Y-m-d H:i:s'),
                   'modified_date' => date('Y-m-d H:i:s'),
                   ));
                $row -> save();
                // send notification to followers
                $business -> sendNotificationToFollowers(Zend_Registry::get('Zend_Translate') -> _('new job')); 
            
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                // Add activity
                $action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_job_import');
                if($action) {
                    $activityApi->attachActivity($action, $job);
                }
            }
            $db -> commit();
        }
        catch(Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        
        $tab = $this->_getParam('tab', null);
        if ($tab) {
            return $this -> _forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Jobs has been imported.')),
                'layout' => 'default-simple',
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'id' => $business -> getIdentity(),
                    'tab' => $tab,
                    'slug' => $business -> getSlug(),
                ), 'ynbusinesspages_profile', true),
                'closeSmoothbox' => true,
            ));
        }
        return $this -> _forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Jobs has been imported.')),
            'layout' => 'default-simple',
            'parentRefresh' => $reload,
            'closeSmoothbox' => true,
        ));
    }

    public function listAction() {
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        //check auth import
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->canImport = $canImport = $business -> isAllowed('job_import');
        
        //Get Search Form
        $this -> view -> form = $form = new Ynbusinesspages_Form_Job_Search();

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['status'] = $this -> _getParam('status', 'all');
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'status' => $params['status'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'ynjobposting_job';
        
        $this -> view -> ItemTable = $params['ItemTable'];
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getJobsPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
    }
}
?>
