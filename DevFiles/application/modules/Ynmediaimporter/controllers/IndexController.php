<?php

class Ynmediaimporter_IndexController extends Core_Controller_Action_Standard
{
    public function init()
    {

    }

    /**
     * connect page.
     */
    public function indexAction()
    {
        $this -> view -> iframeurl = $this -> _getParam('iframeurl', '');
        // Render
        $this -> _helper -> content
        // ->    setNoRender()
        -> setEnabled();
    }

    public function callbackAction()
    {
    	if ($_GET['error']){
    		$this->_redirectCustom(array('route' => 'ynmediaimporter_extended'));
    		exit;
    	}
        /**
         * disable layout
         */
        $this -> _helper -> layout() -> disableLayout();

        /**
         * @var string
         */
        $service = $this -> _getParam('service');

        /**
         * get provider
         * @var Ynmediaimporter_Provider_Abstract
         */
        $provider = Ynmediaimporter::getProvider($service);
		
        /**
         * process connect
         */
        $provider -> doConnect($this -> _getAllParams());

        /**
         * @var string
         */
        $action = $provider -> supportedMethod('getAlbums') ? 'albums' : 'photos';

        /**
         *
         * @var string
         */
        $url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => $service), 'ynmediaimporter_extended', 1);

        /**
         * redirect to next page.
         */
        $this -> _helper -> redirector -> gotoUrl($url, array('prependBase' => false));

    }

    public function disconnectAction()
    {
        $this -> _helper -> layout() -> disableLayout();

        $service = $this -> _getParam('service');

        $provider = Ynmediaimporter::getProvider($service);

        $provider -> doDisconnect($this -> _getAllParams());

        /**
         *
         * @var string
         */
        $url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynmediaimporter_general', 1);

        $url .= '?iframeurl=' . $provider -> getLogoutIframeUrl();

        /**
         * redirect to next page.
         */
        $this -> _helper -> redirector -> gotoUrl($url, array('prependBase' => false));

    }

    public function connectAction()
    {
        $this -> _helper -> layout() -> disableLayout();

        $service = $this -> _getParam('service');
        /**
         * get provider
         */
        $provider = Ynmediaimporter::getProvider($service);

        /**
         * get remote url,
         */
        $req = $this -> getRequest();

        $url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('service' => $service, ), 'ynmediaimporter_callback', 1);
		
        $url = $req -> getScheme() . '://' . $req -> getHttpHost() . $url;
        
        // callback url.
        $url = $provider -> getAuthUrl($url);
        $this -> _helper -> redirector -> gotoUrl($url, array('prependBase' => false));
    }

    public function sessionAction()
    {
        $this -> _helper -> layout() -> disableLayout();
        print_r($_SESSION);
    }

    public function disconnectFacebookAction()
    {

        $flag = true;
        $front = Zend_Controller_Front::getInstance();
        $request = $front -> getRequest();
        $router = $front -> getRouter();
		
		
        $provider = Ynmediaimporter::getProvider('facebook');
        
        $provider -> doDisconnect();
        $url = $router -> assemble(array(), 'ynmediaimporter_general', 1);
        header('location:' . $url);
        exit ;

    }

}
