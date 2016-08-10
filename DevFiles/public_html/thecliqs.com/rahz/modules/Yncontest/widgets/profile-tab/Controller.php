<?php

class Yncontest_Widget_ProfileTabController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Yncontest/externals/scripts/TabContent.js');
        // Don't render this if not authorized
	   
	    $request = Zend_Controller_Front::getInstance() -> getRequest();
		//init param
		$this->view->widgettype = 'entryviewer';
		$viewer = Engine_Api::_()->user()->getViewer();
		//get object contest
		$contestId = $request->getParam('contestId');		
		if(empty($contestId))
		{			
			return $this->setNoRender();
		}
		$this->view->contest = $contest = Engine_Api::_()->getItem('contest', $contestId);
		
		//get configure from back-end
	    $entryType = $contest->contest_type;
		
		$this->view->height = (int)$this -> _getParam('height'.$entryType,142);
		$this->view->width = (int)$this -> _getParam('width'.$entryType,245);
		$this->view->items_per_page = $items_per_page = (int)$this -> _getParam('max'.$entryType,12);
		$this->view->tab = (int)$this -> _getParam('tab',0);
	    
	    $this->getElement()->removeDecorator('Title');
	    // Get subject and check auth
	  	
	  	
	  	//get list entries
	  	$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');	  	
	  	$select = $table -> select() -> where('contest_id =?', $contest->contest_id) -> where("entry_status = 'published' or entry_status = 'win'") -> where("approve_status = 'approved'") ;	  	
	  	$order = $request->getParam('order', 'start_date');
		$select-> order("start_date DESC");	  		
	  	//echo $select;die;
	  	$results = $table->fetchAll($select);
	  	
	  	$this->view->t_entries = count($results);
	  	$this->view->paginator = Zend_Paginator::factory($results);
	  	
	  	$this->view->paginator -> setItemCountPerPage($items_per_page);
	  	$this->view->paginator -> setCurrentPageNumber($request->getParam('page'.$this->view->widgettype, 1));
		//$this->view->form = $form = new Yncontest_Form_Entries_SearchEntry;
   
		// get pending entries
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');		
		$select = $table -> select() -> where('contest_id =?', $contest->contest_id)  -> where("approve_status = 'pending'") -> order('start_date');
		$results = $table->fetchAll($select);		
		$this->view->t_pendingentries = count($results);
		$this -> view -> entries = Zend_Paginator::factory($results);	
		$this -> view -> entries -> setItemCountPerPage($items_per_page);
		$this -> view -> entries -> setCurrentPageNumber($request->getParam('page'.$this->view->widgettype, 1));
		
		// denied entries		
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$select = $table -> select() -> where('contest_id =?', $contest->contest_id)  -> where("approve_status = 'denied'") -> order('start_date');
		$results = $table->fetchAll($select);		
		$this->view->t_deniedentries = count($results);
		$this -> view -> entrydenied = Zend_Paginator::factory($results);
		$this -> view -> entrydenied -> setItemCountPerPage($items_per_page);
		$this -> view -> entrydenied -> setCurrentPageNumber($request->getParam('page'.$this->view->widgettype, 1));	
		//echo 1;die;	
    }

}
