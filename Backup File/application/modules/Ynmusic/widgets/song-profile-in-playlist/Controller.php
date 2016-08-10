<?php
class Ynmusic_Widget_SongProfileInPlaylistController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		// Check authorization to view album.
		if (!$subject->isViewable()) {
		    return $this -> setNoRender();
		}
		$limit = $this ->_getParam('itemCountPerPage', 3);
		$this -> view -> playlistIds = $playlistIds = $subject -> getPlaylistIds($limit);
		if(!count($playlistIds)) {
			return $this -> setNoRender();
		}
	}
}
