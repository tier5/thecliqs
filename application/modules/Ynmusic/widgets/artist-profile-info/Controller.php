<?php
class Ynmusic_Widget_ArtistProfileInfoController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$this -> view -> artist = $subject = Engine_Api::_() -> core() -> getSubject();
	}
}
