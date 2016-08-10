<?php

class Ynauction_AdminHelpsController extends Core_Controller_Action_Admin{
	
	public function init(){
         $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_helps');
	}
	
	public function indexAction(){
		$Model = new Ynauction_Model_DbTable_HelpPages;
		
		
		$select = $Model->select();
		$select->order('ordering asc');
		
		$paginator = $this->view->paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
	}
	
	public function editAction(){
		$form =  $this->view->form =  new Ynauction_Form_HelpPage_Admin_Edit;
		
		$Model = new Ynauction_Model_DbTable_HelpPages;
		
		$id = $this->_getParam('id',0);
		
		$item = $Model->find($id)->current();
		
		if(!is_object($item)){
			return $this->_redirect('/admin/ynauction/helps/create');
		}
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			
			$form->populate($item->toArray());
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/ynauction/helps');
		}
	}
	
	public function deleteAction(){
		
		$Model = new Ynauction_Model_DbTable_HelpPages;
		
		$id = $this->_getParam('id',0);
        
        $this->view->id = $id; 
		
		$item = $Model->find($id)->current();
		
		$req = $this->getRequest();
		
		if($req->isPost()){
			$item->delete();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array($this->view->translate('Deleted Successfully.'))));
		}
	}
	
	public function createAction(){
		$form =  $this->view->form =  new Ynauction_Form_HelpPage_Admin_Create;
		
		$Model = new Ynauction_Model_DbTable_HelpPages;
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item = $Model->fetchNew();
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/ynauction/helps');
		}
	}
}
