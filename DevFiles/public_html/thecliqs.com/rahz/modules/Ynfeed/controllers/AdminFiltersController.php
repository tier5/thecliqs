<?php
class Ynfeed_AdminFiltersController extends Core_Controller_Action_Admin {
	public function indexAction() {
		if ($this -> getRequest() -> isPost()) 
		{
			$values = $this -> getRequest() -> getPost();
			if(isset($values['ids']) && $values['ids'])
			{
				$ids = explode(',', $values['ids']);
				foreach ($ids as $id) 
				{
					$content = Engine_Api::_() -> getItem('ynfeed_content', $id);
					if($content)
						$content -> delete();
				}
			}
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeed_admin_main', array(), 'ynfeed_admin_main_filters');
		$this -> view -> contents = Engine_Api::_() -> getDbtable('contents', 'ynfeed') -> getContentList();
		$this -> view -> contentsShow = Engine_Api::_() -> getDbtable('contents', 'ynfeed') -> getContentList(array('show' => 1));
	}

	public function sortAction() {
		$contents = Engine_Api::_() -> getDbtable('contents', 'ynfeed') -> getContents();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $value) {
			$content_id = substr($value, strrpos($value, '_') + 1);
			foreach ($contents as $item) {
				if ($item -> content_id == $content_id) {
					$item -> order = $i;
					$item -> save();
				}
			}
		}
	}

	public function showAction() {
		$id = $this -> getRequest() -> getParam('id', 0);
		$value = $this -> getRequest() -> getParam('value');
		if ($id && $content = Engine_Api::_() -> getItem('ynfeed_content', $id)) {
			$content -> show = $value;
			$content -> save();
		}
	}

	public function showMultiAction() {
		$contents = Engine_Api::_() -> getDbtable('contents', 'ynfeed') -> getContents();
		$value = $this -> getRequest() -> getParam('value');
		foreach ($contents as $item) {
			$item -> show = $value;
			$item -> save();
		}
	}

	public function deleteFilterAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$filter_type = $this -> _getParam('filter_type');
		$this -> view -> filter_type = $filter_type;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try {
				$content = Engine_Api::_() -> getDbTable('contents', 'ynfeed') -> getContents(array('filter_type' => $filter_type));
				if ($content)
					$content -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-filters/delete.tpl');
	}

	public function addFilterAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form = $form = new Ynfeed_Form_Admin_Content();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();
		$contentTable = Engine_Api::_() -> getItemTable('ynfeed_content');
		$contentCheck = $contentTable -> fetchRow(array('filter_type = ?' => $values['filter_type']));
		if (!empty($contentCheck)) {
			$itemError = Zend_Registry::get('Zend_Translate') -> _("Filter already exists.");
			$form -> getDecorator('errors') -> setOption('escape', false);
			$form -> addError($itemError);
			return;
		}
		$content = $contentTable -> createRow();
		$content -> setFromArray($values);
		$content -> save();

