<?php
class Ynlistings_Widget_ListingAboutController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> setNoRender();
		}
		// Get subject and check auth
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject('ynlistings_listing');
		//check follow
		$tableFollow = Engine_Api::_()->getItemTable('ynlistings_follow');
		$row = $tableFollow -> getRow($viewer -> getIdentity(), $subject->user_id);
		if($row) 
		{
			if($row ->status == 1)
			{
				$isFollowed = true;
			}
			else {
				$isFollowed = false;
			}
		}
		else
		{
			$isFollowed = false;
		}
		$this-> view -> isFollowed = $isFollowed;
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		if($viewer -> getIdentity())
        	$this->view->can_follow  = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'follow');
        
	}
}
?>
