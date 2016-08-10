<?php
class Ynmusic_Widget_SongProfileInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$this -> view -> song = $subject = Engine_Api::_() -> core() -> getSubject();
		// Check authorization to view song.
		if (!$subject->isViewable()) {
		    return $this -> setNoRender();
		}
	}
}
