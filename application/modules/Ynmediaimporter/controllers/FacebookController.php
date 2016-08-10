<?php

class Ynmediaimporter_FacebookController extends Core_Controller_Action_Standard
{
    protected $_serviceName = 'facebook';

    public function init()
    {
		
    }
    
    public function indexAction()
    {
        $provider = Ynmediaimporter::getProvider($this -> _serviceName);

    	if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.facebook.enable',1) == 0){
            $this->_redirectCustom(array('route' => 'ynmediaimporter_extended'));
            exit;
        }
        
        if (!$provider -> isAlive())
        {
            $this -> _helper -> redirector -> gotoUrl($provider -> getConnectUrl(), array('prependBase' => 0));
            exit ;
        }

		if(!Engine_Api::_() -> getApi('Core', 'Ynmediaimporter')->checkSocialBridgePlugin())
		{
			return;
		}
		else {
			// Render
	        $this -> _helper -> content
	        // ->       setNoRender()
	        -> setEnabled();
		}
    }

}
