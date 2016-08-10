<?php
class Yncontest_Widget_EntriesDetailManageController extends Engine_Content_Widget_Abstract {
	
	public function indexAction()
  {
	
	$request = Zend_Controller_Front::getInstance() -> getRequest();
// 	if( !Engine_Api::_()->core()->hasSubject() ) {
// 		return $this->setNoRender();
// 	}
	 
// 	// Get subject and check auth
 	//$contest = Engine_Api::_()->core()->getSubject();
	
	$viewer = Engine_Api::_()->user()->getViewer();
	$params = array();
	$params['option'] = $request->getParam('option', null);	
	
	
	if( !Engine_Api::_()->core()->hasSubject() ) {
	  		return $this->setNoRender();
	  	}
  	
  	// Get subject and check auth
  	$entries = Engine_Api::_()->core()->getSubject();
  	
  
	
	$this->view->contest = Engine_Api::_()->getItem('contest', $entries->contest_id);
	
	
	
	
	$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
	$Name = $table -> info('name');
	$select = $table -> select() -> from($Name, "$Name.*") -> setIntegrityCheck(false);
	
	
	switch ($params['option']) {
		case '1': // all
				
			;
			break;
		case '2': // entries belong contest
			//$select -> where("$Name.user_id = ?", $viewer->getIdentity());
			$select -> where("$Name.contest_id = ?", $entries->contest_id);
			;
			break;
		case '3': //
			$params['contest_id'] = $contest->contest_id
			;
			break;
	}
	
	
	$select -> where("$Name.entry_status = 'published' or $Name.entry_status = 'win' ");
	
	$page = $request -> getParam('page');
	
	
	if(!isset($page)){
		$results = $table->fetchAll($select);
		foreach($results as $key => $result){
			if($result->entry_id == $entries->entry_id)
				$page = $key+1;
		}
	}
	//else $page =1;
	
	
	
	$this->view->flag= Engine_Api::_()->yncontest()->checkRule(array(
			'contestId'=>$entries->contest_id,
			'key' => 'voteentries',
	));
	
	//echo $select;die;
	
	
	
	

	$this->view->paginator = Zend_Paginator::factory($select);
	 
	$this->view->paginator -> setItemCountPerPage($request -> getParam('itemCountPerPage', 1));
	$this->view->paginator -> setCurrentPageNumber($page);	
	

	

   
  
   
				
	
	 
  }

}
