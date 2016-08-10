<?php
class Ynbusinesspages_MusicController extends Core_Controller_Action_Standard {
	public function init() {
		$this -> view -> tab = $this->_getParam('tab', null);
		$music_enable = Engine_Api::_() -> hasModuleBootstrap('music');
        $mp3music_enable = Engine_Api::_() -> hasModuleBootstrap('mp3music');
        
        if (!$music_enable && !$mp3music_enable) {
           return $this -> _helper -> requireSubject -> forward();
        }
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
		if(!$business -> isViewable() || (!$business -> getPackage() -> checkAvailableModule('mp3music_album') && !$business -> getPackage() -> checkAvailableModule('music_playlist'))) {
			return $this -> _helper -> requireAuth -> forward();
		}
	}
	
    public function listAction() {
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $canCreate = $business -> isAllowed('music_create');
        
        $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynbusinesspages_business', $viewer, 'music');
        if ($canCreate && $levelCreate) {
            $this -> view -> canCreate = true;
        } else {
            $this -> view -> canCreate = false;
        }
        
        //Get Search Form
        $this -> view -> form = $form = new Ynbusinesspages_Form_Music_Search();

        if ($viewer -> getIdentity() == 0)
            $form -> removeElement('view');
        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['user_id'] = null;
        $params['search'] = $this -> _getParam('search', '');
        $params['view'] = $this -> _getParam('view', 0);
        $params['order'] = $this -> _getParam('order', 'recent');
        if ($params['view'] == 1) {
            $params['user_id'] = $viewer -> getIdentity();
        }
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'view' => $params['view'],
            'order' => $params['order'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        if ($this -> _getParam('type') == 'mp3music') {
            $params['ItemTable'] = 'mp3music_album';
        }   
        else {
            $params['ItemTable'] = 'music_playlist';
        }
        
        $this -> view -> ItemTable = $params['ItemTable'];
        //Get Album paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getAlbumsPaginator($params);
    
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 10));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
    }
}
?>
