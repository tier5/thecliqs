<?php
class Mp3music_Widget_PlayerAlbumController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {   
    if(Engine_Api::_()->core()->hasSubject())
    {
        $subject = Engine_Api::_()->core()->getSubject();
        $songs = array();
				/*Is album*/
        if($subject->getType() == "mp3music_album_song")
        {
          $this->view->song = $subject;
          $album = Engine_Api::_()->getItem('mp3music_album', $subject->album_id);
          $this->view->album = $album;  
          $songs = Engine_Api::_()->mp3music()->getServiceSongs($album, $subject->getIdentity());
        }
        else
        {
          $songs = Engine_Api::_()->mp3music()->getServiceSongs($subject);
          $this->view->song = Engine_Api::_()->getItem('mp3music_album_song', $subject->getSongIDFirst());
          $this->view->album = $subject;
          $album = $subject;
        }
        Engine_Api::_()->core()->clearSubject();
        Engine_Api::_()->core()->setSubject($album);  
        $this->view->songs = $songs;
    }
    else
    {
       $this->setNoRender();
    }
  }
}