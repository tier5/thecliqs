<?php
class Yncontest_Widget_ProfileSettingsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$contest = Engine_Api::_()->core()->getSubject();
		
		// Only admin, owner and organizer can see the contest settings
		if (!($viewer->isOwner($contest) 
			|| $viewer->isAdminOnly() 
			|| $contest->getOrganizerList()->has($viewer))) {
			return $this->setNoRender();
		}
		
		$this->view->settings = $settings = 
			Engine_Api::_() -> getDbTable('settings', 'yncontest')-> getSettingByContest($contest->getIdentity());
	}
}