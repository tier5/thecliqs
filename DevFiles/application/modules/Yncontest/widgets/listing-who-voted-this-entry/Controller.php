<?php
class Yncontest_Widget_ListingWhoVotedThisEntryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}
		
		$this->view->entry = $entry =  Engine_Api::_()->core()->getSubject();
		$this->view->users = $users = $entry->getWhoVoted();
		if (!count($users))
			$this->setNoRender();
	}
}