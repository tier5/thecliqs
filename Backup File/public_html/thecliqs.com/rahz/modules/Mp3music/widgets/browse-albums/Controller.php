<?php
class Mp3music_Widget_BrowseAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth') -> setAuthParams('mp3music_album', null, 'view') -> isValid())
			return;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params = $request -> getParams();
		$params['sort'] = 'recent';
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.songsPerPage', 10);
		$params['limit'] = $limit;
		$obj = new Mp3music_Api_Core( array());
		$this -> view -> albumPaginator = $albums = $obj -> getAlbumPaginator($params);
		$this -> view -> newAlbumPaginator = $newalbum = $obj -> getNewAlbumPaginator($params);
		$this -> view -> search = $params['search'];
		$this -> view -> title = $params['title'];
		$this -> view -> params = $params;
	}

}
