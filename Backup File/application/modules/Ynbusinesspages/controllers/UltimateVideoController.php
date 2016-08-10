<?php
class Ynbusinesspages_UltimateVideoController extends Core_Controller_Action_Standard {

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
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynultimatevideo_video')) {
			return $this -> _helper -> requireAuth -> forward();
		}

        $ynultimatevideo_enable = Engine_Api::_() -> hasModuleBootstrap('ynultimatevideo');
        if (!$ynultimatevideo_enable) {
            return $this -> _helper -> requireSubject -> forward();
        }
	}
	
    public function listAction() {
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->canCreate = $canCreate = $business -> isAllowed('video_create');

        //Get Search Form
        // @TODO write ynultimatevideo search form
        $this -> view -> form = $form = new Ynbusinesspages_Form_Ynultimatevideo_Search();

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['search'] = $this -> _getParam('search', '');
        $params['browse_by'] = $this -> _getParam('browse_by', 'recently_created');
		$params['type'] = $this -> _getParam('type', 'video');
        //Populate Search Form
        $form -> populate(array(
            'search' => $params['search'],
            'browse_by' => $params['browse_by'],
            'page' => $this -> _getParam('page', 1)
        ));
        $this -> view -> formValues = $form -> getValues();
        $params['ItemTable'] = 'ynultimatevideo_'.$params['type'];

		$this -> view -> type = $params['type'];
        $this -> view -> ItemTable = $params['ItemTable'];

        // check if logged in as business to disable Add to
        $this -> view -> isLogAsBusiness = Engine_Api::_()->ynbusinesspages()->isLogAsBusiness();

        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getYnultimatevideoPaginator($params);

        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
    }

    public function manageAction()
    {

        if (!$this -> _helper -> requireUser() -> isValid())
        {
            return;
        }
        //Get viewer, business, search form
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        $this -> view -> form = $form = new Ynbusinesspages_Form_Ynultimatevideo_Search;
        // Check create video authorization
        $this -> view -> canCreate = $business -> isAllowed('video_create');
        //Prepare data filer
        $params = array();
        $params = $this -> _getAllParams();
        $params['user_id'] = $viewer -> getIdentity();
        $params['business_id'] = $business -> getIdentity();
        $form -> populate($params);
        $this -> view -> formValues = $form -> getValues();

        //Get table Mappings
        $tableMapping = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');

        //Get data
        $this -> view -> paginator = $paginator = $tableMapping -> getYnultimatevideoPaginator($params);
        if (!empty($params['orderby']))
        {
            switch($params['orderby'])
            {
                case 'most_liked' :
                    $this -> view -> infoCol = 'like';
                    break;
                case 'most_commented' :
                    $this -> view -> infoCol = 'comment';
                    break;
                default :
                    $this -> view -> infoCol = 'view';
                    break;
            }
        }
        $paginator -> setItemCountPerPage(10);
        $paginator -> setCurrentPageNumber($this -> _getParam('page'));
    }
}
?>
