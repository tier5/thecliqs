<?php
class Mp3music_Widget_ArtistsMusicController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if($this->_getParam('max') != ''){       
            $limit = $this->_getParam('max');
            if ($limit <=0)
            {
                $limit = 5;
            }
        }else{
        $limit = 5; }
        $obj = new Mp3music_Api_Core(array());
        $artists = $obj->getArtistRows($limit);
        $this->view->artists = $artists;
    }
}