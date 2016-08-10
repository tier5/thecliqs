<?php
class Ynultimatevideo_Widget_PlaylistCreateLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Must be logged-in
		if (!$viewer -> getIdentity()) {
			return $this -> setNoRender();
		}

		// @TODO implement playlist create permission
//		if (!Engine_Api::_() -> authorization() -> isAllowed('ynultimatevideo_video', $viewer, 'create')) {
//			return $this -> setNoRender();
//		}
	}
}
