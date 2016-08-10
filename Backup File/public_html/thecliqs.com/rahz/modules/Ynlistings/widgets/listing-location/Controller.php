<?php
class Ynlistings_Widget_ListingLocationController extends Engine_Content_Widget_Abstract
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
		if(($subject -> longitude == 0) && ($subject -> latitude == 0))
		{
			if(!$viewer->isSelf($subject->getOwner()))
			{
				return $this -> setNoRender();
			}
		}
	}
}
?>
