<?php
class Ynbusinesspages_ContestController extends Core_Controller_Action_Standard {
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
        if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('yncontest_contest')) {
            return $this -> _helper -> requireAuth -> forward();
        }
        
        $contest_enable = Engine_Api::_()->hasModuleBootstrap('yncontest');
        
        if (!$contest_enable) {
            return $this -> _helper -> requireSubject -> forward();
        }
	}
	
    public function listAction() {
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->canCreate = $canCreate = $business -> isAllowed('contest_create');
        
        //Get Search Form
        $this -> view -> form = $form = new Ynbusinesspages_Form_Contest_Search();

        if (!$business -> isViewable()) {
            return $this -> _helper -> requireAuth -> forward();
        }
            
        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['browseby'] = $this -> _getParam('browseby', 'all');
        $params['manage'] = 1;
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'browseby' => $params['browseby'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'yncontest_contest';
        
        $this -> view -> ItemTable = $params['ItemTable'];
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getContestsPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
    }
}
?>
