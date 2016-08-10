<?php

class Ynmusic_Widget_MusicPlayerController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
    	$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
    	if(Engine_Api::_()->core()->hasSubject()) {
	        $subject = Engine_Api::_()->core()->getSubject();
			if (!$subject->isViewable()) {
				$this->setNoRender();
				return ;
			}
			switch ($subject->getType()) {
				case 'ynmusic_song':
					$this->view->song = $subject;
					if (!empty($params['playlist'])) {
						$ids = explode(',', $params['playlist']);
						$this->view->songs = Engine_Api::_()->getItemTable('ynmusic_song')->getAvalableSongs($ids);
					}
					else $this->view->songs = array($subject);
					if (!empty($params['play'])) {
						$subject->play_count++;
						$subject->save();
					}
					break;
					
				case 'ynmusic_album':
				case 'ynmusic_playlist':
					$this->view->subject = $subject;
					if (empty($params['song'])) {
						if (!$subject->getFirstSong()) {
							return $this->setNoRender();
						}
						$this->view->song = $subject->getFirstSong();
					}
					else {
						$this->view->song = Engine_Api::_()->getItemByGuid($params['song']);
					}
					$this->view->songs = $subject->getAvailableSongs();
					if (!empty($params['play'])) {
						$subject->play_count++;
						$this->view->song->play_count++;
						$subject->save();
						$this->view->song->save();
					}
					break;
					
			}
			
			if(!count($this->view->songs)) {
				$this->setNoRender();
			}
			
			$this->view->play =  (!empty($params['play'])) ? true : false;
			
			$viewer = Engine_Api::_()->user()->getViewer();
			if (!empty($params['play']) && $viewer->getIdentity() && in_array($subject->getType(), array('ynmusic_song', 'ynmusic_album', 'ynmusic_playlist'))) {
				Engine_Api::_()->getDbTable('history', 'ynmusic')->updateItem($viewer, $subject);
			}
    	}
		else {
			return $this->setNoRender();
		}
    }
}
