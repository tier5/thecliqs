<?php
class Sdtopbarmenu_Widget_TopbarMiniMenuOnlyController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;
	
	if( $viewer->getIdentity() )
	{
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');	
	  $this->view->notificationCount = count($notificationsTable->notificationsOnlys());	
	  $this->view->requestsCount = count($notificationsTable->friendrequestOnlys());
	  $this->view->messageCount = count($notificationsTable->messageOnlys());
	}

    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');
  }
}