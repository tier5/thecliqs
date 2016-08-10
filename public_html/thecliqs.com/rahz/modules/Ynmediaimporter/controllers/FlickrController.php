<?php

class Ynmediaimporter_FlickrController extends Core_Controller_Action_Standard
{
    protected $_serviceName = 'flickr';

    public function indexAction()
    {
        $provider = Ynmediaimporter::getProvider($this -> _serviceName);
		
    	if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.flickr.enable',1) == 0){
            $this->_redirectCustom(array('route' => 'ynmediaimporter_extended'));
            exit;
        }
        
        if (!$provider -> isAlive())
        {
            $this -> _helper -> redirector -> gotoUrl($provider -> getConnectUrl(), array('prependBase' => 0));
            exit ;
        }

        // Render
        $this -> _helper -> content
        // ->       setNoRender()
        -> setEnabled();

    }

}
