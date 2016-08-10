<?php
class Ynmusic_Widget_ArtistProfileRelatedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> artists = $artists = $subject -> getRelatedArtists();
		if(!count($artists)) {
			return $this -> setNoRender();
		}
	}
}
