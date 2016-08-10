<?php
class Yncontest_AdminEntriesController extends Core_Controller_Action_Admin
{
	/**
	 *
	 */
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		// Get navigation bar
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      					->getNavigation('yncontest_admin_main', array(), 'yncontest_admin_main_entries');
		$this -> view -> form = $form = new Yncontest_Form_Entries_Search;
		$form->removeElement('award');
		$params = $request -> getParams();
		$form -> populate($params);

		if (empty($params['orderby']))
			$params['orderby'] = 'modified_date';
		if (empty($params['direction']))
			$params['direction'] = 'DESC';
		$this -> view -> formValues = $params;

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($request -> isPost())
		{
			$values = $request -> getPost();
			foreach ($values['delete'] as $value)
			{
				$entry = Engine_Api::_() -> getItem('yncontest_entries', $value);
				if ($entry)
					$entry -> delete();
			}
		}
		//$params['user_id'] = $viewer -> getIdentity();

		$this -> view -> arrPlugins = Engine_Api::_() -> yncontest() -> getPlugins();

		$this -> view -> formValues = $params;
		$this -> view -> paginator = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator3($params);

		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contest.entries.page', 10);
		$this -> view -> paginator -> setItemCountPerPage($items_per_page);
		if (isset($params['page']))
			$this -> view -> paginator -> setCurrentPageNumber($params['page']);

	}

	

	/**
	 *
	 */
	public function deleteAction()
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$entry_id = (int)$this -> _getParam('id');
		$entries = Engine_Api::_() -> getItem('yncontest_entries', $entry_id);
		if (!$entries)
			return $this -> _helper -> requireAuth -> forward();
		// Get subject and check auth

		if (!$this -> _helper -> requireAuth() -> setAuthParams('contest', $viewer, 'deleteentries') -> isValid())
		{
			return;
		}
		if ($this -> getRequest() -> isPost())
		{

			$entries -> delete();

			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Entry is deleted successfully.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));

		}

	}

	public function deleteSelectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Save values
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{

				$contest = Engine_Api::_() -> getItem('yncontest_contest', $id);

				if ($contest)
				{
					$contest -> delete();
					//$contest -> contest_status = "delete";
					//$contest -> save();
				}
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
		}

	}

	public function statisticAction()
	{
		$contest_id = $this -> _getParam('contest_id');
		$contest = Engine_Api::_() -> getItem('yncontest_contest', contest_id);
		$this -> view -> contest = $contest;
	}

}
