<?php
class Yncredit_AdminManageFaqController extends Core_Controller_Action_Admin
{
  public function init(){
         $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_manage_faq');
	}
	public function indexAction()
    {
		$table = Engine_Api::_()->getDbTable('faqs', 'yncredit');
		$select = $table->select();
		$select->order('ordering asc');
		$paginator = $this->view->paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
	}
	
	public function editAction()
	{
	    $this -> view -> id = $id = $this->_getParam('id', 0);
		$table = Engine_Api::_()->getDbTable('faqs', 'yncredit');
	    $faq = $table -> findRow($id);
		if(!$faq)
		{
			return;
		}
		$this->view->form = $form = new Yncredit_Form_Admin_Faq_Edit();
		$form -> populate($faq -> toArray());
		if (!$this->getRequest()->isPost()) {
	      return ;
	    }
	
	    $values = $this->getRequest()->getPost();
	
	    if (!$form -> isValid($values)) 
	    {
	      	return;
	    }
	
	    $db = $table->getAdapter();
	    $db->beginTransaction();
	
	    try 
	    {
	      $faq->setFromArray($values);
	      $faq->save();
	      $db->commit();
			$this -> _helper -> redirector -> gotoRoute(array('module'=>'yncredit','controller'=>'manage-faq', 'action'=>'index'), 'admin_default', TRUE);
	    } 
	    catch(Exception $e) 
	    {
	      $db->rollBack();
		  $form -> addError(Zend_Registry::get('Zend_Translate') -> _('Error.'));
	      throw $e;
	    }
	}
	
	public function deleteAction()
	{
		// In smoothbox
	    $this->_helper->layout->setLayout('admin-simple');
	    $this -> view -> id = $id = $this->_getParam('id', 0);
		
	    // Check post
	    if( $this->getRequest()->isPost() )
	    {
	    	$table = Engine_Api::_()->getDbTable('faqs', 'yncredit');
	    	$faq = $table -> findRow($id);
			$faq -> delete();
			// Refresh parent page
		    $this->_forward('success', 'utility', 'core', array(
		          'smoothboxClose' => 10,
		          'parentRefresh'=> 10,
		          'messages' => array('')
		      ));
		}
	    // Output
	    $this->renderScript('admin-manage-faq/delete.tpl');
	}
	
	public function createAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this->view->form = $form = new Yncredit_Form_Admin_Faq_Create();
		$table = Engine_Api::_()->getDbTable('faqs', 'yncredit');
		if (!$this->getRequest()->isPost()) {
	      return ;
	    }
	
	    $values = $this->getRequest()->getPost();
	
	    if (!$form -> isValid($values)) 
	    {
	      	return;
	    }
	
	    $db = $table->getAdapter();
	    $db->beginTransaction();
	
	    try {
	      $row = $table->createRow();
	      $row->setFromArray($values);
	      $row->save();
	      $db->commit();
			$this -> _helper -> redirector -> gotoRoute(array('module'=>'yncredit','controller'=>'manage-faq', 'action'=>'index'), 'admin_default', TRUE);
	    } catch(Exception $e) {
	      $db->rollBack();
		  $form -> addError(Zend_Registry::get('Zend_Translate') -> _('Error.'));
	      throw $e;
	    }
	}
}