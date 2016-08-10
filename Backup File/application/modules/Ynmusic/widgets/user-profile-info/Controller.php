<?php
class Ynmusic_Widget_UserProfileInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this->setNoRender();
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		// Check authorization to view album.
		if (!$subject->isViewable()) {
		    return $this->setNoRender();
		}
		
		if ($subject instanceof User_Model_User) {
			$owner = $subject;
		}
		else {
			$owner = $subject->getOwner();
			if (!$owner || (!$owner instanceof User_Model_User)) {
				$owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id', 0));
			}
		}
		if (!$owner || (!$owner instanceof User_Model_User)) {
			$this->setNoRender();
		}
		$this->view->owner = $owner;
	}
}
