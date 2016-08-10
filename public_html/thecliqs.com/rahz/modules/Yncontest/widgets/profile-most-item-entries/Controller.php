<?php
class Yncontest_Widget_ProfileMostItemEntriesController extends Engine_Content_Widget_Abstract
{
	  public function indexAction()
	  { 
	  	if( !Engine_Api::_()->core()->hasSubject() ) {
	  		return $this->setNoRender();
	  	}
		// Get subject and check auth
	  	$item = Engine_Api::_()->core()->getSubject(); 
		$this->view->contest_id = $item->contest_id;
	  	
	  	$this->view->maxItem = $maxItem = $this->_getParam('number',5);
		$this->view->type = $type = $this->_getParam('type','view_count');
	  	
	  	//view entries
	  	$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
	  	$select = $table -> select();	  	
		$select-> where('contest_id =?', $item->contest_id);	  		
	  	$select -> where("approve_status = 'approved'");
		
	  	$select	-> order("$type DESC") ;
		
		
		
		$this->view->items = $items = $table->fetchAll($select);
		
		if(count($items) == 0)
			$this -> setNoRender();
		if(count($items)>$maxItem)
			$this->view->canViewmore = true;
		
	  }
}