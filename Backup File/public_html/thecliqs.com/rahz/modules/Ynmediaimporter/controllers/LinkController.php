<?php

class Ynmediaimporter_LinkController extends Core_Controller_Action_User
{
    protected $_serviceName = 'link';

    public function photosAction()
    {
        $provider = Ynmediaimporter::getProvider($this -> _serviceName);
        $result = $provider -> getPhotos();
        var_dump($result);

    }

    public function albumsAction()
    {
        $provider = Ynmediaimporter::getProvider($this -> _serviceName);
        $result = $provider -> getAlbums();
        var_dump($result);

    }

}
