<?php
class Ynbusinesspages_SocialMusicController extends Core_Controller_Action_Standard {
	public function init() {
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id))) {
				Engine_Api::_() -> core() -> setSubject($business);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
		
        $business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynmusic_song')) {
			return $this -> _helper -> requireAuth -> forward();
		}
        
        $ynmusic_enable = Engine_Api::_() -> hasModuleBootstrap('ynmusic');
        if (!$ynmusic_enable) {
            return $this -> _helper -> requireSubject -> forward();
        }
	}
	
    public function listAction() {
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->canCreate = $canCreate = $business -> isAllowed('music_create');
        
        //Get Search Form
        $this -> view -> form = $form = new Ynbusinesspages_Form_Ynmusic_Search();

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['browse_by'] = $this -> _getParam('browse_by', 'recently_created');
		$params['type'] = $this -> _getParam('type', 'album');
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'browse_by' => $params['browse_by'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'ynmusic_'.$params['type'];
    	
		$this -> view -> type = $params['type'];   
        $this -> view -> ItemTable = $params['ItemTable'];
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getYnmusicPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
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
