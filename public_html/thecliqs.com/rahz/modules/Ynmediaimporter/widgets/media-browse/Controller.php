<?php

class Ynmediaimporter_Widget_MediaBrowseController extends Engine_Content_Widget_Abstract
{

    /**
     * fetch albums from some thing else.
     * @param null
     */
    public function indexAction()
    {

        $request = Zend_Controller_Front::getInstance() -> getRequest();
        $format = $request -> getParam('format', null);

        $serviceName = $request -> getParam('service', null);
        
        if (null == $serviceName)
        {
            $serviceName = $request -> getControllerName();
        }
        
        $params = array();
        $params['limit'] = intval($request -> getParam('limit', YNMEDIAIMPORTER_PER_PAGE));
        $params['offset'] = intval($request -> getParam('offset', 0));
        $params['service'] = $serviceName;
        $params['extra'] = $request -> getParam('extra', 'my');
        
        //Load my photos first with Flickr provider.
        if ($serviceName == 'flickr')
        	$params['media'] = $media = $request -> getParam('media', 'photo');
        else 
        	$params['media'] = $media = $request -> getParam('media', 'album');
        	
        $params['aid'] = $request -> getParam('aid', 0);
        $params['cache'] = $request -> getParam('cache', 1);
        
        $cache = $request -> getParam('cache', 1);
        $this -> view -> requestParam = $params;
    }
}
