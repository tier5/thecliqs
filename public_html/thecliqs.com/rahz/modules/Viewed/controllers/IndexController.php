<?php

class Viewed_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  	if( !$this->_helper->requireUser()->isValid() ) return;
  	$log = Zend_Registry::get('Zend_Log');
  	
  	// get user
  	$this->view->user=$viewer = Engine_Api::_()->user()->getViewer();
  	$user_id = $viewer->getIdentity();
  	$user_level = $viewer->level_id;
  	$member_Exits = Engine_Api::_()->getApi('core','viewed')->subscriptionStatus($user_level,$user_id);
  	if(!$member_Exits)
  	{
  		return $this->_helper->redirector->gotoRoute(array('module'=>'members','controller'=>'home'));
  	}
  	  	// get viewed members
  	$membersView= Engine_Api::_()->getApi('core','viewed')->getWhoViewedMeAll($user_id);
  	//$this->view->paginator = $membersView;
  	$this->view->totalUsers = count($membersView);
  	
  	// Make paginator
  	$page = $this->_getParam('page',1);
  	$this->view->paginator =$paginator= Zend_Paginator::factory($membersView);
  	$paginator->setItemCountPerPage(20);
  	$paginator->setCurrentPageNumber($page);
  	
  }
}
