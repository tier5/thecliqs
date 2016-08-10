<?php
class Ynbusinesspages_WikiController extends Core_Controller_Action_Standard {
	public function init() {
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> hasModuleBootstrap('ynwiki')) {
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id))) {
				Engine_Api::_() -> core() -> setSubject($business);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}

		$business = Engine_Api::_() -> core() -> getSubject();
		if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynwiki_page')) {
			return $this -> _helper -> requireAuth -> forward();
		}
	}

	public function listAction() 
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynbusinesspages_Form_Wiki_Search;

		// Check create wiki authorization
		$canCreate  = $business -> isAllowed('wiki_create');
		$this -> view -> canCreate = $canCreate;

		$params = $this -> _getAllParams();
		$params['parent_type'] = $business -> getType();
		$params['parent_id'] = $business -> getIdentity();
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		$this -> view -> pages = $paginator = Engine_Api::_() -> ynwiki() -> getPagesPaginator($params);

		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynwiki_page;
		$paginator -> setItemCountPerPage($items_per_page);
		if (key_exists('page', $params)) {
			$paginator -> setCurrentPageNumber($params['page']);
		}
	}

}
?>
