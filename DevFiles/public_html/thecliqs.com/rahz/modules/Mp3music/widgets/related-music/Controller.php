<?php
class Mp3music_Widget_RelatedMusicController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	if($this->_getParam('max') != ''){       
			$this->view->limit = $this->_getParam('max');
			if ($this->view->limit <=0)
			{
				$this->view->limit = 5;
			}
		}else{
		$this->view->limit = 5; }
     $album = Engine_Api::_()->core()->getSubject();
     if($album->getType() == 'mp3music_album_song')
     {
           $album = Engine_Api::_()->getItem('mp3music_album', $album->album_id);    
     }
     $this->view->owner_id = $album->user_id;
  }
}