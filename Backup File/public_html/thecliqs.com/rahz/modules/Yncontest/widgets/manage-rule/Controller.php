<?php
class Yncontest_Widget_ManageRuleController extends Engine_Content_Widget_Abstract {
	
	public function indexAction()
  	{
  		$table = Engine_Api::_() -> getDbTable('managerules', 'yncontest');
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();		
		$select = $table -> select()->where("user_id = ?",$user_id);
		
		$page = $request -> getParam('page', 1);
		
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
	
  	}
}