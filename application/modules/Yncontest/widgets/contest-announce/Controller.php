<?php
class Yncontest_Widget_ContestAnnounceController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {
  	$this->getElement()->removeDecorator('Title');
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
	
    // Get subject and check auth
    $contest = Engine_Api::_()->core()->getSubject();
  
  	
  	$this->view->contest = $contest;  	
  	$this->view->viewer = $viewer;
  	
  	$announcement = Engine_Api::_()->getDbtable('announcements', 'yncontest')->getAnnouncementByContestId($contest->contest_id);
  	if(count($announcement)==0)
  		return $this->setNoRender();
    $this->view->announcement = $announcement;
  	
	
  	
  	
  	
    
  }

  
}