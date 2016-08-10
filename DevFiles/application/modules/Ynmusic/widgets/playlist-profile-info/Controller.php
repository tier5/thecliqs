<?php
class Ynmusic_Widget_PlaylistProfileInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$this -> view -> playlist = $subject = Engine_Api::_() -> core() -> getSubject();
		// Check authorization to view album.
		if (!$subject->isViewable()) {
		    return $this -> setNoRender();
		}
	}
}
