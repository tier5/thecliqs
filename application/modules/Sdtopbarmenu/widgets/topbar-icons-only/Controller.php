<?php
class Sdtopbarmenu_Widget_TopbarIconsOnlyController extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
	
	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	
	if( $viewer->getIdentity() )
	{
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');	
	  $this->view->notificationCount = count($notificationsTable->notificationsOnlys());	
	  $this->view->requestsCount = count($notificationsTable->friendrequestOnlys());
	  $this->view->messageCount = count($notificationsTable->messageOnlys());
	}
  }
}