		// Add photo
		if (!empty($values['photo'])) {
			$content -> setPhoto($form -> photo);
		}

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The filter was added successfully.'))));
	}

	public function editFilterAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$filter_type = $this -> _getParam('filter_type');
		$content = Engine_Api::_() -> getDbTable('contents', 'ynfeed') -> getContents(array('filter_type' => $filter_type));
		$this -> view -> form = $form = new Ynfeed_Form_Admin_Content();
		$form -> removeElement('module_name');
		$form -> removeElement('filter_type');
		$form -> setTitle('Edit Filter');
		if(!$content -> content_tab)
		{
			$form -> removeElement('resource_title');
		}
		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($content -> toarray());
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		// Process
		$values = $form -> getValues();
		$content -> setFromArray($values);
		$content -> save();
		// Add photo
		if (!empty($values['photo'])) {
			$content -> setPhoto($form -> photo);
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The filter was edited successfully.'))));
	}
	public function customListsAction() 
	{
		if ($this -> getRequest() -> isPost()) 
		{
			$values = $this -> getRequest() -> getPost();
			if(isset($values['ids']) && $values['ids'])
			{
				$ids = explode(',', $values['ids']);
				foreach ($ids as $id) 
				{
					$custom = Engine_Api::_() -> getItem('ynfeed_customtype', $id);
					if($custom)
						$custom -> delete();
				}
			}
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeed_admin_main', array(), '');
		$module_name = $this->_getParam('module_name');
		$this -> view -> m_name = $module_name;
		$this -> view -> module_name = Engine_Api::_() -> getDbTable('modules', 'core') -> getModule($module_name) -> title;
		$this -> view -> customLists = Engine_Api::_() -> getDbtable("customtypes", "ynfeed") -> getCustomTypeList(array('module_name' => $module_name));
		$this -> view -> customListsEnabled = Engine_Api::_() -> getDbtable("customtypes", "ynfeed") -> getCustomTypeList(array('module_name' => $module_name, 'enabled' => 1));
  	}
	public function addCustomAction() 
	{
	    $this->view->form = $form = new Ynfeed_Form_Admin_CustomType();
	    if (!$this->getRequest()->isPost()) {
	      return;
	    }
	    if (!$form->isValid($this->getRequest()->getPost())) {
	      return;
	    }
	
	    // Process
	    $values = $form->getValues();
	    $customTable = Engine_Api::_()->getItemTable('ynfeed_customtype');
	    $customCheck = $customTable->fetchRow(array('resource_type = ?' => $values['resource_type']));
	    if (!empty($customCheck)) 
	    {
	      $itemError = Zend_Registry::get('Zend_Translate')->_("Content Type already exists.");
	      $form->getDecorator('errors')->setOption('escape', false);
	      $form->addError($itemError);
	      return;
	    }
	    $custom = $customTable->createRow();
	    $custom->setFromArray($values);
	    $custom->save();
		
	    $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The content type was added successfully.'))));
  	}

  	public function editCustomAction() 
  	{
	    $resource_type = $this->_getParam('resource_type');
	    $custom = Engine_Api::_()->getItemTable('ynfeed_customtype')->fetchRow(array('resource_type = ?' => $resource_type));
	    $this->view->form = $form = new Ynfeed_Form_Admin_CustomType();
		$form -> removeElement('resource_type');
		$form -> setTitle('Edit Content Type');
		$form -> setDescription('');
	    if (!$this->getRequest()->isPost()) 
	    {
	      $form->populate($custom->toarray());
	      return;
	    }
	    if (!$form->isValid($this->getRequest()->getPost())) {
	      return;
	    }
	    // Process
	    $values = $form->getValues();
	    $custom->setFromArray($values);
	    $custom->save();
		
	    $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The content type was edited successfully.'))));
  	}

  	public function deleteCustomAction() 
  	{
	    $this->_helper->layout->setLayout('admin-simple');
	    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
	
	    if ($this->getRequest()->isPost()) 
	    {
	      $custom = Engine_Api::_()->getItemTable('ynfeed_customtype')->fetchRow(array('resource_type = ?' => $resource_type));
		  if($custom)
		  {
	      	$custom->delete();
		  }
	      $this->_forward('success', 'utility', 'core', array(
	          'smoothboxClose' => 10,
	          'parentRefresh' => 10,
	          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
	      ));
	    }
  	}

	public function enableAction() 
	{
	    $id = $this->_getParam('id', 0);
		$value = $this -> getRequest() -> getParam('value');
	    $db = Engine_Db_Table::getDefaultAdapter();
	    $db->beginTransaction();
	    $custom = Engine_Api::_()->getItem('ynfeed_customtype', $id);
		if($custom)
		{
			try 
		    {
		      $custom->enabled = $value;
		      $custom->save();
		      $db->commit();
		    } catch (Exception $e) {
		      $db->rollBack();
		      throw $e;
		    }
		}
	}
	public function enableMultiAction() {
		$value = $this -> getRequest() -> getParam('value');
		$module_name = $this -> getRequest() -> getParam('module_name');
		$customtypes = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getCustomTypes(array('module_name' => $module_name));
		foreach ($customtypes as $item) {
			$item -> enabled = $value;
			$item -> save();
		}
	}

	public function sortCustomAction() 
	{
		$customs = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getCustomTypes(array('module_name' => $module_name));
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $value) {
			$customtype_id = substr($value, strrpos($value, '_') + 1);
			foreach ($customs as $custom) 
			{
				if ($custom -> customtype_id == $customtype_id) 
				{
					$custom -> order = $i;
					$custom -> save();
				}
			}
		}
	}
}
