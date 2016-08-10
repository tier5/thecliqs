<?php

class Ynauction_HelpController extends Core_Controller_Action_Standard {
	
	public function init() {
		// private page
		//if(!$this -> _helper -> requireUser() -> isValid()) {
		//	return ;
		//}
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynauction_main', array(), 'ynauction_main_helps');
	}

	public function indexAction() {
		$this->_forward('detail');
	}
	
	public function detailAction(){
		$this -> _helper -> content -> setEnabled();
		$id =  $this->_getParam('id',0);
		$table = new Ynauction_Model_DbTable_HelpPages;
		$item =  $table->find($id)->current();
		
		if(!is_object($item)){
			$select = $table->select()->where('status=?','show')->order('ordering asc');
			$item = $table->fetchRow($select);
		}
		
		if(!is_object($item)){
			return $this->_forward('empty');
		}
		
		$this->view->item = $item;
		
		Zend_Registry::set('ACTIVE_HELP_PAGE', $item->getIdentity());
	}
	
	public function emptyAction(){
		
	}
}
