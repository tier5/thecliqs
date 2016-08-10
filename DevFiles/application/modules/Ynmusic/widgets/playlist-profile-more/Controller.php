<?php
class Ynmusic_Widget_PlaylistProfileMoreController extends Engine_Content_Widget_Abstract {
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
		if (!$limit) $limit = 3;
		$playlistTable = Engine_Api::_() -> getItemTable('ynmusic_playlist');
		$this -> view -> playlists = $playlists = $playlistTable -> getOtherByUser($subject -> getOwner(), $subject, $limit);
		if(!count($playlists)) {
			return $this -> setNoRender();
		}
	}
}
