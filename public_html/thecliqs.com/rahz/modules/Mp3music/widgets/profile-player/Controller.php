<?php
class Mp3music_Widget_ProfilePlayerController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Get playlist
    $obj = new Mp3music_Api_Core(array());
    $select   = $obj->getPlaylistSelect(array('user'=>$subject->getIdentity()))->where('profile = 1');
    $playlist = Engine_Api::_()->getDbtable('playlists', 'mp3music')->fetchRow($select);
    // No playlist registered
    if( !$playlist ) {
      return $this->setNoRender();
    }

	$songs = Engine_Api::_()->mp3music()->getservicesongs($playlist);
    
    $this->view->songs = $songs;
	
	if( count($songs) <= 0 ) {
      return $this->setNoRender();
    }
	
    $this->getElement()->setTitle($playlist->getTitle());

    // Assign
    $this->view->playlist = $playlist;
  }
}