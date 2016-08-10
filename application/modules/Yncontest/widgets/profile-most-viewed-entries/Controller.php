<?php
class Yncontest_Widget_ProfileMostViewedEntriesController extends Engine_Content_Widget_Abstract
{
	  public function indexAction()
	  {
	  	
	  	$viewer = Engine_Api::_()->user()->getViewer();
	  	if( !Engine_Api::_()->core()->hasSubject() ) {
	  		return $this->setNoRender();
	  	}
	  	
	  	$request = Zend_Controller_Front::getInstance() -> getRequest();
	  	
	  	// Get subject and check auth
	  	$contest = Engine_Api::_()->core()->getSubject();	  	
	  	$this->view->contest = $contest;
	  	$items_per_page = $this->_getParam('number',5);
		$page = $request->getParam("page",1);
		
	  	//view entries
	  	$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
	  	$select = $table -> select() 
	  		//-> where('contest_id =?', $contest->contest_id) 
	  		//-> where("entry_status = 'published' or entry_status = 'win'") 
	  		-> where("approve_status = 'approved'")
	  		-> order('view_count DESC') ;
	  	
		$results = $table->fetchAll($select);
	  	$this->view->paginator = Zend_Paginator::factory($results);
	  	$this->view->paginator -> setItemCountPerPage($items_per_page);
	  	$this->view->paginator -> setCurrentPageNumber($page);
	  }
}