<?php
class Yncontest_Widget_ProfileOrganizerController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {

  
  	$request = Zend_Controller_Front::getInstance() -> getRequest();
  	$this->getElement()->removeDecorator('Title');
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$viewer = Engine_Api::_()->user()->getViewer();
  	if( !Engine_Api::_()->core()->hasSubject() ) {
  		return $this->setNoRender();
  	}
  	 
  	// Get subject and check auth
  		$this->view->contest = $contest = Engine_Api::_()->core()->getSubject();
  	
  	
  	
  	
  	$table = Engine_Api::_()->getDbTable('members','yncontest');
  	$select = $table->select()
  	->where('contest_id =?', $contest->contest_id)
  	->where('member_status = ?', 'approved')
  	->where('member_type =2');
  	
  	 
  	$paginator = Zend_Paginator::factory($select);
  	
 	if(count($paginator)==0) return $this->setNoRender();
  	
  	// Set item count per page and current page number
  	$paginator->setItemCountPerPage($request->getParam('itemCountPerPage', 20));
  	$paginator->setCurrentPageNumber($request->getParam('page', 1));
  	 
  	
  	 $this->view->paginator  = $paginator;

  
  	
  	
    
  }

  
}