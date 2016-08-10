<?php

class Ynlocationbased_IndexController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        $this->view->someVar = 'someVal';
    }

    public function getMyLocationAction()
    {
        $latitude = $this->_getParam('latitude');
        $longitude = $this->_getParam('longitude');
        $values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
        echo $values;
        die;
    }
}
