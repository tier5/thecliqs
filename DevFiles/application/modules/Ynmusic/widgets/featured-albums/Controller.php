<?php
class Ynmusic_Widget_FeaturedAlbumsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$albums = Engine_Api::_()->getItemTable('ynmusic_album')->getFeaturedAlbums();
		$availableAlbums = array();
		foreach ($albums as $album) {
			if ($album->isViewable()) {
				$availableAlbums[]= $album;
			}
		}
		
		if (empty($availableAlbums)) return $this->setNoRender();
		$this->view->albums = $availableAlbums;
	}
}