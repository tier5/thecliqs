<?php
class Mp3music_Widget_StatisticsMusicController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
   {
        $model = new Mp3music_Model_Album(array());
        $this->view->count_albums = $model->getCountAlbums();
        $this->view->count_songs = $model->getCountSongs();
        $obj = new Mp3music_Api_Core(array());
        $this->view->count_artists = count($obj->getArtistRows());
        $model = new Mp3music_Model_Playlist(array());
        $this->view->count_playlists = $model->getCountPlaylists();
   }
}