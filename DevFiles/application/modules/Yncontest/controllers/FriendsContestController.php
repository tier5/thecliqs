<?php

class Yncontest_FriendsContestController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','friends_contest');
		$this -> _helper -> content -> setNoRender() -> setEnabled();
		
		$viewer = Engine_Api::_()->user()->getViewer();
		//$viewer->get
	}
}
