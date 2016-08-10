<?php
class Mp3music_Widget_PlayerPlaylistController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {     
    if(Engine_Api::_()->core()->hasSubject())
    {
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->song = Engine_Api::_()->getItem('mp3music_album_song', $subject->getSongIDFirst());
        $this->view->playlist = $subject;
        $songs = Engine_Api::_()->mp3music()->getServiceSongs($subject);
        $this->view->songs = $songs;
    }
    else
    {
       $this->setNoRender();
    } 
  }
}