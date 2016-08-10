<?php
class Ynsocialads_AdminModulesController extends Core_Controller_Action_Admin
{

	public function init()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
     ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_modules');
	}

	public function indexAction()
	{
		$this->view->form = $form = new Ynsocialads_Form_Admin_Module_Search();
		$form->isValid($this->_getAllParams());
	    $params = $form->getValues();
	    $this->view->formValues = $params;
	    $page = $this->_getParam('page',1);
	    $this->view->paginator = Engine_Api::_()->getItemTable('ynsocialads_module')->getModulesPaginator($params);
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
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
            $module = Engine_Api::_()->getItem('ynsocialads_module', $id);
            if( $module ) $module->delete();
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
	
	public function createAction()
	{
		// Get form
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Module_Create();
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Save
		 $values = $form->getValues();
	     $module = Engine_Api::_() -> getDbtable('modules', 'ynsocialads') -> createRow();
		
		 //get title module
		 	$module_core_table = Engine_Api::_() -> getDbtable('modules', 'core');
			$module_core_select = $module_core_table->select()->where('name = ?', $values['module_name'] ) -> limit(1);
		 	$module_core = $module_core_table -> fetchRow($module_core_select);
			if($module_core){
		 		$module-> module_title  = $values['module_title'];
			}	
			else {
				$form->addError("Can not find module name.");
				return;
			}
		 $module-> module_name = strtolower($values['module_name']);	
		 $module-> table_item = $values['table_item'];
		 $module-> title_field = $values['title_field'];
		 $module-> body_field = $values['body_field'];
		 $module-> owner_field = $values['owner_field'];
	     $module->save();
		 
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Module Added.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function editAction()
	{
		$module = Engine_Api::_() -> getItem('ynsocialads_module', $this-> _getParam('id', 0));	
		// Get form
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Module_Edit();
		$form->populate($module->toArray());
		
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Save
		 $values = $form->getValues();
		 //get title module
		 	$module_core_table = Engine_Api::_() -> getDbtable('modules', 'core');
			$module_core_select = $module_core_table->select()->where('name = ?', $values['module_name'] ) -> limit(1);
		 	$module_core = $module_core_table -> fetchRow($module_core_select);
			if($module_core){
		 		$module-> module_title  = $values['module_title'];
			}	
			else {
				$form->addError("Can not find module name.");
				return;
			}
		 $module-> module_name = strtolower($values['module_name']);	
		 $module-> table_item = $values['table_item'];
		 $module-> title_field = $values['title_field'];
		 $module-> body_field = $values['body_field'];
		 $module-> owner_field = $values['owner_field'];
	     $module->save();
		 
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Module Edited.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function deleteAction()
   {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
	$this->view->module_id=$id;
    // Check post
    if( $this->getRequest()->isPost() )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $module = Engine_Api::_()->getItem('ynsocialads_module', $id);
        // delete the book entry into the database
        $module->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-modules/delete.tpl');
  }
}
