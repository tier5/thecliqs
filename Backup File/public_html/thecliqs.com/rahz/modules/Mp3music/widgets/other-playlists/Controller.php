<?php
class Mp3music_Widget_OtherPlaylistsController extends Engine_Content_Widget_Abstract
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
      if($album)
        $this->view->playlist_id = $album->playlist_id;
      $model = new Mp3music_Model_Playlist(array());
      $playlists = $model->getOtherPlaylists($this->view->limit,$this->view->playlist_id);
      if( count($playlists) <= 0 ) {
                return $this->setNoRender();
                }
      $this->view->playlists = $playlists;
   }
}