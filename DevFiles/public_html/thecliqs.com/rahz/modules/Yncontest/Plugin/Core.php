<?php

class Yncontest_Plugin_Core{
	
	public function onItemUpdateAfter($event)
	{
		$payload = $event->getPayload();

		$request = Zend_Controller_Front::getInstance()->getRequest();
		if(!$request)
			return;
		$view  = Zend_Registry::get('Zend_View');
		$contest_id = $request->getParam("contest_id", null);
		
		if($contest_id){
			$type = $payload->getType();
			$flag = false;
			switch ($type) {
				case 'video':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.manage';
					$flag = true;
					break;
				case 'ynvideo':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.manage';
					$flag = true;
					break;
				case 'album_photo':
					//TODO add more image
					$key = 'predispatch_url:'.$request->getParam('module').'.album.editphotos';
					$flag = true;
					break;
				case 'advalbum_photo':
					//TODO add more image
					$key = 'predispatch_url:'.$request->getParam('module').'.album.editphotos';
					$flag = true;
					break;
			}
			if($flag){
				$value = $view->url(array('action' => 'view', 'contestId'=>$contest_id, 'submit'=>1), 'yncontest_mycontest', true);
				$_SESSION[$key]= $value;
			}
		}
	}
	
	public function onItemCreateAfter($event)
	{
		$payload = $event->getPayload();

		$request = Zend_Controller_Front::getInstance()->getRequest();
		if(!$request)
			return;
		$view  = Zend_Registry::get('Zend_View');
		$contest_id = $request->getParam("contest_id", null);
		
		if($contest_id){
			$type = $payload->getType();		
			$flag = false;
			switch ($type) {
				case 'video':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.view';
					$flag = true;
					break;
				case 'ynvideo':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.view';
					$flag = true;
					break;
				case 'blog':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.manage';
					$flag = true;
					break;
				case 'ynblog':
					$key = 'predispatch_url:'.$request->getParam('module').'.index.manage';
					$flag = true;
					break;
				case 'album':
					$key = 'predispatch_url:'.$request->getParam('module').'.album.editphotos';
					$flag = true;
					break;
				case 'advalbum_album':
					$key = 'predispatch_url:'.$request->getParam('module').'.album.editphotos';
					$flag = true;
					break;
				case 'music_playlist':
					$key = 'predispatch_url:'.$request->getParam('module').'.playlist.view';
					$flag = true;
					break;
				case 'mp3music_album':
					$key = 'predispatch_url:'.$request->getParam('module').'.album.edit';					
					$flag = true;
					break;
				case 'ynmusic_song':
					if($payload->album_id != 0) {
						$key = 'predispatch_url:'.$request->getParam('module').'.albums.edit';
					} else {
						$key = 'predispatch_url:'.$request->getParam('module').'.songs.manage';
					}
					$flag = true;
					break;
				case 'ynultimatevideo_video':
						$key = 'predispatch_url:'.$request->getParam('module').'.index.view';
					$flag = true;
					break;
			}
			if($flag){
				$value = $view->url(array('action' => 'view', 'contestId'=>$contest_id, 'submit'=>1), 'yncontest_mycontest', true);
				$_SESSION[$key]= $value;
			}
		}
	}
}
