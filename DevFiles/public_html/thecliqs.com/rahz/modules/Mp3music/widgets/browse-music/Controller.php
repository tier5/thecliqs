<?php
class Mp3music_Widget_BrowseMusicController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth') -> setAuthParams('mp3music_album', null, 'view') -> isValid())
			return;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params = $request -> getParams();
		if (empty($params['search']))
		{
			$params['search'] = 'songs';
			$params['title'] = '';
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.songsPerPage', 10);
		$params['limit'] = $limit;
		$obj = new Mp3music_Api_Core( array());
		$this -> view -> songPaginator = $songs = $obj -> getSongPaginator($params);
		$this -> view -> search = $params['search'];
		$this -> view -> title = $params['title'];
		$this -> view -> params = $params;
	}

}
