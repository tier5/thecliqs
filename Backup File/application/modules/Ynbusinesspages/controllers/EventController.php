<?php
class Ynbusinesspages_EventController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		//Checking Event/Ynevent Plugin - View privacy
		if (!Engine_Api::_() -> hasItemType('event'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($group = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
		$business = Engine_Api::_() -> core() -> getSubject();
		if(!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('event'))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
	}

	public function listAction()
	{
		//Get viewer, business, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynbusinesspages_Form_Event_Search;
		$val = $this -> _getAllParams();
		// Populate form data
		if (!$form -> isValid($val))
		{
			$form -> populate($defaultValues);
			$this -> view -> formValues = $values = array();
			$this -> view -> message = "The search value is not valid !";
			return;
		}
		$values = $form -> getValues();
		$values['business_id'] = $business -> getIdentity();
		// Prepare data
		$this -> view -> formValues = $values = array_merge($values, $_GET);
		
		// Check create event authorization
		$this -> view -> canCreate = $business -> isAllowed('event_create');
		//Get data
		$this -> view -> paginator = $paginator = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getEventsPaginator($values);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
	}

	public function manageAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();

		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $formFilter = new Ynbusinesspages_Form_Event_Manage;

		// Check create video authorization
		$this -> view -> canCreate = $business -> isAllowed('event_create');

		$defaultValues = $formFilter -> getValues();

		// Populate form data
		if (!$formFilter -> isValid($this -> _getAllParams()))
		{
			$formFilter -> populate($defaultValues);
			$this -> view -> formValues = $values = array();
		}
		$values = $formFilter -> getValues();
		
		$table = Engine_Api::_() -> getItemTable('event');
		$tableName = $table -> info('name');

		$module_event = 'event';
		if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
		{
			$module_event = 'ynevent';
		}
		// Only mine
		if (@$values['view'] == 2)
		{
			$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity());
		}
		// All membership
		else
		{
			$membership = Engine_Api::_() -> getDbtable('membership', $module_event);
			$select = $membership -> getMembershipsOfSelect($viewer);
		}
		
		//Get data from table Mappings
		$tableMapping = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');
		$ids = $tableMapping -> getItemIdsMapping('event', array('business_id' => $business -> getIdentity()));
		
		if(!$ids)
		{
			$select -> where("`{$tableName}`.event_id IN (0)");
		}
		else {
			$select -> where("`{$tableName}`.event_id IN (?)", $ids);
		}
		if (!empty($values['text']))
		{
			$select -> where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
		}
		$select -> order('creation_date DESC');
		
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> text = $values['text'];
		$this -> view -> view = $values['view'];

		$paginator -> setItemCountPerPage(10);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
	}
}
?>
