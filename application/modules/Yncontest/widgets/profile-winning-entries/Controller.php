<?php
class Yncontest_Widget_ProfileWinningEntriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{		
		$this->getElement()->removeDecorator('Title');
		$viewer = Engine_Api::_()->user()->getViewer();

		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}
		$this->view->contest = $contest = Engine_Api::_()->core()->getSubject();

		if(!$contest->IsOwner($viewer))
			if($contest->contest_status != 'close')
			return $this->setNoRender();
		
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if($contest->contest_type == 'advalbum' || $contest->contest_type == 'ynvideo')
		{
			$this->view->height = (int)$this -> _getParam('heightadvalbum',160);
			$this->view->width = (int)$this -> _getParam('widthadvalbum',155);
		}
		else{
			$this->view->height = (int)$this -> _getParam('heightynblog',250);
			$this->view->width = (int)$this -> _getParam('widthynblog',60);
		}
		
		//init table 
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');		
		
		//entries win by vote		
		
		if($contest->award_number >0){
					
			$select = $table -> select() -> where('contest_id = ?', $contest->contest_id) -> where("entry_status = 'published' or entry_status = 'win'") -> where("approve_status = 'approved'");
			$select -> where('vote_count > 0');
			$select-> order('vote_count DESC') ->limit($contest->award_number);
			$results = $table->fetchAll($select);			
			
			$this->view->viewvote = Zend_Paginator::factory($results);			
			//$this->view->viewvote -> setItemCountPerPage($limitvote);
			$this->view->viewvote -> setCurrentPageNumber($request->getParam('page1', 1));
		}
		
		//entries win by owner		
		//$limitowner = (int)$this->_getParam('limitowner',6);		

		$select2 = $table -> select() -> where('contest_id =?', $contest->contest_id) -> where("waiting_win = 1") -> where("approve_status = 'approved'")->order('start_date');
		$results2 = $table->fetchAll($select2);		
			
		$this->view->viewowner = Zend_Paginator::factory($results2);
		//$this->view->viewowner -> setItemCountPerPage($limitowner);
		$this->view->viewowner -> setCurrentPageNumber($request->getParam('page2', 1));		
		
	
	}


}