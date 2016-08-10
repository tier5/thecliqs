<?php

class Ynmediaimporter_TwitpicController extends Core_Controller_Action_User
{
    protected $_serviceName = 'twitpic';
    
    public function indexAction()
    {
        $provider = Ynmediaimporter::getProvider($this -> _serviceName);

    	if(Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynmediaimporter.twipic.enable',1) == 0){
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
