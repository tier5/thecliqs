<?php
class Ynbusinesspages_VideoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		//Checking Ynvideo Plugin - Viewer required -View privacy
		$video_enable = Engine_Api::_() -> hasItemType('video');
		if (!$video_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
			{
				Engine_Api::_() -> core() -> setSubject($business);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		$business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('video'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
	}

	public function listAction()
	{
		//Get viewer, business, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynbusinesspages_Form_Video_Search;

		// Check create video authorization
		$canCreate = $business -> isAllowed('video_create');
		$this -> view -> canCreate = $canCreate;

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['search'] = 1;
		$params['business_id'] = $business -> getIdentity();
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		
		//Get data from table Mappings
		$tableMapping = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');
		
		//Get data
		$this -> view -> paginator = $paginator = $tableMapping -> getVideosPaginator($params);
		
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

	public function manageAction()
	{
		
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		//Get viewer, business, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynbusinesspages_Form_Video_Search;
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
		$this -> view -> paginator = $paginator = $tableMapping -> getVideosPaginator($params);
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
