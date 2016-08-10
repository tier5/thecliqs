<?php
class Ynsocialads_AdminPackagesController extends Core_Controller_Action_Admin
{

	public function init()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
     ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_packages');
	}

	public function indexAction()
	{
		$this->view->form = $form = new Ynsocialads_Form_Admin_Packages_Search();
		$form->isValid($this->_getAllParams());
	    $params = $form->getValues();
	    $this->view->formValues = $params;
	    $this -> view -> page = $page = $this->_getParam('page',1);
	    $this->view->paginator = Engine_Api::_()->getItemTable('ynsocialads_package')->getPackagesPaginator($params);
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	}
	
	public function createAction()
	{
		$this->view->form = $form = new Ynsocialads_Form_Admin_Packages_Create();
		$arr_modules = Engine_Api::_() -> getItemTable('ynsocialads_module') -> getModules();
		$arr= array();
		foreach($arr_modules as $item)
		{
			$arr[$item->module_name] = $item->module_title;
		}
		$form -> modules -> setMultiOptions($arr);
		
		$AdBlockTable = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
		$blocks = $AdBlockTable->fetchAll($AdBlockTable -> select()->where('enable = 1')->where('deleted = 0'));
		$arr_block= array();
		foreach($blocks as $item)
		{
			$arr_block[$item->adblock_id] = $item->title;
		}
		$form->blocks->setMultiOptions($arr_block);
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$db = Engine_Api::_()->getItemTable('ynsocialads_package')->getAdapter();
    	$db->beginTransaction();
	    $viewer = Engine_Api::_() -> user() -> getViewer();
		try
		{
		  $package = Engine_Api::_()->getItemTable('ynsocialads_package')->createRow();
		  $values = $form->getValues();
		  $package->title = $values['title'];
		  $package->price = $values['price'];
		  $package->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		  $package->benefit_amount = $values['benefit_amount'];
		  $package->benefit_type = $values['benefit_type'];
		  $package->description = $values['description'];
		  $package->modules = $values['modules'];
		  $package->allowed_ad_types = $values['allowed_ad_types'];
		  $package->show = $values['show'];
          $package->user_id = $viewer->getIdentity();
		  $package->save();
		  
		  foreach ($values['blocks'] as $block_id)
		  {
		  	$packageblock = Engine_Api::_()->getItemTable('ynsocialads_packageblock') ->createRow();
			$packageblock -> package_id = $package -> getIdentity();
			$packageblock -> block_id = $block_id;
			$packageblock ->save();
		  }
		  
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
		
		$this->_helper->redirector->gotoRoute(array('module'=>'ynsocialads','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}
	
	public function editAction()
	{
		$this->view->form = $form = new Ynsocialads_Form_Admin_Packages_Edit();
		$arr_modules = Engine_Api::_() -> getItemTable('ynsocialads_module') -> getModules();
		$arr= array();
		foreach($arr_modules as $item)
		{
			$arr[$item->module_name] = $item->module_title;
		}
		$form -> modules -> setMultiOptions($arr);
		
		$AdBlockTable = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
		$blocks = $AdBlockTable->fetchAll($AdBlockTable -> select()->where('enable = 1')->where('deleted = 0'));
		$arrr_block= array();
		foreach($blocks as $item)
		{
			$arrr_block[$item->adblock_id] = $item->title;
		}
		$form->blocks->setMultiOptions($arrr_block);
		
		$packageblockTable = Engine_Api::_()->getItemTable('ynsocialads_packageblock');
		$arr_blocks = $packageblockTable->fetchAll($packageblockTable -> select() -> where ('package_id = ?',$this->_getParam('id')));
		
		$arr_block= array();
		foreach($arr_blocks as $item)
		{
			$arr_block[$item->block_id] = $item->block_id;
		}
		$form -> blocks -> setValue($arr_block);
		
		$package = Engine_Api::_() -> getItem('ynsocialads_package', $this->_getParam('id'));
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
		  $package->benefit_amount = $values['benefit_amount'];
		  $package->benefit_type = $values['benefit_type'];
		  $package->description = $values['description'];
		  $package->modules = $values['modules'];
		  $package->allowed_ad_types = $values['allowed_ad_types'];
		  $package->show = $values['show'];
		  $package->save();
		  
		  //delete all old blocks
			foreach($arr_blocks as $item)
			{
				$item->delete();
			}
		  
		  //add brand new blocks
		  foreach ($values['blocks'] as $block_id)
		  {
		  	$packageblock = Engine_Api::_()->getItemTable('ynsocialads_packageblock') ->createRow();
			$packageblock -> package_id = $package -> getIdentity();
			$packageblock -> block_id = $block_id;
			$packageblock ->save();
		  }
		  
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
		$this->_helper->redirector->gotoRoute(array('module'=>'ynsocialads','controller'=>'packages', 'action' => 'index'), 'admin_default', true);
	}

 public function deleteSelectedAction()
 {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Check post
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      //Process delete
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try{
          $ids_array = explode(",", $ids);
          foreach( $ids_array as $id ){
            $package = Engine_Api::_()->getItem('ynsocialads_package', $id);
            if( $package ) {
				 $package->deleted = 1;
				$package->save();
			}	
          }
          $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

     $this->_helper->redirector->gotoRoute(array('action' => ''));
      }
  }
	
	public function deleteAction()
   {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $package = Engine_Api::_()->getItem('ynsocialads_package', $id);
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

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 100,
          'parentRefresh'=> 100,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-packages/delete.tpl');
  }
   
   public function sortAction()
  {
  	$params['page'] = $this->getRequest()->_getParam('page', 0);
	$packages = Engine_Api::_()->getItemTable('ynsocialads_package')->getPackagesPaginator($params);
    $order = explode(',', $this->getRequest()->_getParam('order'));
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
