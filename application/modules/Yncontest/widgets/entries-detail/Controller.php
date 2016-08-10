<?php
class Yncontest_Widget_EntriesDetailController extends Engine_Content_Widget_Abstract {
	
	public function indexAction()
  	{	
		$request = Zend_Controller_Front::getInstance() -> getRequest();	
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !Engine_Api::_()->core()->hasSubject() ) {
		  		return $this->setNoRender();
		  	}  	
	
	  	$this->view->entry = $entry =  Engine_Api::_()->core()->getSubject();  	
	  
		$this->view->contest = $contest =  Engine_Api::_()->getItem('contest', $entry->contest_id);
		
		
		$this->view->flag= Engine_Api::_()->yncontest()->checkRule(array(
				'contestId'=>$entry->contest_id,
				'key' => 'voteentries',
		));
		
	    $this->view->viewer = Engine_Api::_()->user()->getViewer();
		$this->view->organizerList = $contest->getOrganizerList();
		
	    $this->view ->award = Engine_Api::_()->getDbTable('awards','yncontest')->find($entry->award_id)->current();
	    
	    $this->view->member = Engine_Api::_()->getItemTable('yncontest_members')->getMemberContest2(array(
	    		'contestId'=>$entry->contest_id,
	    		'user_id'=> $entry->user_id));
  	}
}