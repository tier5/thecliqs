<?php
class Ynjobposting_AdminPackagesController extends Core_Controller_Action_Admin {

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_manage_packages');
	}

	public function indexAction() {
		$this->view->form = $form = new Ynjobposting_Form_Admin_Package_Search();
		$form->isValid($this->_getAllParams());
	    $params = $form->getValues();
	    $this->view->formValues = $params;
	    $this -> view -> page = $page = $this->_getParam('page',1);
	    $tablePackage = Engine_Api::_() -> getItemTable('ynjobposting_package');
		$this -> view -> currency =  $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
		$this -> view -> paginator = $paginator = $tablePackage -> getPackagesPaginator($params);
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	}

	public function createAction() {
		$this->view->form = $form = new Ynjobposting_Form_Admin_Package_Create();
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Api::_()->getItemTable('ynjobposting_package')->getAdapter();
    	$db->beginTransaction();
	    $viewer = Engine_Api::_() -> user() -> getViewer();
		try
		{
		  $package = Engine_Api::_()->getItemTable('ynjobposting_package')->createRow();
		  $values = $form->getValues();
		  $package->title = $values['title'];
		  $package->price = $values['price'];
		  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		  $package->valid_amount = $values['valid_amount'];
		  $package->valid_period = 'day';
		  $package->description = $values['description'];
		  $package->show = $values['show'];
          $package->user_id = $viewer->getIdentity();
		  $package->save();
		  
		  $db->commit();
		  
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			$auth = Engine_Api::_() -> authorization() -> context;
			$auth -> setAllowed($package, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			}
	
			// Add permissions view package
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			}
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Package added.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function editAction()
	{
		$this->view->form = $form = new Ynjobposting_Form_Admin_Package_Edit();
		
		$package = Engine_Api::_() -> getItem('ynjobposting_package', $this->_getParam('id'));
		$form -> populate($package->toArray());
		
		$auth = Engine_Api::_() -> authorization() -> context;
		$allowed = array();
		// populate permission view package 
		if ($auth -> isAllowed($package, 'everyone', 'view')) {

		} else {
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
			foreach ($levels as $level) {
				if (Engine_Api::_() -> authorization() -> context -> isAllowed($package, $level, 'view')) {
					$allowed[] = $level -> getIdentity();
				}
			}
			if (count($allowed) == 0 || count($allowed) == count($levels)) {
				$allowed = null;
			}
		}
		
		if (!empty($allowed)) {
			$form -> populate(array('levels' => $allowed, ));
		}
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();
	
		try
		{
		  $values = $form->getValues();
		  $package->title = $values['title'];
		  $package->price = $values['price'];
		  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		  $package->valid_amount = $values['valid_amount'];
		  $package->valid_period = 'day';
		  $package->description = $values['description'];
		  $package->show = $values['show'];
		  $package->save();
		  
		  $db->commit();
		  
		  // Handle permissions
			$levels = Engine_Api::_() -> getDbtable('levels', 'authorization') -> fetchAll();
	
			// Clear permissions view package by level
			$auth -> setAllowed($package, 'everyone', 'view', false);
			foreach ($levels as $level) {
				$auth -> setAllowed($package, $level, 'view', false);
			}
	
			// Add permissions view package
			if (count($values['levels']) == 0 || count($values['levels']) == count($form -> getElement('levels') -> options)) {
				$auth -> setAllowed($package, 'everyone', 'view', true);
			} else {
				foreach ($values['levels'] as $levelIdentity) {
					$level = Engine_Api::_() -> getItem('authorization_level', $levelIdentity);
					$auth -> setAllowed($package, $level, 'view', true);
				}
			}
		}
		catch( Exception $e )
		{
		  $db->rollBack();
		  throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Package edited.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}
	
	public function deleteAction()
   {
    // In smoothbox
    $this->view->form = $form = new Ynjobposting_Form_Admin_Package_Delete();
    $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $package = Engine_Api::_()->getItem('ynjobposting_package', $id);
		if($package)
		{
			$package->deleted =  1;
			$package->save();
		}	
		$db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Package deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
    }
  }
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$package = Engine_Api::_() -> getItem('ynjobposting_package', $id);
						$package -> deleted = true;
						$package -> save();
					}
					break;
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function sortAction()
  	{
		$packages = Engine_Api::_()->getItemTable('ynjobposting_package')->getPackagesPaginator($params);
	    $order = explode(',', $this->getRequest()->getParam('order'));
	    foreach( $order as $i => $item ) {
	      $package_id = substr($item, strrpos($item, '_')+1);
	      foreach( $packages as $package ) {
	        if( $package->package_id == $package_id ) {
	          $package->order = $i;
	          $package->save();
	        }
	    	}
    	}
	}

}
